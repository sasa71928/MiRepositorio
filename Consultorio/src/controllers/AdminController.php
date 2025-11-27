<?php
/**
 * Controlador de Administración.
 * Gestiona doctores, departamentos y reportes.
 * Integra auditoría (Logs), transacciones y Soft Delete.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Logger

function obtenerTotalDoctores(): int {
    global $pdo;
    // Contamos solo los activos para estadísticas generales
    return (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2 AND is_active = 1")->fetchColumn();
}

function obtenerTotalPacientes(): int {
    global $pdo;
    // Contamos solo los activos
    return (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3 AND is_active = 1")->fetchColumn();
}

function crearDoctor($data) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;

    try {
        $pdo->beginTransaction();

        // 1. Insertar Usuario
        $stmtUser = $pdo->prepare("
            INSERT INTO users (role_id, username, password_hash, first_name, last_name, email, phone, birthdate, gender, address, city, is_active, created_at) 
            VALUES (2, :username, :password, :first_name, :last_name, :email, :phone, :birthdate, :gender, :address, :city, 1, NOW())
        ");

        $stmtUser->execute([
            ':username'   => $data['username'],
            ':password'   => password_hash($data['password'], PASSWORD_BCRYPT),
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':email'      => $data['email'],
            ':phone'      => $data['phone'],
            ':birthdate'  => $data['birthdate'],
            ':gender'     => $data['gender'],
            ':address'    => $data['address'],
            ':city'       => $data['city'],
        ]);

        $userId = $pdo->lastInsertId();

        // 2. Insertar Doctor
        $stmtDoctor = $pdo->prepare("
            INSERT INTO doctors (user_id, cedula_profesional, department_id)
            VALUES (:user_id, :cedula, :department_id)
        ");

        $stmtDoctor->execute([
            ':user_id'       => $userId,
            ':cedula'        => $data['cedula'],
            ':department_id' => $data['department_id'],
        ]);
        
        // 3. Preferencias por defecto
        $stmtPref = $pdo->prepare("INSERT INTO user_preferences (user_id) VALUES (?)");
        $stmtPref->execute([$userId]);

        $pdo->commit();

        log_audit($adminId, 'doctor_creado', "Admin creó al Dr. {$data['first_name']} {$data['last_name']} (ID: $userId)");
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        log_audit($adminId, 'error_crear_doctor', $e->getMessage());
        return false;
    }
}

function editarDoctor($data) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;

    if (!isset($data['id'])) return false;

    try {
        $pdo->beginTransaction();
        
        $id = $data['id'];

        // Actualiza usuario
        $sqlUser = "UPDATE users SET
            username = :username, first_name = :first_name, last_name = :last_name,
            email = :email, phone = :phone, birthdate = :birthdate,
            gender = :gender, address = :address, city = :city
            " . (!empty($data['password']) ? ", password_hash = :password" : "") . "
            WHERE id = :id";
        
        $stmt = $pdo->prepare($sqlUser);
        
        $params = [
            ':username' => $data['username'], ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'], ':email' => $data['email'],
            ':phone' => $data['phone'], ':birthdate' => $data['birthdate'],
            ':gender' => $data['gender'], ':address' => $data['address'],
            ':city' => $data['city'], ':id' => $id
        ];
        
        if (!empty($data['password'])) {
            $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        
        $stmt->execute($params);

        // Actualiza doctor
        $stmt2 = $pdo->prepare("UPDATE doctors SET department_id = :dep_id, cedula_profesional = :cedula WHERE user_id = :id");
        $stmt2->execute([':dep_id' => $data['departamento_id'], ':cedula' => $data['cedula'], ':id' => $id]);

        $pdo->commit();
        
        log_audit($adminId, 'doctor_editado', "Admin editó al doctor ID: $id");
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        log_audit($adminId, 'error_editar_doctor', $e->getMessage());
        return false;
    }
}

/**
 * [NUEVO] Cambiar estado del doctor (Soft Delete / Reactivación)
 */
function cambiarEstadoDoctor($id, $nuevoEstado) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;

    try {
        $pdo->beginTransaction();
        
        // Actualizamos el estado en la tabla users (1 = Activo, 0 = Inactivo)
        $stmt = $pdo->prepare("UPDATE users SET is_active = :estado WHERE id = :id AND role_id = 2");
        $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);

        // Opcional: Si se desactiva, cancelamos citas futuras pendientes para evitar problemas
        if ($nuevoEstado == 0) {
             $stmtCitas = $pdo->prepare("
                UPDATE appointments 
                SET status = 'cancelada' 
                WHERE doctor_id = (SELECT id FROM doctors WHERE user_id = :uid) 
                AND status = 'pendiente' 
                AND scheduled_at > NOW()
             ");
             $stmtCitas->execute([':uid' => $id]);
        }

        $pdo->commit();

        $accion = $nuevoEstado ? 'doctor_reactivado' : 'doctor_suspendido';
        $estadoTexto = $nuevoEstado ? 'Activo' : 'Inactivo';
        log_audit($adminId, $accion, "El admin cambió el estado del doctor ID: $id a $estadoTexto");
        
        return true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        log_audit($adminId, 'error_cambiar_estado', $e->getMessage());
        return false;
    }
}

// --- Departamentos ---

function crearDepartamento($nombre) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;
    try {
        $stmt = $pdo->prepare("INSERT INTO departments (name) VALUES (:nombre)");
        $stmt->execute([':nombre' => $nombre]);
        log_audit($adminId, 'depto_creado', "Nuevo departamento: $nombre");
        return true;
    } catch (Exception $e) {
        log_audit($adminId, 'error_crear_depto', $e->getMessage());
        return false;
    }
}

function editarDepartamento($id, $nombre) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;
    try {
        $stmt = $pdo->prepare("UPDATE departments SET name = :nombre WHERE id = :id");
        $stmt->execute([':nombre' => $nombre, ':id' => $id]);
        log_audit($adminId, 'depto_editado', "Departamento ID $id renombrado a $nombre");
        return true;
    } catch (Exception $e) {
        log_audit($adminId, 'error_editar_depto', $e->getMessage());
        return false;
    }
}

function eliminarDepartamento($id) {
    global $pdo;
    $adminId = $_SESSION['user']['id'] ?? 0;

    try {
        // Validar si hay doctores (Integridad Referencial)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM doctors WHERE department_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            log_audit($adminId, 'error_eliminar_depto', "Intento fallido ID $id (tiene doctores)");
            return false; 
        }

        $stmt = $pdo->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        
        log_audit($adminId, 'depto_eliminado', "Departamento ID $id eliminado");
        return true;
        
    } catch (Exception $e) {
        log_audit($adminId, 'error_eliminar_depto', $e->getMessage());
        return false;
    }
}

// --- Consultas de Lectura ---

function obtenerDepartamentos() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name FROM departments");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDoctores($limite, $offset, $departamento = '', $nombre = '') {
    global $pdo;
    
    // [MEJORA] Incluimos is_active en la consulta
    $sql = "
        SELECT u.id, u.username, u.first_name, u.last_name, u.email, u.phone, 
               u.birthdate, u.gender, u.address, u.city, u.is_active,
               d.name AS departamento, doc.cedula_profesional, doc.department_id
        FROM users u
        JOIN doctors doc ON u.id = doc.user_id
        JOIN departments d ON doc.department_id = d.id
        WHERE u.role_id = 2
    ";
    
    $params = [];
    if ($departamento !== '') {
        $sql .= " AND d.name = :departamento";
        $params[':departamento'] = $departamento;
    }
    if (!empty($nombre)) {
        $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE :nombre";
        $params[':nombre'] = "%$nombre%";
    }
    
    // Aquí NO filtramos por is_active, para que el admin pueda ver a los suspendidos
    $sql .= " LIMIT :limite OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) $stmt->bindValue($key, $value);
    $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function contarDoctores($departamento = '', $nombre = '') {
    global $pdo;
    $sql = "
        SELECT COUNT(*) FROM users u
        JOIN doctors doc ON u.id = doc.user_id
        JOIN departments d ON doc.department_id = d.id
        WHERE u.role_id = 2
    ";
    $params = [];
    if ($departamento !== '') {
        $sql .= " AND d.name LIKE :departamento";
        $params[':departamento'] = $departamento;
    }
    if (!empty($nombre)) {
        $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE :nombre";
        $params[':nombre'] = "%$nombre%";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function obtenerDepartamentosConDoctores() {
    global $pdo;
    // Contamos solo los activos para la vista pública
    $stmt = $pdo->query("
        SELECT d.id, d.name, COUNT(doc.id) AS total_doctores
        FROM departments d
        LEFT JOIN doctors doc ON doc.department_id = d.id
        LEFT JOIN users u ON doc.user_id = u.id AND u.is_active = 1
        GROUP BY d.id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function contarTotalConsultas() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'completada'")->fetchColumn();
}

function contarNuevosPacientes() {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn();
}

function calcularIngresosTotales() {
    global $pdo;
    return $pdo->query("SELECT SUM(amount) FROM payments WHERE status = 'completado'")->fetchColumn() ?: 0;
}

function obtenerDoctoresMasActivos() {
    global $pdo;
    // Solo consideramos doctores activos para el ranking
    $stmt = $pdo->query("
        SELECT 
            CONCAT(u.first_name, ' ', u.last_name) AS nombre,
            d.name AS departamento,
            COUNT(a.id) AS consultas,
            AVG(r.score) AS valoracion
        FROM appointments a
        JOIN users u ON a.doctor_id = u.id
        JOIN doctors doc ON doc.user_id = u.id
        LEFT JOIN departments d ON doc.department_id = d.id
        LEFT JOIN ratings r ON r.doctor_id = u.id
        WHERE a.status = 'completada' AND u.is_active = 1
        GROUP BY u.id
        ORDER BY consultas DESC
        LIMIT 5
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
<?php
/**
 * Controlador de Citas Médicas.
 * Gestiona la creación, listado, cancelación y finalización de citas.
 * Integra auditoría (Logs) y transacciones para mayor fiabilidad.
 * @package CliniGest\Controllers
 * @version 2.1
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Importar Logger para registrar eventos

/**
 * Crea una nueva cita médica y registra el pago inicial.
 * @param array $data Datos del formulario (user_id, doctor_id, date, time, etc.)
 * @return array ['success' => bool] o ['error' => string]
 */
function crearCita($data) {
    global $pdo;

    // ID de usuario para el log (si falla antes de leerlo, usamos 0)
    $userId = $data['user_id'] ?? 0;

    try {
        $doctorId = $data['doctor_id'];
        $fecha = $data['date'];
        $hora = $data['time'];
        $reason = trim($data['reason']);
        $amount = floatval($data['amount']);
        $paymentMethod = $data['method'];

        $scheduledAt = "$fecha $hora";

        // Validaciones básicas
        if (empty($reason)) return ['error' => 'El motivo de la cita es obligatorio.'];

        $horaInt = (int) date('H', strtotime($scheduledAt));
        if ($horaInt < 8 || $horaInt >= 18) {
            return ['error' => 'La hora debe estar entre las 08:00 y las 18:00.'];
        }

        if ($amount < 100) {
            return ['error' => 'El monto debe ser mínimo de $100.00'];
        }

        // Verificar límite de citas pendientes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE user_id = ? AND status = 'pendiente'");
        $stmt->execute([$userId]);
        $pendientes = $stmt->fetchColumn();

        if ($pendientes >= 3) {
            // [OPCIONAL] Loguear intento fallido por límite
            log_audit($userId, 'reserva_bloqueada', 'Límite de citas excedido');
            return ['error' => 'Ya tienes 3 o más citas pendientes. Cancela alguna antes de continuar.'];
        }

        // [MEJORA] Uso de Transacciones para integridad de datos (Todo o nada)
        $pdo->beginTransaction();

        // 1. Insertar Cita
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, doctor_id, scheduled_at, reason) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$userId, $doctorId, $scheduledAt, $reason]);

        if (!$result) {
            throw new Exception("Fallo al insertar la cita en la base de datos.");
        }

        $appointmentId = $pdo->lastInsertId();

        // 2. Registrar Pago
        $stmt = $pdo->prepare("INSERT INTO payments (appointment_id, amount, method, status) VALUES (?, ?, ?, 'completado')");
        $stmt->execute([$appointmentId, $amount, $paymentMethod]);

        // Confirmar cambios
        $pdo->commit();

        // [MEJORA] Log de Éxito (Auditoría) - SE REGISTRA EN BD
        log_audit($userId, 'cita_creada', "Cita ID: $appointmentId creada con Dr. ID: $doctorId. Monto: $$amount");

        return ['success' => true];

    } catch (Exception $e) {
        // Si algo falla, revertir cambios para no dejar basura en la BD
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // [MEJORA] Log de Error Técnico en BD - SE REGISTRA EN BD
        log_audit($userId, 'error_crear_cita', "Fallo SQL/Lógico: " . $e->getMessage());

        return ['error' => 'Ocurrió un error interno al agendar la cita. El incidente ha sido registrado.'];
    }
}

function obtenerDepartamentos() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name FROM departments ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDoctoresConDepartamento() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT d.id AS doctor_id, d.user_id, u.first_name, u.last_name, dep.name AS departamento
        FROM doctors d
        JOIN users u ON d.user_id = u.id
        JOIN departments dep ON d.department_id = dep.id
        ORDER BY dep.name, u.first_name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerCitasPorUsuario($userId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT a.id, a.scheduled_at, a.status, a.reason, a.doctor_id,
               u.first_name AS doctor_first_name, u.last_name AS doctor_last_name,
               d.name AS departamento
        FROM appointments a
        JOIN users u ON a.doctor_id = u.id
        JOIN doctors doc ON doc.user_id = u.id
        JOIN departments d ON doc.department_id = d.id
        WHERE a.user_id = :user_id
        ORDER BY a.scheduled_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Cancela una cita si pertenece al usuario y está pendiente.
 */
function cancelarCita($id, $userId) {
    global $pdo;

    // Verifica que la cita pertenezca al usuario y esté pendiente
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = :id AND user_id = :user_id AND status = 'pendiente'");
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId
    ]);

    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cita) {
        // Actualiza estado a cancelada
        $update = $pdo->prepare("UPDATE appointments SET status = 'cancelada' WHERE id = :id");
        $update->execute([':id' => $id]);
        
        // [MEJORA] Log de cancelación
        log_audit($userId, 'cita_cancelada', "Usuario canceló cita ID: $id");
        
        return true;
    }

    // [OPCIONAL] Log de intento fallido de cancelación
    log_audit($userId, 'error_cancelar_cita', "Intento fallido cancelación ID: $id (No existe o no es dueño)");
    return false;
}

/**
 * Completa una consulta médica, guardando historial y actualizando estado.
 */
function completarConsulta($data) {
    global $pdo;

    // Obtenemos el usuario actual (Doctor) desde la sesión si es posible
    $doctorId = $_SESSION['user']['id'] ?? 0; 

    try {
        $appointmentId = $data['appointment_id'];
        $padecimiento = trim($data['padecimiento']);
        $medicamento = trim($data['medicamento']);
        $instrucciones = trim($data['instrucciones']);
        $observaciones = trim($data['observaciones'] ?? '');

        if (empty($padecimiento) || empty($medicamento) || empty($instrucciones)) {
            return ['error' => 'Todos los campos obligatorios deben ser completados.'];
        }

        $pdo->beginTransaction();

        // 1. Guardar el registro médico
        $stmt = $pdo->prepare("INSERT INTO medical_records (user_id, appointment_id, notes) VALUES (
            (SELECT user_id FROM appointments WHERE id = ?),
            ?, 
            ?
        )");
        
        $notas = "Padecimiento: $padecimiento\nMedicamento: $medicamento\nInstrucciones: $instrucciones\nObservaciones: $observaciones";
        $stmt->execute([$appointmentId, $appointmentId, $notas]);

        // 2. Cambiar estado de la cita
        $stmt = $pdo->prepare("UPDATE appointments SET status = 'completada' WHERE id = ?");
        $stmt->execute([$appointmentId]);

        $pdo->commit();

        // [MEJORA] Log de consulta completada
        log_audit($doctorId, 'consulta_completada', "Cita ID: $appointmentId completada por Dr. ID: $doctorId");

        return ['success' => true];

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        // [MEJORA] Log de error en consulta
        log_audit($doctorId, 'error_consulta', "Fallo al completar consulta ID $appointmentId: " . $e->getMessage());
        
        return ['error' => 'Error interno al guardar la consulta.'];
    }
}

function obtenerPacientesDelDoctor($doctorId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT DISTINCT 
            u.id, u.first_name, u.last_name, u.phone, u.birthdate, u.gender,
            u.email, u.address,
            mi.blood_type, mi.allergies, mi.chronic_diseases, mi.current_medications,
            mi.updated_at
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        LEFT JOIN medical_info mi ON u.id = mi.user_id
        WHERE a.doctor_id = ?
        ORDER BY u.last_name
    ");
    $stmt->execute([$doctorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDatosPaciente($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function obtenerHistorialMedico($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerHistorialMedicoPorUsuario($userId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT notes, created_at 
        FROM medical_records 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
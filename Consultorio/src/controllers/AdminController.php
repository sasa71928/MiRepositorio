<?php
require_once __DIR__ . '/../config/database.php';

function obtenerTotalDoctores(): int {
    global $pdo;
    return (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 2")->fetchColumn();
}

function obtenerTotalPacientes(): int {
    global $pdo;
    return (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role_id = 3")->fetchColumn();
}


function crearDoctor($data) {
    global $pdo;

    // Paso 1: Insertar en tabla users
    $stmtUser = $pdo->prepare("
        INSERT INTO users (role_id, username, password_hash, first_name, last_name, email, phone, birthdate, gender, address, city) 
        VALUES (2, :username, :password, :first_name, :last_name, :email, :phone, :birthdate, :gender, :address, :city)
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

    // Paso 2: Insertar en tabla doctors
    $stmtDoctor = $pdo->prepare("
        INSERT INTO doctors (user_id, cedula_profesional, department_id)
        VALUES (:user_id, :cedula, :department_id)
    ");

    $stmtDoctor->execute([
        ':user_id'       => $userId,
        ':cedula'        => $data['cedula'],
        ':department_id' => $data['department_id'],
    ]);
}

function obtenerDepartamentos() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name FROM departments");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDoctores($limite, $offset, $departamento = '', $nombre = '') {
    global $pdo;

    $sql = "
        SELECT u.id, u.username, u.first_name, u.last_name, u.email, u.phone, 
               u.birthdate, u.gender, u.address, u.city,
               d.name AS departamento, 
               doc.cedula_profesional,
               doc.department_id
        FROM users u
        JOIN doctors doc ON u.id = doc.user_id
        JOIN departments d ON doc.department_id = d.id
        WHERE u.role_id = 2
    ";

    $params = [];

    $departamento = trim($departamento);
    if ($departamento !== '') {

            $sql .= " AND d.name = :departamento";
            $params[':departamento'] = $departamento;
        }

        if (!empty($nombre)) {
            $sql .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE :nombre";
            $params[':nombre'] = "%$nombre%";
        }

        $sql .= " LIMIT :limite OFFSET :offset";
        $stmt = $pdo->prepare($sql);

        // bindValue necesario para LIMIT y OFFSET como enteros
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function contarDoctores($departamento = '', $nombre = '') {
        global $pdo;

        $sql = "
            SELECT COUNT(*) 
            FROM users u
            JOIN doctors doc ON u.id = doc.user_id
            JOIN departments d ON doc.department_id = d.id
            WHERE u.role_id = 2
        ";

        $params = [];

    $departamento = trim($departamento);
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


function obtenerDoctoresFiltrados($filtroDepartamento = '', $nombreBuscado = '', $limite = 5, $offset = 0) {
    global $pdo;

    $sql = "
        SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS nombre, 
               u.email, u.phone, d.name AS departamento
        FROM users u
        JOIN doctors doc ON u.id = doc.user_id
        JOIN departments d ON doc.department_id = d.id
        WHERE u.role_id = 2
    ";

    $params = [];

    if (!empty($filtroDepartamento)) {
        $sql .= " AND doc.department_id = :departamento";
        $params[':departamento'] = $filtroDepartamento;
    }

    if (!empty($nombreBuscado)) {
        $sql .= " AND (u.first_name LIKE :nombre OR u.last_name LIKE :nombre)";
        $params[':nombre'] = "%$nombreBuscado%";
    }

    $sql .= " LIMIT :limite OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function editarDoctor() {
    global $pdo;

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data || !isset($data['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        return;
    }

    $id = $data['id'];

    // Actualiza la tabla `users`
    $stmt = $pdo->prepare("UPDATE users SET
        username = :username,
        first_name = :first_name,
        last_name = :last_name,
        email = :email,
        phone = :phone,
        birthdate = :birthdate,
        gender = :gender,
        address = :address,
        city = :city
        " . (!empty($data['password']) ? ", password_hash = :password" : "") . "
        WHERE id = :id");

    $params = [
        ':username' => $data['username'],
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':email' => $data['email'],
        ':phone' => $data['phone'],
        ':birthdate' => $data['birthdate'],
        ':gender' => $data['gender'],
        ':address' => $data['address'],
        ':city' => $data['city'],
        ':id' => $id
    ];

    if (!empty($data['password'])) {
        $params[':password'] = password_hash($data['password'], PASSWORD_BCRYPT);
    }

    $stmt->execute($params);

    // Actualiza la tabla `doctors`
    $stmt2 = $pdo->prepare("UPDATE doctors SET 
        department_id = :department_id,
        cedula_profesional = :cedula 
        WHERE user_id = :id");

    $stmt2->execute([
        ':department_id' => $data['departamento_id'],
        ':cedula' => $data['cedula'],
        ':id' => $id
    ]);

    echo json_encode(['status' => 'ok']);
}

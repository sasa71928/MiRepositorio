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

function obtenerDoctores(): array {
    global $pdo;

    $stmt = $pdo->query("
        SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS nombre,
               u.email, u.phone, d.name AS departamento,
               doc.cedula_profesional
        FROM users u
        JOIN doctors doc ON u.id = doc.user_id
        LEFT JOIN departments d ON doc.department_id = d.id
        WHERE u.role_id = 2
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

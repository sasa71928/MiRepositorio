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

function obtenerDoctores(): array {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM users WHERE role_id = 2");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function crearDoctor($data) {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO users (role_id, username, password_hash, first_name, last_name, email, phone, birthdate, gender, address, city) 
        VALUES (2, :username, :password, :first_name, :last_name, :email, :phone, :birthdate, :gender, :address, :city)");

    $stmt->execute([
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
}

function obtenerDepartamentos() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name FROM departments");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

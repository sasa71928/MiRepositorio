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

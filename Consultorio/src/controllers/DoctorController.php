<?php
require_once __DIR__ . '/../config/database.php';

function obtenerCitasDelDoctor($doctorId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT a.id, a.scheduled_at, a.status,
               u.first_name, u.last_name
        FROM appointments a
        JOIN users u ON u.id = a.user_id
        WHERE a.doctor_id = :doctor_id
        ORDER BY a.scheduled_at ASC
    ");
    $stmt->execute(['doctor_id' => $doctorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

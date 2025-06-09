<?php
require_once __DIR__ . '/../config/database.php';


function obtenerCitasDelDoctor($doctorId) {
    global $pdo;

    $stmt = $pdo->prepare("
SELECT a.id, a.scheduled_at, a.status, u.first_name, u.last_name
FROM appointments a
JOIN users u ON a.user_id = u.id
WHERE a.doctor_id = :doctor_id

        ORDER BY a.scheduled_at ASC
    ");
    $stmt->execute(['doctor_id' => $doctorId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerCitaPorIdYDoctor($citaId, $doctorId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT a.*, 
               u.first_name, u.last_name, u.phone
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        WHERE a.id = :id AND a.doctor_id = :doctor
    ");
    $stmt->execute([
        ':id' => $citaId,
        ':doctor' => $doctorId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function cancelarCitaDoctor($citaId, $doctorId) {
    global $pdo;

    $stmt = $pdo->prepare("
        UPDATE appointments
        SET status = 'cancelada'
        WHERE id = :id AND doctor_id = :doctor
    ");
    $stmt->execute([
        ':id' => $citaId,
        ':doctor' => $doctorId
    ]);
}

function obtenerCitasPorUsuario($userId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT 
            scheduled_at, 
            reason, 
            status
        FROM appointments 
        WHERE user_id = :id 
        ORDER BY scheduled_at DESC
    ");
    $stmt->execute([':id' => $userId]);

    $citasRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $citas = [];

    foreach ($citasRaw as $cita) {
        $citas[] = [
            'fecha' => $cita['scheduled_at'],
            'motivo' => $cita['reason'],
            'estado' => $cita['status'],
        ];
    }

    return $citas;
}

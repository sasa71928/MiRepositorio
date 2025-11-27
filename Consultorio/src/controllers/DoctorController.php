<?php
/**
 * Controlador del Panel de Doctor.
 * @package CliniGest\Controllers
 * @version 2.1
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Logger

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
    $stmt->execute([':id' => $citaId, ':doctor' => $doctorId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function cancelarCitaDoctor($citaId, $doctorId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            UPDATE appointments
            SET status = 'cancelada'
            WHERE id = :id AND doctor_id = :doctor
        ");
        $stmt->execute([':id' => $citaId, ':doctor' => $doctorId]);
        
        // [MEJORA] Log de auditoría cuando el DOCTOR cancela
        log_audit($doctorId, 'cita_cancelada_doctor', "El doctor canceló la cita ID: $citaId");
        
    } catch (Exception $e) {
        log_audit($doctorId, 'error_cancelar_doctor', "Error SQL: " . $e->getMessage());
    }
}
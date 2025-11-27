<?php
/**
 * Controlador de Valoraciones.
 * Gestiona las calificaciones de los pacientes a los doctores.
 * Integra auditoría (Logs) para trazabilidad.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Logger

function obtenerValoracionesUsuario($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT r.id, r.comment, r.score AS rating, r.created_at,
                   a.id AS appointment_id, 
                   u.first_name AS doctor_first_name, u.last_name AS doctor_last_name
            FROM ratings r
            JOIN appointments a ON r.appointment_id = a.id
            JOIN users u ON a.doctor_id = u.id
            WHERE a.user_id = :user_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        log_audit($userId, 'error_leer_valoraciones', $e->getMessage());
        return [];
    }
}

function obtenerCitasCompletadasNoValoradas($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT r.id, r.appointment_id, r.comment, r.score AS rating, r.created_at,
                u.first_name AS doctor_first_name, u.last_name AS doctor_last_name
            FROM ratings r
            RIGHT JOIN appointments a ON r.appointment_id = a.id
            JOIN users u ON a.doctor_id = u.id
            WHERE a.user_id = :user_id 
            AND a.status = 'completada'
            AND a.id NOT IN (SELECT appointment_id FROM ratings)
            ORDER BY a.scheduled_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function guardarValoracion($data) {
    global $pdo;
    
    // Obtenemos el ID del usuario de la sesión para el log
    $userId = $_SESSION['user']['id'] ?? 0;

    try {
        $appointmentId = $data['appointment_id'];
        $doctorId = $data['doctor_id'];
        $score = $data['score'];
        $comment = trim($data['comment']);

        if (empty($score) || $score < 1 || $score > 5) {
            // [MEJORA] Log de validación
            log_audit($userId, 'error_valoracion_val', "Puntaje inválido: $score");
            return false;
        }

        $stmt = $pdo->prepare("
            INSERT INTO ratings (appointment_id, rater_id, doctor_id, score, comment, created_at)
            VALUES (:appointment_id, :rater_id, :doctor_id, :score, :comment, NOW())
        ");
        
        $stmt->execute([
            ':appointment_id' => $appointmentId,
            ':rater_id' => $userId, // Usamos el ID de sesión, es más seguro
            ':doctor_id' => $doctorId,
            ':score' => $score,
            ':comment' => $comment
        ]);

        // [MEJORA] Log de éxito
        log_audit($userId, 'valoracion_creada', "Calificación: $score estrellas al Dr. ID: $doctorId");

        return true;

    } catch (PDOException $e) {
        // [MEJORA] Log de error técnico
        log_audit($userId, 'error_sql_valoracion', $e->getMessage());
        return false;
    }
}
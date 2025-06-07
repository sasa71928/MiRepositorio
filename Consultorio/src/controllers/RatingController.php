<?php
require_once __DIR__ . '/../config/database.php'; 

function obtenerValoracionesUsuario($userId) {
    global $pdo;

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
}


function obtenerCitasCompletadasNoValoradas($userId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT r.id, r.appointment_id, r.comment, r.score AS rating, r.created_at,
            u.first_name AS doctor_first_name, u.last_name AS doctor_last_name
        FROM ratings r
        JOIN appointments a ON r.appointment_id = a.id
        JOIN users u ON a.doctor_id = u.id
        WHERE a.user_id = :user_id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function guardarValoracion($data) {
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO ratings (appointment_id, rater_id, doctor_id, score, comment, created_at)
        VALUES (:appointment_id, :rater_id, :doctor_id, :score, :comment, NOW())
    ");
    $stmt->execute([
        ':appointment_id' => $data['appointment_id'],
        ':rater_id' => $_SESSION['user']['id'],
        ':doctor_id' => $data['doctor_id'],
        ':score' => $data['score'],
        ':comment' => $data['comment']
    ]);
}

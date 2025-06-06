<?php
require_once __DIR__ . '/../config/database.php';

function crearCita($data) {
    global $pdo;

    $userId = $_SESSION['user']['id'];
    $doctorId = (int)$data['doctor'];
    $fecha = $data['date'];
    $hora = $data['time'];
    $mensaje = $data['message'] ?? null; // si deseas usarlo más adelante

    // Combina fecha y hora en DATETIME
    $scheduledAt = date('Y-m-d H:i:s', strtotime("$fecha $hora"));

        $reason = $data['message'] ?? null;

        $stmt = $pdo->prepare("
            INSERT INTO appointments (user_id, doctor_id, scheduled_at, reason)
            VALUES (:user_id, :doctor_id, :scheduled_at, :reason)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':doctor_id' => $doctorId,
            ':scheduled_at' => $scheduledAt,
            ':reason' => $reason
    ]);

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


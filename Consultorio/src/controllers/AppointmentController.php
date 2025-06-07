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

function obtenerCitasPorUsuario($userId) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT a.id, a.scheduled_at, a.status, a.reason,
               u.first_name AS doctor_first_name, u.last_name AS doctor_last_name,
               d.name AS departamento
        FROM appointments a
        JOIN users u ON a.doctor_id = u.id
        JOIN doctors doc ON doc.user_id = u.id
        JOIN departments d ON doc.department_id = d.id
        WHERE a.user_id = :user_id
        ORDER BY a.scheduled_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function cancelarCita($id, $userId) {
    global $pdo;

    // Verifica que la cita pertenezca al usuario y esté pendiente
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = :id AND user_id = :user_id AND status = 'pendiente'");
    $stmt->execute([
        ':id' => $id,
        ':user_id' => $userId
    ]);

    $cita = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cita) {
        // Actualiza estado a cancelada
        $update = $pdo->prepare("UPDATE appointments SET status = 'cancelada' WHERE id = :id");
        $update->execute([':id' => $id]);
        return true;
    }

    return false;
}

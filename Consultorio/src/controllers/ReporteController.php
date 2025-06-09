<?php
require_once __DIR__ . '/../config/database.php';

function generarReporteGeneral() {
    global $pdo;

    $stmt = $pdo->query("
        SELECT d.name AS departamento,
               COUNT(doc.id) AS total_doctores,
               (SELECT COUNT(*) FROM appointments a 
                JOIN doctors d2 ON a.doctor_id = d2.id 
                WHERE d2.department_id = d.id) AS total_citas
        FROM departments d
        LEFT JOIN doctors doc ON doc.department_id = d.id
        GROUP BY d.id
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

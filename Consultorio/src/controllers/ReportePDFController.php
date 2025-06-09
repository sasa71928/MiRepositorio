<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarReporteResumen($correo) {
    global $pdo;

    require_once __DIR__ . '/AdminController.php';
    $totalConsultas = contarTotalConsultas();
    $nuevosPacientes = contarNuevosPacientes();
    $ingresosTotales = calcularIngresosTotales();
    $doctoresActivos = obtenerDoctoresMasActivos();

    // Generar el HTML del cuerpo
    $html = "<h2>Reporte de CliniGest</h2>";
    $html .= "<p><strong>Total Consultas:</strong> {$totalConsultas}</p>";
    $html .= "<p><strong>Nuevos Pacientes:</strong> {$nuevosPacientes}</p>";
    $html .= "<p><strong>Ingresos Totales:</strong> $" . number_format($ingresosTotales, 2) . "</p>";

    $html .= "<h3>Doctores más Activos</h3>";
    $html .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
    $html .= "<thead><tr><th>Doctor</th><th>Departamento</th><th>Consultas</th><th>Valoración</th></tr></thead><tbody>";

    foreach ($doctoresActivos as $doc) {
        $html .= "<tr>
            <td>{$doc['nombre']}</td>
            <td>{$doc['departamento']}</td>
            <td>{$doc['consultas']}</td>
            <td>" . number_format($doc['valoracion'], 1) . "</td>
        </tr>";
    }

    $html .= "</tbody></table>";

    // Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'clinigest.soporte@gmail.com';
        $mail->Password   = 'ybeobtwdzapgdbyn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('clinigest.soporte@gmail.com', 'CliniGest');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = 'Reporte General de CliniGest';
        $mail->Body    = $html;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar el correo: " . $mail->ErrorInfo);
        return false;
    }
}

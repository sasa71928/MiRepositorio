<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../helpers/Logger.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Genera y envía el reporte general (Resumen y Doctores)
 */
function enviarReporteResumen($correo) {
    global $pdo;
    require_once __DIR__ . '/AdminController.php';
    
    $totalConsultas = contarTotalConsultas();
    $nuevosPacientes = contarNuevosPacientes();
    $ingresosTotales = calcularIngresosTotales();
    $doctoresActivos = obtenerDoctoresMasActivos();
    $fechaReporte = date('d/m/Y H:i');

    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; color: #333; background-color: #f4f6f9; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #ddd; }
            .header { text-align: center; border-bottom: 2px solid #1977cc; padding-bottom: 15px; margin-bottom: 20px; }
            .header h1 { color: #1977cc; margin: 0; }
            .card { background: #1977cc; color: white; padding: 15px; text-align: center; border-radius: 5px; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 20px; }
            th { background-color: #f8f9fa; padding: 10px; border-bottom: 2px solid #ddd; }
            td { padding: 10px; border-bottom: 1px solid #eee; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Reporte General CliniGest</h1>
                <p>Generado el: $fechaReporte</p>
            </div>
            <div class='card'>
                <h3>$totalConsultas Consultas Totales</h3>
                <p>Ingresos: $" . number_format($ingresosTotales, 2) . "</p>
            </div>
            <h3>Top Doctores</h3>
            <table>
                <thead><tr><th>Doctor</th><th>Depto</th><th>Citas</th><th>Calif.</th></tr></thead>
                <tbody>";
    foreach ($doctoresActivos as $doc) {
        $html .= "<tr><td>{$doc['nombre']}</td><td>{$doc['departamento']}</td><td style='text-align:center'>{$doc['consultas']}</td><td style='text-align:center'>{$doc['valoracion']}</td></tr>";
    }
    $html .= "</tbody></table>
        </div>
    </body>
    </html>";

    return enviarCorreo($correo, 'Reporte General - CliniGest', $html, 'general');
}

/**
 * [NUEVO] Genera y envía el reporte de auditoría (Logs)
 */
function enviarReporteAuditoria($correo) {
    require_once __DIR__ . '/AdminController.php';

    $statsLogs = obtenerEstadisticasLogs();
    $fechaReporte = date('d/m/Y H:i');

    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; padding:20px; background:#f4f6f9; }
            .container { background:white; padding:20px; border-radius:8px; border:1px solid #ddd; }
            .header { text-align:center; border-bottom:2px solid #1977cc; margin-bottom:15px; }
            .header h1 { margin:0; color:#1977cc; }
            table { width:100%; border-collapse:collapse; margin-top:20px; }
            th { background:#1977cc; color:white; padding:10px; }
            td { padding:8px; border-bottom:1px solid #eee; }
        </style>
    </head>
    <body>
    <div class='container'>
        <div class='header'>
            <h1>Reporte de Auditoría</h1>
            <p>Generado el: $fechaReporte</p>
        </div>
        <table>
            <thead>
                <tr><th>Acción</th><th>Total</th></tr>
            </thead>
            <tbody>";

    foreach ($statsLogs as $log) {
        $accion = ucwords(str_replace('_', ' ', $log['action']));
        $html .= "<tr><td>{$accion}</td><td style='text-align:center'>{$log['total']}</td></tr>";
    }

    $html .= "
            </tbody>
        </table>
    </div>
    </body>
    </html>";

    return enviarCorreo($correo, 'Reporte Auditoría - CliniGest', $html, 'auditoria');
}

function enviarReporteListado($correo) {
    require_once __DIR__ . '/AdminController.php';

    $doctoresActivos = obtenerDoctoresMasActivos();
    $fechaReporte = date('d/m/Y H:i');

    $html = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; padding:20px; background:#f4f6f9; }
            .container { background:white; padding:20px; border-radius:8px; border:1px solid #ddd; }
            .header { text-align:center; border-bottom:2px solid #1977cc; margin-bottom:15px; }
            .header h1 { margin:0; color:#1977cc; }
            table { width:100%; border-collapse:collapse; margin-top:20px; }
            th { background:#1977cc; color:white; padding:10px; }
            td { padding:8px; border-bottom:1px solid #eee; }
        </style>
    </head>
    <body>
    <div class='container'>
        <div class='header'>
            <h1>Listado Detallado de Doctores</h1>
            <p>Generado el: $fechaReporte</p>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Departamento</th>
                    <th>Consultas</th>
                    <th>Calificación</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($doctoresActivos as $doc) {
        $html .= "
        <tr>
            <td>{$doc['nombre']}</td>
            <td>{$doc['departamento']}</td>
            <td style='text-align:center'>{$doc['consultas']}</td>
            <td style='text-align:center'>{$doc['valoracion']}</td>
        </tr>";
    }

    $html .= "
            </tbody>
        </table>
    </div>
    </body>
    </html>";

    return enviarCorreo($correo, 'Reporte Listado - CliniGest', $html, 'listado');
}

/**
 * Función auxiliar privada para enviar el correo
 */
function enviarCorreo($destinatario, $asunto, $body, $tipoReporte) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'clinigest.soporte@gmail.com';
        $mail->Password   = 'ybeobtwdzapgdbyn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('clinigest.soporte@gmail.com', 'CliniGest Reportes');
        $mail->addAddress($destinatario);
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $body;

        $mail->send();
        
        // Log de la acción
        $userId = $_SESSION['user']['id'] ?? 0;
        log_audit($userId, 'reporte_enviado', "Tipo: $tipoReporte enviado a $destinatario");

        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}
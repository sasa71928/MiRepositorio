<?php
/**
 * Controlador de Recuperación de Contraseñas.
 * Gestiona el envío de códigos OTP y el restablecimiento de contraseñas.
 * Integra auditoría (Logs) para seguridad.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Logger

// Importar PHPMailer
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function solicitar_codigo(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $correo = trim($_POST['correo'] ?? '');
    
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Correo inválido']);
        return;
    }

    try {
        $pdo = require __DIR__ . '/../config/database.php';
        
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Por seguridad, no decimos si el correo existe o no, pero logueamos el intento fallido
            // Opcional: simular tiempo de espera para evitar enumeración de usuarios
            log_audit(0, 'recuperacion_fallida', "Correo no encontrado: $correo");
            echo json_encode(['status' => 'error', 'message' => 'Si el correo existe, se ha enviado un código.']);
            return;
        }

        // Generar código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Guardar en sesión
        $_SESSION['recuperacion'] = [
            'user_id' => $user['id'],
            'email' => $correo,
            'codigo' => $codigo,
            'expira' => time() + 300, // 5 minutos
            'verificado' => false
        ];

        // Enviar correo
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'clinigest.soporte@gmail.com'; // Idealmente mover a config
        $mail->Password   = 'ybeobtwdzapgdbyn';           // Idealmente mover a config
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('clinigest.soporte@gmail.com', 'CliniGest Soporte');
        $mail->addAddress($correo);
        $mail->isHTML(true);
        $mail->Subject = 'Codigo de recuperacion - CliniGest';
        $mail->Body    = "
            <h2>Recuperación de Contraseña</h2>
            <p>Hola <b>{$user['username']}</b>,</p>
            <p>Tu código de verificación es:</p>
            <h1 style='color:#1977cc; letter-spacing: 5px;'>$codigo</h1>
            <p>Este código expira en 5 minutos.</p>
        ";

        $mail->send();
        
        // [LOG]
        log_audit($user['id'], 'solicitud_recuperacion', "Código enviado a $correo");

        echo json_encode(['status' => 'ok']);

    } catch (Exception $e) {
        log_audit(0, 'error_email_recuperacion', "Fallo envío a $correo: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error al enviar el correo. Intente más tarde.']);
    }
}

function verificar_codigo(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $inputCode = $_POST['codigo'] ?? '';

    if (!isset($_SESSION['recuperacion'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sesión expirada. Inicie de nuevo.']);
        return;
    }

    $guardado = $_SESSION['recuperacion']['codigo'];
    $expira   = $_SESSION['recuperacion']['expira'];
    $userId   = $_SESSION['recuperacion']['user_id'];

    if (time() > $expira) {
        log_audit($userId, 'recuperacion_expirada', 'Intento con código expirado');
        unset($_SESSION['recuperacion']);
        echo json_encode(['status' => 'error', 'message' => 'El código ha expirado.']);
        return;
    }

    if ($inputCode !== $guardado) {
        log_audit($userId, 'recuperacion_codigo_invalido', "Ingresó: $inputCode");
        echo json_encode(['status' => 'error', 'message' => 'Código incorrecto.']);
        return;
    }

    // [EXITO] Marcar como verificado
    $_SESSION['recuperacion']['verificado'] = true;
    log_audit($userId, 'codigo_verificado', 'Código correcto, procediendo a cambio de password');
    
    echo json_encode(['status' => 'ok']);
}

function reset_password(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Validación de seguridad: Debe haber pasado por verificar_codigo
    if (!isset($_SESSION['recuperacion']) || empty($_SESSION['recuperacion']['verificado'])) {
        echo json_encode(['status' => 'error', 'message' => 'Acceso no autorizado.']);
        return;
    }

    $newPassword = $_POST['nueva_contrasena'] ?? '';
    $confirm     = $_POST['confirmar_contrasena'] ?? '';
    $userId      = $_SESSION['recuperacion']['user_id'];
    $email       = $_SESSION['recuperacion']['email'];

    if (strlen($newPassword) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres.']);
        return;
    }

    if ($newPassword !== $confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
        return;
    }

    try {
        $pdo = require __DIR__ . '/../config/database.php';

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hashed, $userId]);

        // [LOG FINAL]
        log_audit($userId, 'password_restablecido', "Recuperación por correo exitosa ($email)");

        // Limpiar sesión de recuperación
        unset($_SESSION['recuperacion']);

        echo json_encode(['status' => 'ok']);

    } catch (Exception $e) {
        log_audit($userId, 'error_sql_reset', $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Error interno al actualizar contraseña.']);
    }
}
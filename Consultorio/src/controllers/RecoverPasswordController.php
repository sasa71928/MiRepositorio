<?php
require_once __DIR__ . '/../config/database.php';
global $pdo; // Si estás usando PDO definido globalmente

require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function solicitar_codigo(): void {
    session_start();
    require_once __DIR__ . '/../config/database.php';

    $correo = $_POST['correo'] ?? '';
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Correo inválido',
            'correo' => $correo
        ]);
        return;
    }

    error_log("[solicitar_codigo] Correo recibido: $correo");

    $sql = "SELECT id FROM users WHERE email = ?";
    error_log("[solicitar_codigo] Consulta SQL: $sql");

    global $pdo;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        error_log("[solicitar_codigo] Resultado SQL: vacío");
        echo json_encode([
            'status' => 'no_encontrado',
            'correo' => $correo,
            'consulta' => $sql
        ]);
        return;
    }

    error_log("[solicitar_codigo] Usuario encontrado: ID = " . $user['id']);

    $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['recuperacion'] = [
        'codigo' => $codigo,
        'email' => $correo,
        'expira' => time() + 300 // 5 minutos
    ];

    // Enviar correo con PHPMailer
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'clinigest.soporte@gmail.com';
        $mail->Password   = 'ybeobtwdzapgdbyn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('clinigest.soporte@gmail.com', 'CliniGest Soporte');
        $mail->addAddress($correo);

        $mail->isHTML(true);
        $mail->Subject = 'Codigo de recuperacion de CliniGest';
        $mail->Body    = "
            <h2>Hola 👋</h2>
            <p>Has solicitado recuperar tu contraseña.</p>
            <p>Tu código de verificación es:</p>
            <h1 style='color:#1977cc;'>$codigo</h1>
            <p>Este código expira en 5 minutos.</p>
            <p>Si no solicitaste esto, puedes ignorar este mensaje.</p>
            <hr>
            <small>CliniGest • No responder a este correo</small>
        ";

        $mail->send();
        error_log("[PHPMailer] Correo enviado correctamente a $correo");
    } catch (Exception $e) {
        error_log("[PHPMailer] Error al enviar: {$mail->ErrorInfo}");
    }

    echo json_encode([
        'status' => 'ok',
        'correo' => $correo
    ]);
}

function verificar_codigo(): void {
    session_start();

    $inputCode = $_POST['codigo'] ?? '';

    if (!isset($_SESSION['recuperacion'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sesión de recuperación no iniciada']);
        return;
    }

    $guardado = $_SESSION['recuperacion']['codigo'];
    $expira   = $_SESSION['recuperacion']['expira'];

    if (time() > $expira) {
        unset($_SESSION['recuperacion']);
        echo json_encode(['status' => 'expirado', 'message' => 'El código ha expirado.']);
        return;
    }

    if ($inputCode !== $guardado) {
        echo json_encode(['status' => 'invalido', 'message' => 'El código ingresado no es válido.']);
        return;
    }

    $_SESSION['recuperacion']['verificado'] = true;
    echo json_encode(['status' => 'ok']);
}

function reset_password(): void {
    session_start();

    if (!isset($_SESSION['recuperacion']) || empty($_SESSION['recuperacion']['verificado'])) {
        echo json_encode(['status' => 'error', 'message' => 'No autorizado o código no verificado']);
        return;
    }

    $newPassword = $_POST['nueva_contrasena'] ?? '';
    $confirm     = $_POST['confirmar_contrasena'] ?? '';

    if (strlen($newPassword) < 8 || $newPassword !== $confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña inválida o no coincide']);
        return;
    }

    $correo = $_SESSION['recuperacion']['email'];

    require_once __DIR__ . '/../config/database.php';
    global $pdo;

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
   $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $ok = $stmt->execute([$hashed, $correo]);

    if ($ok) {
        unset($_SESSION['recuperacion']); // limpiar sesión
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar contraseña']);
    }
}


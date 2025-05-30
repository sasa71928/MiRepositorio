<?php
// public/login.php

// Carga helpers y configuración
require_once __DIR__ . '/../src/helpers/auth.php';

$config = include __DIR__ . '/../src/config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

// Si ya está autenticado, redirige al inicio
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

$error = '';
// Procesar POST de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conectar a la BD
    $pdo = include __DIR__ . '/../src/config/database.php';
    // Buscar por correo
    $stmt = $pdo->prepare('SELECT users.*, roles.name AS role_name
                           FROM users
                           JOIN roles ON users.role_id = roles.id
                           WHERE users.email = ?');
    $stmt->execute([ $_POST['correo'] ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['contrasena'], $user['password_hash'])) {
        session_regenerate_id(true);
        // Guardar datos esenciales en la sesión
        $_SESSION['user'] = [
            'id'         => $user['id'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
            'email'      => $user['email'],
            'role'       => $user['role_name'],
        ];
        header('Location: ' . BASE_URL . '/');
        exit;
    } else {
        $error = 'Correo o contraseña inválidos.';
    }
}

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>CliniGest</h1>
        <p>Ingrese sus datos para iniciar sesión</p>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <input type="email" name="correo" placeholder="Correo" required>
            <input type="password" name="contrasena" placeholder="Contraseña" required>
            <button type="submit" class="btn btn-login">Iniciar sesión</button>
        </form>
        <div class="forgot-password">
            <a href="<?= BASE_URL ?>/recuperarContraseña.php">¿Olvidó su contraseña?</a>
        </div>
        <div class="create-account">
            <a href="<?= BASE_URL ?>/registro.php" class="btn btn-create">Crear cuenta</a>
        </div>
        <div class="back-link" style="text-align:center; margin-top:1rem;">
        <a href="<?= BASE_URL ?>/" class="btn btn-link">Volver</a>
        </div>
    </div>

</body>
</html>


<style>
    /* Estilos generales */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background-color: #bdd7e5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        padding: 20px;
    }

    .login-container {
        background-color: white;
        padding: 60px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        min-height: 550px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .login-container h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .login-container p {
        margin-bottom: 30px;
        color: #333;
    }

    .login-container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-login {
        background-color: #3768A7;
        color: white;
    }

    .btn-login:hover {
        background-color: #264d80;
    }

    .btn-create {
        background-color: #1f3970;
        color: white;
        margin-top: 5px;
    }

    .btn-create:hover {
        background-color: #162b56;
    }

    /* Formularios */
    form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
    }

    input {
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    font-size: 16px;
    transition: border-color 0.3s;
    }

    input:focus {
    outline: none;
    border-color: #1977cc;
    }

    .btn {
    padding: 12px 15px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    }

    .btn-login {
    background-color: #1977cc;
    color: white;
    }

    .btn-login:hover {
    background-color: #166ab5;
    }

    .btn-create {
    background-color: transparent;
    color: #1977cc;
    border: 1px solid #1977cc;
    text-decoration: none;
    display: block;
    }

    .btn-create:hover {
    background-color: rgba(25, 119, 204, 0.1);
    }

    /* Enlace de contraseña olvidada */
    .forgot-password {
    margin-bottom: 20px;
    text-align: center;
    }

    .forgot-password a {
    color: #1977cc;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.3s;
    }

    .forgot-password a:hover {
    text-decoration: underline;
    }

    /* Estilos para recuperación de contraseña */
    .recovery-step {
    display: none;
    }

    .recovery-step.active {
    display: block;
    }

    .recovery-info {
    color: #6c757d;
    margin-bottom: 20px;
    font-size: 14px;
    }

    /* Código de verificación */
    .verification-code {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    }

    .code-input {
    width: 40px;
    height: 50px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    }

    /* Requisitos de contraseña */
    .password-requirements {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 15px;
    text-align: left;
    }

    .password-requirements p {
    color: #2c4964;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 14px;
    }

    .password-requirements ul {
    list-style-type: none;
    padding-left: 5px;
    }

    .password-requirements li {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 5px;
    position: relative;
    padding-left: 20px;
    }

    .password-requirements li::before {
    content: "○";
    position: absolute;
    left: 0;
    color: #6c757d;
    }

    .password-requirements li.valid {
    color: #19c079;
    }

    .password-requirements li.valid::before {
    content: "✓";
    color: #19c079;
    }

    /* Confirmación exitosa */
    .success-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .checkmark {
    color: #19c079;
    font-size: 40px;
    line-height: 1;
    }

    .success-title {
    color: #2c4964;
    margin-bottom: 15px;
    font-size: 22px;
    }

    /* Responsivo */
    @media (max-width: 480px) {
    .login-container {
        padding: 30px 20px;
    }

    h1 {
        font-size: 24px;
    }

    p {
        font-size: 14px;
    }

    input,
    .btn {
        padding: 10px 12px;
        font-size: 14px;
    }

    .code-input {
        width: 35px;
        height: 45px;
        font-size: 18px;
    }

    .forgot-password {
        flex-direction: column;
        gap: 10px;
        align-items: center;
    }
    }
</style>
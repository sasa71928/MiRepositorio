<?php
// src/views/public/login.php

// Ya comienza con session_start() y verifica is_logged_in() si quieres
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../src/helpers/auth.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

// Aquí asumo que $error proviene del router (ó de handleLogin)
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CliniGest - Iniciar Sesión</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css" />
</head>
<body>
    <div class="login-container">
        <h1>CliniGest</h1>
        <p>Ingrese sus datos para iniciar sesión</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/login" method="POST">
            <input type="email" name="correo" placeholder="Correo" required />
            <input type="password" name="contrasena" placeholder="Contraseña" required />
            <button type="submit" class="btn btn-login">Iniciar sesión</button>
        </form>

        <div class="forgot-password">
            <a href="<?= BASE_URL ?>/recuperarContraseña.php">¿Olvidó su contraseña?</a>
        </div>
        <div class="create-account">
            <a href="<?= BASE_URL ?>/registro" class="btn btn-create">Crear cuenta</a>
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
    }
</style>

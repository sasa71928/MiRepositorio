<?php
// public/errores.php

// CORRECCIÓN: Solo iniciamos sesión si no hay una activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir BASE_URL si no existe (por si se accede directo a este archivo)
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    // Ajusta esto a tu carpeta real si es necesario
    define('BASE_URL', $protocol . $host . '/ProyectoConsultorio/Consultorio/public');
}

$homeUrl = BASE_URL . '/';

if (isset($_SESSION['user'])) {
    $rol = $_SESSION['user']['role'];
    if ($rol === 'user') {
        $homeUrl = BASE_URL . '/appointments/mine';
    } elseif ($rol === 'doctor') {
        $homeUrl = BASE_URL . '/doctor-home';
    } elseif ($rol === 'admin') {
        $homeUrl = BASE_URL . '/adminDoctors';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>404 – CliniGest</title>
  <!-- Usamos una ruta absoluta para el CSS para asegurar que cargue siempre -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <div class="error-container">
    <h1>404</h1>
    <p>Lo sentimos, la página que buscas no existe.</p>
    <a href="<?= $homeUrl ?>" class="btn">Volver al inicio</a>
  </div>
</body>
</html>

<style>
    /* Estilos integrados para asegurar visualización */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f4f8;
        margin: 0;
    }
    .error-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        text-align: center;
        flex-direction: column;
        color: #2c4964;
    }
    .error-container h1 {
        font-size: 96px;
        margin: 0;
        color: #1977cc;
    }
    .error-container p {
        font-size: 24px;
        margin-bottom: 30px;
        color: #6c757d;
    }
    .error-container a {
        display: inline-block;
        padding: 12px 30px;
        background: #1977cc;
        color: #fff;
        border-radius: 50px;
        text-decoration: none;
        transition: background 0.3s ease;
        font-weight: 600;
    }
    .error-container a:hover {
        background: #166ab5;
    }
</style>
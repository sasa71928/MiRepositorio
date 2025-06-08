<?php
session_start(); // ✅ Asegúrate de que esto esté arriba si no lo has hecho
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
    /* Secciones específicas para la página de error */
    .error-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        text-align: center;
        flex-direction: column;
        color: #2c4964;
    }
    .error-container h1 {
        font-size: 72px;
        margin-bottom: 20px;
    }
    .error-container p {
        font-size: 24px;
        margin-bottom: 30px;
    }
    .error-container a {
        display: inline-block;
        padding: 12px 30px;
        background: #1977cc;
        color: #fff;
        border-radius: 50px;
        text-decoration: none;
        transition: background 0.3s ease;
    }
    .error-container a:hover {
        background: #166ab5;
    }
</style>

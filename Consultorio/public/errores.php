<?php
http_response_code(404);
$config = include __DIR__ . '/../src/config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
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
    <a href="<?= BASE_URL ?>/" class="btn">Volver al inicio</a>
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

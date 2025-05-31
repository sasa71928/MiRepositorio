<?php
// src/views/public/registro.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>CliniGest – Crear Cuenta</title>
    <!-- Apunta al CSS desde ASSETS_URL -->
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/registro.css">
</head>
<body>
    <div class="registro-container">
        <h1>CliniGest</h1>
        <p>Crea una cuenta</p>

        <!-- Si el controlador envió errores, muéstralos -->
        <?php if (!empty($errors)): ?>
            <ul class="errors-list">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/registro" method="POST">
            <!-- Correo electrónico -->
            <input type="email" name="correo" placeholder="Correo electrónico" required
                   value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">

            <!-- Nombre(s) -->
            <input type="text" name="first_name" placeholder="Nombre(s)" required
                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">

            <!-- Apellido(s) -->
            <input type="text" name="last_name" placeholder="Apellido(s)" required
                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">

            <!-- Nombre de usuario -->
            <input type="text" name="username" placeholder="Nombre de usuario" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <!-- Teléfono -->
            <input type="tel" name="phone" placeholder="Número de teléfono" required
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

            <!-- Contraseña -->
            <input type="password" name="contrasena" placeholder="Contraseña" required>

            <!-- Confirmar contraseña -->
            <input type="password" name="confirmar" placeholder="Confirmar contraseña" required>

            <!-- Botón de registro -->
            <button type="submit" class="btn btn-registrar">Registrarte</button>
        </form>

        <a href="<?= BASE_URL ?>/login" class="enlace-login">¿Ya tienes una cuenta?</a>
    </div>
</body>
</html>

<style>
    /* Tus estilos específicos para registro */
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

    .registro-container {
        background-color: white;
        padding: 60px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 500px;
        min-height: 600px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .registro-container h1 {
        font-size: 48px;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .registro-container p {
        margin-bottom: 30px;
        color: #333;
    }

    .registro-container input {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
    }

    .btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-top: 12px;
        border: none;
        border-radius: 4px;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-registrar {
        background-color: #1f3970;
        color: white;
    }

    .btn-registrar:hover {
        background-color: #162b56;
    }

    .enlace-login {
        display: block;
        margin-top: 20px;
        color: #1f5c85;
        text-decoration: none;
        font-size: 14px;
    }

    .enlace-login:hover {
        text-decoration: underline;
    }

    .errors-list {
        list-style: none;
        padding: 0;
        margin-bottom: 20px;
        color: #c0392b;
    }

    .errors-list li {
        margin: 4px 0;
    }

    @media (max-width: 480px) {
        .registro-container {
            padding: 40px 20px;
            min-height: auto;
        }
        .registro-container h1 {
            font-size: 36px;
        }
        .btn {
            font-size: 16px;
            padding: 10px;
        }
    }
</style>

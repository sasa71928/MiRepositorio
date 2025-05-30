<?php
// src/controllers/LoginController.php

require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/auth.php';

/**
 * Muestra el formulario de login, con un posible mensaje de error.
 *
 * @param string|null $error
 */
function showLogin(?string $error = null): void {
    include __DIR__ . '/../views/public/login.php';
    exit;
}

/**
 * Procesa el POST del login.
 * - Si OK, redirige a "/"
 * - Si falla, vuelve a mostrar el form con $error.
 */
function handleLogin(): void {
    $email    = $_POST['correo']    ?? '';
    $password = $_POST['contrasena'] ?? '';
    $error    = null;

    try {
        $pdo  = getPDO();
        $stmt = $pdo->prepare(
            'SELECT u.*, r.name AS role_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.email = ?'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'         => $user['id'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
                'role'       => $user['role_name'],
            ];
            redirect('/');
        }

        $error = 'Correo o contraseña inválidos.';
    } catch (PDOException $ex) {
        $error = 'Error de conexión: ' . $ex->getMessage();
    }

    showLogin($error);
}

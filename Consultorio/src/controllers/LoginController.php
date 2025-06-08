<?php
// src/controllers/LoginController.php

require_once __DIR__ . '/../helpers/auth.php';

$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Muestra el formulario de login (o redirige si ya está logueado)
 */
function showLogin(): void {
    if (is_logged_in()) {
        header('Location: ' . BASE_URL . '/');
        exit;
    }
    // Incluir la plantilla correcta de login
    include __DIR__ . '/../views/public/login.php';
    exit;
}

/**
 * Procesa el POST de login y devuelve mensaje de error si falla
 */
function handleLogin(string $email, string $password): ?string {
    try {
        $pdo = include __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare(
            'SELECT users.*, roles.name AS role_name
             FROM users
             JOIN roles ON users.role_id = roles.id
             WHERE users.email = ?'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id'         => $user['id'],
        'username'   => $user['username'],
        'first_name' => $user['first_name'],
        'last_name'  => $user['last_name'],
        'email'      => $user['email'],
        'role'       => $user['role_name'],
        'phone'      => $user['phone'],
    ];

        // 🔁 Redirección personalizada según el rol
        switch ($_SESSION['user']['role']) {
            case 'doctor':
                header('Location: ' . BASE_URL . '/doctor-home');
                break;
            case 'admin':
                header('Location: ' . BASE_URL . '/adminDoctors');
                break;
            default:
                header('Location: ' . BASE_URL . '/');
        }

        exit;
    }
        return 'Correo o contraseña inválidos.';

    } catch (PDOException $ex) {
        return 'Error de conexión: ' . $ex->getMessage();
    }
}

<?php
// src/controllers/LoginController.php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/Logger.php'; // [MEJORA] Logger

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
            
            // [MEJORA SOFT DELETE] Verificar si el usuario está activo
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                log_audit($user['id'], 'login_bloqueado', "Intento de acceso de usuario inactivo: $email");
                return 'Su cuenta ha sido suspendida. Contacte al administrador.';
            }

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

            // [MEJORA] Registrar login exitoso
            log_audit($user['id'], 'login_success', "Inicio de sesión exitoso desde IP: " . $_SERVER['REMOTE_ADDR']);

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
        
        // [MEJORA] Registrar intento fallido
        $userIdIntento = $user ? $user['id'] : 0; 
        log_audit($userIdIntento, 'login_failed', "Fallo login correo: $email | IP: " . $_SERVER['REMOTE_ADDR']);

        return 'Correo o contraseña inválidos.';

    } catch (PDOException $ex) {
        error_log("Error DB Login: " . $ex->getMessage());
        return 'Error de conexión. Intente más tarde.';
    }
}
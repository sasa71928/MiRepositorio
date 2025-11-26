<?php
// src/controllers/LoginController.php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/Logger.php'; // Importar Logger

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

            // [MEJORA] Registrar login exitoso en audit_logs
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
        
        // [MEJORA] Registrar intento fallido (Si existe el usuario logueamos su ID, si no, logueamos 0 o null e indicamos el email intentado)
        $userIdIntento = $user ? $user['id'] : 0; 
        log_audit($userIdIntento, 'login_failed', "Fallo login correo: $email | IP: " . $_SERVER['REMOTE_ADDR']);

        return 'Correo o contraseña inválidos.';

    } catch (PDOException $ex) {
        // [MEJORA] Log de error de base de datos en login
        error_log("Error DB Login: " . $ex->getMessage());
        return 'Error de conexión. Intente más tarde.';
    }
}
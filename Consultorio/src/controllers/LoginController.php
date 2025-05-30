<?php
require_once __DIR__ . '/../helpers/auth.php';

// Asegura que BASE_URL esté definida
$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Muestra el formulario de login
 */
function showLogin(): void {
    include __DIR__ . '/../../public/login.php';
    exit;
}

/**
 * Procesa el formulario de login y retorna mensaje de error si falla
 *
 * @param string $email
 * @param string $password
 * @return string|null
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
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
                'role'       => $user['role_name'],
            ];
            header('Location: ' . BASE_URL . '/');
            exit;
        }

        return 'Correo o contraseña inválidos.';
    } catch (PDOException $ex) {
        return 'Error de conexión: ' . $ex->getMessage();
    }
}
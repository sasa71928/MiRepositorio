<?php
// src/helpers/auth.php

// 1) Arranca la sesión si aún no se ha iniciado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Asegurarse de que BASE_URL esté definida antes de usarla
if (! defined('BASE_URL')) {
    // Suponemos que config.php está en src/config/config.php
    $cfg = include __DIR__ . '/../config/config.php';
    define('BASE_URL', rtrim($cfg['base_url'], '/'));
}

/**
 * Cierra la sesión y redirige a /
 * [MEJORA DE SEGURIDAD] Elimina la cookie de sesión para un cierre completo.
 */
function logout_user(): void {
    // Limpia todas las variables de sesión
    $_SESSION = [];
    
    // 1. Si se usa una cookie de sesión, fuerzo su expiración (Cierre robusto)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // 2. Destruye la sesión en el servidor
    session_destroy();
    
    // 3. Redirige al inicio
    header('Location: ' . BASE_URL . '/');
    exit;
}


/**
 * @return bool   Si hay un usuario autenticado en $_SESSION['user']
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

/**
 * @return bool   Si el usuario en sesión tiene rol 'admin'
 */
function is_admin(): bool {
    return is_logged_in() && (
        isset($_SESSION['user']['role'])
        && $_SESSION['user']['role'] === 'admin'
    );
}

/**
 * @return bool   Si el usuario en sesión tiene rol 'doctor'
 */
function is_doctor(): bool {
    return is_logged_in() && (
        isset($_SESSION['user']['role'])
        && $_SESSION['user']['role'] === 'doctor'
    );
}

/**
 * @return bool   Si el usuario en sesión tiene rol 'user' (normal)
 */
function is_user(): bool {
    return is_logged_in() && (
        isset($_SESSION['user']['role'])
        && $_SESSION['user']['role'] === 'user'
    );
}

/**
 * Obliga a iniciar sesión; redirige a /login si no lo está
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

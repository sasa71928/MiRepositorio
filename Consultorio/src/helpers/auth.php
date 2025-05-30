<?php
/**
 * Helper de autenticación: funciones para verificar sesión y roles
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * @return bool   Si hay un usuario en sesión
 */
function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

/**
 * @return bool   Si el usuario en sesión es administrador
 */
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] === 'admin');
}

/**
 * @return bool   Si el usuario en sesión es doctor
 */
function is_doctor(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] === 'doctor');
}

/**
 * Obliga a iniciar sesión, redirigiendo a /login si no lo está
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
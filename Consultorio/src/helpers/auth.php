<?php
// src/helpers/auth.php

// Inicia la sesión si aún no lo ha hecho
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario ha iniciado sesión
 * @return bool
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

/**
 * Redirige a login si el usuario no está autenticado
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

/**
 * Verifica que el usuario tenga el rol indicado
 * @param string $role
 */
function require_role(string $role): void {
    require_login();
    if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== $role) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        echo 'No tienes permiso para ver esta página.';
        exit;
    }
}

/**
 * Comprueba si el usuario es administrador
 * @return bool
 */
function is_admin(): bool {
    return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Comprueba si el usuario es médico
 * @return bool
 */
function is_doctor(): bool {
    return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'medico';
}

/**
 * Redirige a una ruta dentro de la aplicación
 * @param string $path
 */
function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}

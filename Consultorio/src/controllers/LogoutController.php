<?php
require_once __DIR__ . '/../helpers/auth.php';

// Asegura que BASE_URL esté definida
$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Cierra la sesión y redirige a login
 */
function handleLogout(): void {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . BASE_URL . '/');
    exit;
}
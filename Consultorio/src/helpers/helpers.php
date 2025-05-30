<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

function require_role(string $role) {
    if (!is_logged_in() || $_SESSION['user']['role'] !== $role) {
        header('HTTP/1.1 403 Forbidden');
        echo "No tienes permiso para ver esta página.";
        exit;
    }
}

function is_admin() {
    return is_logged_in() && $_SESSION['user']['role'] === 'admin';
}

function is_doctor() {
    return is_logged_in() && $_SESSION['user']['role'] === 'medico';
}

<?php
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../config/database.php';

function showLogin() {
    render('public/login');
}

function handleLogin() {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([ $_POST['username'] ]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user'] = [
            'id'   => $user['id'],
            'role' => $user['role']
        ];
        header('Location: ' . BASE_URL . '/');
        exit;
    } else {
        set_flash('error','Usuario o contraseña inválidos.');
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

function handleLogout() {
    session_destroy();
    header('Location: ' . BASE_URL . '/login');
    exit;
}

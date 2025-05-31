<?php
// src/controllers/RegistrationController.php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/functions.php';

$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Muestra el formulario de registro (o redirige si ya inició sesión)
 */
function showRegister(array $errors = []): void {
    if (is_logged_in()) {
        header('Location: ' . BASE_URL . '/');
        exit;
    }
    include __DIR__ . '/../views/public/registro.php';
    exit;
}

/**
 * Procesa el POST de registro: valida, crea usuario con rol 'user' y redirige a login
 */
function handleRegister(): void {
    $errors    = [];
    $correo    = trim($_POST['correo']     ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $username  = trim($_POST['username']   ?? '');
    $phone     = trim($_POST['phone']      ?? '');
    $contras   = $_POST['contrasena']      ?? '';
    $confirm   = $_POST['confirmar']       ?? '';

    // 1) Validaciones
    if ($firstName === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if ($lastName === '') {
        $errors[] = 'El apellido es obligatorio.';
    }
    if ($username === '' || strlen($username) < 4) {
        $errors[] = 'El nombre de usuario debe tener al menos 4 caracteres.';
    }
    if ($correo === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ingresa un correo válido.';
    }
    if ($phone === '' || !preg_match('/^[0-9]{7,15}$/', $phone)) {
        $errors[] = 'El teléfono debe contener sólo dígitos (7–15 números).';
    }
    if (strlen($contras) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if ($contras !== $confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    // 2) Verificar unicidad correo y username
    if (empty($errors)) {
        try {
            $pdo = getPDO();
            // Correo
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$correo]);
            if ($stmt->fetch()) {
                $errors[] = 'Ese correo ya está registrado.';
            }
            // Username
            $stmt2 = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
            $stmt2->execute([$username]);
            if ($stmt2->fetch()) {
                $errors[] = 'Ese nombre de usuario no está disponible.';
            }
        } catch (PDOException $ex) {
            $errors[] = 'Error en la base de datos: ' . $ex->getMessage();
        }
    }

    // 3) Si hay errores, volver a mostrar el formulario
    if (!empty($errors)) {
        showRegister($errors);
    }

    // 4) Insertar en BD
    try {
        $pdo = getPDO();
        // Obtener role_id para “user”
        $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
        $stmt->execute(['user']);
        $roleId = $stmt->fetchColumn();
        if (!$roleId) {
            // Si no existe, lo creamos
            $stmt2 = $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
            $stmt2->execute(['user', 'Usuario normal']);
            $roleId = $pdo->lastInsertId();
        }

        // Insertar el nuevo usuario
        $passwordHash = password_hash($contras, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (role_id, username, password_hash, first_name, last_name, email, phone)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $roleId,
            $username,
            $passwordHash,
            $firstName,
            $lastName,
            $correo,
            $phone
        ]);

        header('Location: ' . BASE_URL . '/login');
        exit;

    } catch (PDOException $ex) {
        $errors[] = 'No se pudo registrar el usuario. Intenta nuevamente más tarde.';
        showRegister($errors);
    }
}

<?php
// src/controllers/RegistrationController.php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/functions.php';

$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

function showRegister(): void {
    $data = [];
    $errors = [];
    include __DIR__ . '/../views/public/registro.php';
    exit;
}

function handleRegister(array $input): array {
    $errors = [];

    $data = [
        'username'            => trim($input['username'] ?? ''),
        'first_name'          => trim($input['first_name'] ?? ''),
        'last_name'           => trim($input['last_name'] ?? ''),
        'email'               => trim($input['email'] ?? ''),
        'password'            => $input['password'] ?? '',
        'confirm_password'    => $input['confirm_password'] ?? '',
        'phone'               => trim($input['phone'] ?? ''),
        'birthdate'           => $input['birthdate'] ?? '',
        'gender'              => $input['gender'] ?? null,
        'address'             => trim($input['address'] ?? ''),
        'city'                => trim($input['city'] ?? ''),
        'blood_type'          => $input['blood_type'] ?? null,
        'allergies'           => trim($input['allergies'] ?? ''),
        'chronic_diseases'    => trim($input['chronic_diseases'] ?? ''),
        'current_medications' => trim($input['current_medications'] ?? ''),
        'notify_email'        => isset($input['notify_email']) ? 1 : 0,
        'notify_sms'          => isset($input['notify_sms']) ? 1 : 0,
        'notify_whatsapp'     => isset($input['notify_whatsapp']) ? 1 : 0,
        'reminder_days'       => $input['reminder_days'] ?? '1',
    ];

    // Validaciones
    if ($data['username'] === '') {
        $errors[] = "El nombre de usuario es obligatorio.";
    }
    if ($data['first_name'] === '') {
        $errors[] = "El nombre es obligatorio.";
    }
    if ($data['last_name'] === '') {
        $errors[] = "El apellido es obligatorio.";
    }
    if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo no es válido.";
    }
    if (strlen($data['password']) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($data['password'] !== $data['confirm_password']) {
        $errors[] = "La contraseña y su confirmación no coinciden.";
    }
    if (! $data['notify_email'] && ! $data['notify_sms'] && ! $data['notify_whatsapp']) {
        $errors[] = "Debes seleccionar al menos un medio de notificación.";
    }
    if ($data['birthdate'] !== '' && ! strtotime($data['birthdate'])) {
        $errors[] = "La fecha de nacimiento no es válida.";
    }

    if (! empty($errors)) {
        $_SESSION['registro_data']   = $data;
        $_SESSION['registro_errors'] = $errors;
        return $errors;
    }

    try {
        $pdo = include __DIR__ . '/../config/database.php';

        // Validar que el usuario/correo no exista
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmtCheck->execute([$data['username'], $data['email']]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = "El nombre de usuario o el correo ya están en uso.";
            $_SESSION['registro_data']   = $data;
            $_SESSION['registro_errors'] = $errors;
            return $errors;
        }

        // Obtener ID del rol
        $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $stmtRole->execute(['user']);
        $rolRow = $stmtRole->fetch(PDO::FETCH_ASSOC);
        if (! $rolRow) {
            $errors[] = "El rol 'user' no existe en la tabla roles.";
            $_SESSION['registro_data']   = $data;
            $_SESSION['registro_errors'] = $errors;
            return $errors;
        }
        $roleId = (int) $rolRow['id'];

        // Insertar en users
        $stmtUser = $pdo->prepare(
            "INSERT INTO users 
             (role_id, username, password_hash, first_name, last_name, email, phone, birthdate, gender, address, city, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmtUser->execute([
            $roleId,
            $data['username'],
            $hash,
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['birthdate'] !== '' ? $data['birthdate'] : null,
            $data['gender'] !== '' ? $data['gender'] : null,
            $data['address'] !== '' ? $data['address'] : null,
            $data['city'] !== '' ? $data['city'] : null
        ]);

        $newUserId = $pdo->lastInsertId();

        // Insertar en medical_info
        $stmtMed = $pdo->prepare(
            "INSERT INTO medical_info 
             (user_id, blood_type, allergies, chronic_diseases, current_medications) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmtMed->execute([
            $newUserId,
            $data['blood_type']          !== null ? $data['blood_type'] : null,
            $data['allergies']           !== ''   ? $data['allergies'] : null,
            $data['chronic_diseases']    !== ''   ? $data['chronic_diseases'] : null,
            $data['current_medications'] !== ''   ? $data['current_medications'] : null
        ]);

        // Insertar en user_preferences
        $stmtPref = $pdo->prepare(
            "INSERT INTO user_preferences
             (user_id, notify_email, notify_sms, notify_whatsapp, reminder_days)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmtPref->execute([
            $newUserId,
            $data['notify_email'],
            $data['notify_sms'],
            $data['notify_whatsapp'],
            (int)$data['reminder_days']
        ]);

        unset($_SESSION['registro_data'], $_SESSION['registro_errors']);
        header('Location: ' . BASE_URL . '/login');
        exit;

    } catch (PDOException $ex) {
        $errors[] = "Error al registrar usuario: " . $ex->getMessage();
        $_SESSION['registro_data']   = $data;
        $_SESSION['registro_errors'] = $errors;
        return $errors;
    }
}

<?php
// src/controllers/ProfileController.php

require_once __DIR__ . '/../helpers/auth.php';

// Asegura que BASE_URL esté definida
$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

function showProfile(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si no está logueado, lo mandamos al login:
    if (! is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    // Obtenemos el ID del usuario en sesión:
    $userId = $_SESSION['user']['id'];

    try {
        // 1) Conexión a la BD
        $pdo = include __DIR__ . '/../config/database.php';

        // 2) Consulta datos básicos en tabla `users`
        $stmtUser = $pdo->prepare("
            SELECT u.username,
                   u.first_name,
                   u.last_name,
                   u.email,
                   u.phone,
                   u.birthdate,
                   u.address,
                   u.city,
                   u.created_at,
                   r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmtUser->execute([ $userId ]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (! $userRow) {
            throw new Exception("El usuario no existe.");
        }

        // 3) Consulta datos médicos en `medical_info`
        $stmtMed = $pdo->prepare("
            SELECT blood_type, allergies, chronic_diseases, current_medications
            FROM medical_info 
            WHERE user_id = ?
        ");
        $stmtMed->execute([ $userId ]);
        $medRow = $stmtMed->fetch(PDO::FETCH_ASSOC);
        if (! $medRow) {
            // Si no existe, devolvemos array vacío para evitar warnings
            $medRow = [
                'blood_type'          => '',
                'allergies'           => '',
                'chronic_diseases'    => '',
                'current_medications' => ''
            ];
        }

        // 4) Consulta preferencias en `user_preferences`
        $stmtPref = $pdo->prepare("
            SELECT notify_email, notify_sms, notify_whatsapp, reminder_days
            FROM user_preferences
            WHERE user_id = ?
        ");
        $stmtPref->execute([ $userId ]);
        $prefRow = $stmtPref->fetch(PDO::FETCH_ASSOC);
        if (! $prefRow) {
            $prefRow = [
                'notify_email'    => 0,
                'notify_sms'      => 0,
                'notify_whatsapp' => 0,
                'reminder_days'   => '1'
            ];
        }

        // 5) Armamos un array con todos los datos para pasar a la vista:
        $user = [
            'username'            => $userRow['username'],
            'first_name'          => $userRow['first_name'],
            'last_name'           => $userRow['last_name'],
            'email'               => $userRow['email'],
            'phone'               => $userRow['phone'],
            'birthdate'           => $userRow['birthdate'],
            'address'             => $userRow['address'],
            'city'                => $userRow['city'],
            'created_at'          => $userRow['created_at'],
            'role'                => $userRow['role_name'],

            // Datos médicos:
            'blood_type'          => $medRow['blood_type'],
            'allergies'           => $medRow['allergies'],
            'chronic_diseases'    => $medRow['chronic_diseases'],
            'current_medications' => $medRow['current_medications'],

            // Preferencias:
            'notify_email'        => $prefRow['notify_email'],
            'notify_sms'          => $prefRow['notify_sms'],
            'notify_whatsapp'     => $prefRow['notify_whatsapp'],
            'reminder_days'       => $prefRow['reminder_days'],
        ];

        // 6) Incluimos la vista y le pasamos el array $user
        include __DIR__ . '/../views/public/profile.php';
        exit;

    } catch (Exception $ex) {
        // Si hubiera error, redirigimos a página de errores o mostramos un mensaje
        $errorMsg = $ex->getMessage();
        include __DIR__ . '/../views/public/errores.php';
        exit;
    }
}

function update_personal(): void {
    session_start();
    require_once __DIR__ . '/../config/database.php';

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $input = $_POST;
    $errors = [];

    // Normalizar entradas
    $username   = trim($input['username']   ?? '');
    $firstName  = trim($input['first_name'] ?? '');
    $lastName   = trim($input['last_name']  ?? '');
    $email      = trim($input['email']      ?? '');
    $phone      = trim($input['phone']      ?? '');
    $birthdate  = trim($input['birthdate']  ?? '');
    $address    = trim($input['address']    ?? '');
    $city       = trim($input['city']       ?? '');

    // Validaciones
    if ($username === '')     $errors[] = 'El nombre de usuario es obligatorio.';
    if ($firstName === '')    $errors[] = 'El nombre es obligatorio.';
    if ($lastName === '')     $errors[] = 'El apellido es obligatorio.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El correo electrónico no es válido.';
    }
    if ($birthdate && !strtotime($birthdate)) {
        $errors[] = 'La fecha de nacimiento no es válida.';
    }

    // Validar duplicados de username o email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $userId]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "El nombre de usuario o correo ya están registrados.";
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    // Actualizar usuario
    try {
        $stmt = $pdo->prepare("
            UPDATE users SET
                username = ?,
                first_name = ?,
                last_name = ?,
                email = ?,
                phone = ?,
                birthdate = ?,
                address = ?,
                city = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $username,
            $firstName,
            $lastName,
            $email,
            $phone !== '' ? $phone : null,
            $birthdate !== '' ? $birthdate : null,
            $address !== '' ? $address : null,
            $city !== '' ? $city : null,
            $userId
        ]);

        $_SESSION['profile_success'] = 'Información actualizada correctamente.';
    } catch (PDOException $e) {
        $_SESSION['profile_errors'] = ['Error al actualizar: ' . $e->getMessage()];
    }
            // Refrescar datos del usuario en la sesión
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);


    header('Location: ' . BASE_URL . '/login');
    exit;
}

function update_medical(): void {
    session_start();
    require_once __DIR__ . '/../config/database.php';

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $bloodType = trim($_POST['blood_type'] ?? '');
    $allergies = trim($_POST['allergies'] ?? '');
    $chronic  = trim($_POST['chronic_diseases'] ?? '');
    $meds     = trim($_POST['current_medications'] ?? '');

    try {
        $stmt = $pdo->prepare("
            UPDATE medical_info SET
                blood_type = ?,
                allergies = ?,
                chronic_diseases = ?,
                current_medications = ?,
                updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([
            $bloodType !== '' ? $bloodType : null,
            $allergies !== '' ? $allergies : null,
            $chronic   !== '' ? $chronic : null,
            $meds      !== '' ? $meds : null,
            $userId
        ]);

        $_SESSION['profile_success'] = "Información médica actualizada correctamente.";
    } catch (PDOException $e) {
        $_SESSION['profile_errors'] = ["Error al actualizar: " . $e->getMessage()];
    }

    header('Location: ' . BASE_URL . '/profile');
    exit;
}


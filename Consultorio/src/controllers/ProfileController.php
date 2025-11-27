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
        // Usa tu vista de error 500 si quieres, o un simple echo por ahora
        echo "Error al cargar perfil: " . $errorMsg;
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
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    // [CORRECCIÓN CRÍTICA]: Refrescar datos de sesión INCLUYENDO EL ROL
    // Antes faltaba el JOIN con roles, lo que rompía la sesión y causaba el bucle infinito.
    $stmt = $pdo->prepare("
        SELECT u.*, r.name as role_name 
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.id = ?
    ");
    $stmt->execute([$userId]);
    $freshUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($freshUser) {
        // Reconstruimos la sesión con la misma estructura que en el Login
        $_SESSION['user'] = [
            'id'         => $freshUser['id'],
            'username'   => $freshUser['username'],
            'first_name' => $freshUser['first_name'],
            'last_name'  => $freshUser['last_name'],
            'email'      => $freshUser['email'],
            'role'       => $freshUser['role_name'], // ¡Esto era lo que faltaba!
            'phone'      => $freshUser['phone'],
        ];
    }

    // Redirigimos al perfil (mejor experiencia) en vez de al login
    header('Location: ' . BASE_URL . '/profile');
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

function change_password(): void {
    session_start();
    require_once __DIR__ . '/../config/database.php';

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $errors  = [];

    // Validaciones
    if (strlen($new) < 6) {
        $errors[] = "La nueva contraseña debe tener al menos 6 caracteres.";
    }
    if ($new !== $confirm) {
        $errors[] = "La nueva contraseña y su confirmación no coinciden.";
    }

    // Verificar contraseña actual
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($current, $row['password_hash'])) {
        $errors[] = "La contraseña actual no es correcta.";
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header('Location: ' . BASE_URL . '/profile#security');
        exit;
    }

    // Actualizar nueva contraseña
    $newHash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$newHash, $userId]);

    $_SESSION['profile_success'] = "Contraseña actualizada correctamente.";
    header('Location: ' . BASE_URL . '/profile#security');
    exit;
}

function update_preferences(): void {
    session_start();
    require_once __DIR__ . '/../config/database.php';

    $userId = $_SESSION['user']['id'] ?? null;
    if (!$userId) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $notify_email    = isset($_POST['notify_email'])    ? 1 : 0;
    $notify_sms      = isset($_POST['notify_sms'])      ? 1 : 0;
    $notify_whatsapp = isset($_POST['notify_whatsapp']) ? 1 : 0;
    $reminder_days   = $_POST['reminder_days'] ?? null;
    $errors = [];

    if (!in_array($reminder_days, ['1', '2', '7'])) {
        $errors[] = "El valor del recordatorio no es válido.";
    }

    if (!$notify_email && !$notify_sms && !$notify_whatsapp) {
        $errors[] = "Debes seleccionar al menos una opción de notificación.";
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header('Location: ' . BASE_URL . '/profile#preferences');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE user_preferences SET
                notify_email = ?, notify_sms = ?, notify_whatsapp = ?, reminder_days = ?, updated_at = NOW()
            WHERE user_id = ?
        ");
        $stmt->execute([
            $notify_email,
            $notify_sms,
            $notify_whatsapp,
            $reminder_days,
            $userId
        ]);

        $_SESSION['profile_success'] = "Preferencias actualizadas correctamente.";
    } catch (PDOException $e) {
        $_SESSION['profile_errors'] = ["Error al actualizar: " . $e->getMessage()];
    }

    header('Location: ' . BASE_URL . '/profile#preferences');
    exit;
}
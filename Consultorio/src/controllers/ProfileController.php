<?php
// src/controllers/ProfileController.php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/Logger.php'; // Importante: Logger

// Asegura que BASE_URL esté definida
$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Muestra la vista de perfil cargando los datos del usuario.
 */
function showProfile(): void {
    // [CORRECCIÓN] Evita error si la sesión ya está abierta
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (! is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    $userId = $_SESSION['user']['id'];

    try {
        // [CORRECCIÓN] Obtener conexión correctamente
        $pdo = require __DIR__ . '/../config/database.php';

        // 1. Consulta principal de datos (JOIN con roles)
        $stmtUser = $pdo->prepare("
            SELECT u.username, u.first_name, u.last_name, u.email, u.phone,
                   u.birthdate, u.address, u.city, u.created_at, r.name AS role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmtUser->execute([ $userId ]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);
        
        if (! $userRow) {
            throw new Exception("El usuario no existe.");
        }

        // 2. Datos Médicos
        $stmtMed = $pdo->prepare("SELECT blood_type, allergies, chronic_diseases, current_medications FROM medical_info WHERE user_id = ?");
        $stmtMed->execute([ $userId ]);
        $medRow = $stmtMed->fetch(PDO::FETCH_ASSOC) ?: [
            'blood_type' => '', 'allergies' => '', 'chronic_diseases' => '', 'current_medications' => ''
        ];

        // 3. Preferencias
        $stmtPref = $pdo->prepare("SELECT notify_email, notify_sms, notify_whatsapp, reminder_days FROM user_preferences WHERE user_id = ?");
        $stmtPref->execute([ $userId ]);
        $prefRow = $stmtPref->fetch(PDO::FETCH_ASSOC) ?: [
            'notify_email' => 0, 'notify_sms' => 0, 'notify_whatsapp' => 0, 'reminder_days' => '1'
        ];

        // Unimos todo en una sola variable $user
        $user = array_merge($userRow, $medRow, $prefRow);
        $user['role'] = $userRow['role_name']; 

        // Llamamos a la vista
        include __DIR__ . '/../views/public/profile.php';
        exit;

    } catch (Exception $ex) {
        error_log("Error perfil: " . $ex->getMessage());
        require __DIR__ . '/../views/public/error_500.php';
        exit;
    }
}

/**
 * Actualiza la información personal del usuario.
 */
function update_personal(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $pdo = require __DIR__ . '/../config/database.php'; // Obtener conexión

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
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo electrónico no es válido.';

    // Validar duplicados
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->execute([$username, $email, $userId]);
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "El nombre de usuario o correo ya están registrados por otro usuario.";
    }

    // [MEJORA] REGISTRO DE AUDITORÍA DE FALLO DE VALIDACIÓN
    if (!empty($errors)) {
        log_audit($userId, 'error_validacion_perfil', "Campos faltantes/inválidos: " . implode(", ", $errors));
        $_SESSION['profile_errors'] = $errors;
        header('Location: ' . BASE_URL . '/profile');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE users SET
                username = ?, first_name = ?, last_name = ?, email = ?,
                phone = ?, birthdate = ?, address = ?, city = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$username, $firstName, $lastName, $email, $phone ?: null, $birthdate ?: null, $address ?: null, $city ?: null, $userId]);

        // REFRESCAR SESIÓN (Crucial para evitar bucle de redirección)
        $stmt = $pdo->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
        $stmt->execute([$userId]);
        $fresh = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fresh) {
            $_SESSION['user'] = [
                'id' => $fresh['id'], 'username' => $fresh['username'], 'first_name' => $fresh['first_name'],
                'last_name' => $fresh['last_name'], 'email' => $fresh['email'], 'role' => $fresh['role_name'], 'phone' => $fresh['phone']
            ];
        }

        // [MEJORA] Log de éxito
        log_audit($userId, 'perfil_actualizado', 'Datos personales modificados');
        $_SESSION['profile_success'] = 'Información actualizada correctamente.';

    } catch (PDOException $e) {
        // [MEJORA] Log de error SQL
        log_audit($userId, 'error_sql_perfil', $e->getMessage());
        $_SESSION['profile_errors'] = ['Error interno al actualizar.'];
    }

    header('Location: ' . BASE_URL . '/profile');
    exit;
}

/**
 * Actualiza la información médica del usuario.
 */
function update_medical(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $pdo = require __DIR__ . '/../config/database.php';
    
    $userId = $_SESSION['user']['id'] ?? null;

    // No hay validaciones complejas en el código original, solo actualizamos.
    try {
        $stmt = $pdo->prepare("UPDATE medical_info SET blood_type=?, allergies=?, chronic_diseases=?, current_medications=?, updated_at=NOW() WHERE user_id=?");
        $stmt->execute([
            $_POST['blood_type']?:null, $_POST['allergies']?:null, 
            $_POST['chronic_diseases']?:null, $_POST['current_medications']?:null, $userId
        ]);
        
        log_audit($userId, 'perfil_medico_upd', 'Info médica actualizada');
        $_SESSION['profile_success'] = "Información médica actualizada correctamente.";
    } catch (PDOException $e) {
        log_audit($userId, 'error_sql_medico', $e->getMessage());
        $_SESSION['profile_errors'] = ["Error al actualizar: " . $e->getMessage()];
    }
    header('Location: ' . BASE_URL . '/profile');
    exit;
}

/**
 * Actualiza la contraseña del usuario.
 */
function change_password(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $pdo = require __DIR__ . '/../config/database.php';
    
    $userId = $_SESSION['user']['id'] ?? null;

    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $errors = [];

    // Validaciones
    if (strlen($new) < 6) $errors[] = "La nueva contraseña debe tener al menos 6 caracteres.";
    if ($new !== $confirm) $errors[] = "La nueva contraseña y su confirmación no coinciden.";

    if (!empty($errors)) {
        log_audit($userId, 'error_password', 'Validación fallida al cambiar contraseña');
        $_SESSION['profile_errors'] = $errors;
        header('Location: ' . BASE_URL . '/profile#security');
        exit;
    }

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($current, $row['password_hash'])) {
        log_audit($userId, 'error_password', 'Contraseña actual incorrecta');
        $_SESSION['profile_errors'] = ["La contraseña actual no es correcta."];
        header('Location: ' . BASE_URL . '/profile#security');
        exit;
    }

    $newHash = password_hash($new, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$newHash, $userId]);

    log_audit($userId, 'password_cambiado', 'Contraseña actualizada exitosamente');
    $_SESSION['profile_success'] = "Contraseña actualizada correctamente.";
    
    header('Location: ' . BASE_URL . '/profile#security');
    exit;
}

/**
 * Actualiza las preferencias de notificación del usuario.
 */
function update_preferences(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $pdo = require __DIR__ . '/../config/database.php';
    
    $userId = $_SESSION['user']['id'] ?? null;

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

    // [MEJORA] REGISTRO DE AUDITORÍA DE FALLO DE VALIDACIÓN
    if (!empty($errors)) {
        log_audit($userId, 'error_pref_val', 'Validación fallida al actualizar preferencias');
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

        // [MEJORA] Log de éxito
        log_audit($userId, 'pref_upd', 'Preferencias actualizadas');
        $_SESSION['profile_success'] = "Preferencias actualizadas correctamente.";
    } catch (PDOException $e) {
        // [MEJORA] Log de error SQL
        log_audit($userId, 'error_sql_pref', $e->getMessage());
        $_SESSION['profile_errors'] = ["Error al actualizar: " . $e->getMessage()];
    }

    header('Location: ' . BASE_URL . '/profile#preferences');
    exit;
}
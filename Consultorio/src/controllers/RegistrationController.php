<?php
/**
 * Controlador de Registro de Usuarios.
 * * Maneja la lógica de validación, creación de usuarios y asignación de roles.
 * Implementa mejoras de mantenibilidad mediante el uso de validadores externos y funciones modulares.
 * * @package CliniGest\Controllers
 * @version 2.0 (Refactorizado para Mantenibilidad ISO 25010)
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../helpers/Validator.php'; // [MEJORA] Importamos el validador

$config = include __DIR__ . '/../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

/**
 * Muestra la vista del formulario de registro.
 *
 * @return void
 */
function showRegister(): void {
    $data = [];
    $errors = [];
    include __DIR__ . '/../views/public/registro.php';
    exit;
}

/**
 * Procesa los datos del formulario de registro.
 * * @param array $input Datos provenientes de $_POST
 * @return array Lista de errores encontrados (si los hay)
 */
function handleRegister(array $input): array {
    $errors = [];

    // [MEJORA] Normalización de datos separada de la lógica principal
    $data = normalizarDatosRegistro($input);

    // [MEJORA] Uso de Validador Centralizado (Mantenibilidad: Reducción de complejidad ciclomática)
    $camposRequeridos = [
        'username' => 'Nombre de usuario',
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'email' => 'Correo',
        'password' => 'Contraseña'
    ];
    
    $errors = array_merge($errors, validar_requeridos($camposRequeridos, $data));
    
    if ($msg = validar_email($data['email'])) {
        $errors[] = $msg;
    }
    
    $errors = array_merge($errors, validar_contrasena($data['password'], $data['confirm_password']));

    if (! $data['notify_email'] && ! $data['notify_sms'] && ! $data['notify_whatsapp']) {
        $errors[] = "Debes seleccionar al menos un medio de notificación.";
    }

    // Si hay errores de validación, retornamos antes de intentar conectar a la BD
    if (! empty($errors)) {
        guardarEstadoSesion($data, $errors);
        return $errors;
    }

    try {
        $pdo = include __DIR__ . '/../config/database.php';

        // Validar si el usuario ya existe
        if (existeUsuario($pdo, $data['username'], $data['email'])) {
            $errors[] = "El nombre de usuario o el correo ya están en uso.";
            guardarEstadoSesion($data, $errors);
            return $errors;
        }

        // Crear usuario (Lógica encapsulada en una función privada para limpieza)
        crearUsuarioCompleto($pdo, $data);

        unset($_SESSION['registro_data'], $_SESSION['registro_errors']);
        header('Location: ' . BASE_URL . '/login');
        exit;

    } catch (PDOException $ex) {
        // En caso de error crítico, lo registramos pero damos un mensaje amable
        error_log("Error Registro: " . $ex->getMessage());
        $errors[] = "Ocurrió un problema al crear tu cuenta. Intenta más tarde.";
        guardarEstadoSesion($data, $errors);
        return $errors;
    }
}

// --- Funciones Auxiliares Privadas (Mejora de Modularidad) ---

/**
 * Limpia y prepara el array de datos de entrada.
 */
function normalizarDatosRegistro($input) {
    return [
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
}

/**
 * Verifica si un usuario o correo ya existen en la base de datos.
 */
function existeUsuario($pdo, $user, $email) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$user, $email]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Guarda los datos en sesión para repoblarlos si hay error.
 */
function guardarEstadoSesion($data, $errors) {
    $_SESSION['registro_data'] = $data;
    $_SESSION['registro_errors'] = $errors;
}

/**
 * Ejecuta las inserciones en las 3 tablas correspondientes.
 * Se envuelve en una transacción para integridad de datos.
 */
function crearUsuarioCompleto($pdo, $data) {
    // [MEJORA] Uso de Transacciones (Fiabilidad de datos)
    $pdo->beginTransaction();

    try {
        // 1. Obtener Rol
        $stmtRole = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
        $stmtRole->execute(['user']);
        $roleId = $stmtRole->fetchColumn();

        // 2. Insertar Usuario
        $stmtUser = $pdo->prepare(
            "INSERT INTO users (role_id, username, password_hash, first_name, last_name, email, phone, birthdate, gender, address, city, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        
        $stmtUser->execute([
            $roleId, $data['username'], password_hash($data['password'], PASSWORD_DEFAULT),
            $data['first_name'], $data['last_name'], $data['email'],
            $data['phone'] ?: null, $data['birthdate'] ?: null, $data['gender'] ?: null,
            $data['address'] ?: null, $data['city'] ?: null
        ]);

        $newUserId = $pdo->lastInsertId();

        // 3. Insertar Info Médica
        $stmtMed = $pdo->prepare(
            "INSERT INTO medical_info (user_id, blood_type, allergies, chronic_diseases, current_medications) VALUES (?, ?, ?, ?, ?)"
        );
        $stmtMed->execute([
            $newUserId, $data['blood_type'] ?: null, $data['allergies'] ?: null,
            $data['chronic_diseases'] ?: null, $data['current_medications'] ?: null
        ]);

        // 4. Insertar Preferencias
        $stmtPref = $pdo->prepare(
            "INSERT INTO user_preferences (user_id, notify_email, notify_sms, notify_whatsapp, reminder_days) VALUES (?, ?, ?, ?, ?)"
        );
        $stmtPref->execute([
            $newUserId, $data['notify_email'], $data['notify_sms'], $data['notify_whatsapp'], (int)$data['reminder_days']
        ]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack(); // Si algo falla, deshacemos todo
        throw $e; // Re-lanzamos el error para que lo capture el try-catch principal
    }
}
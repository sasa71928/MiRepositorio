<?php
// src/helpers/Logger.php

require_once __DIR__ . '/../config/database.php';

/**
 * Registra un evento en la tabla de auditoría.
 * * @param int|null $userId ID del usuario (si existe sesión)
 * @param string $action Acción realizada (ej. 'login_success', 'db_error')
 * @param string|null $details Detalles adicionales del evento
 * @return void
 */
function log_audit($userId, $action, $details = null) {
    try {
        // Importar configuración de BD localmente para asegurar conexión fresca
        $config = include __DIR__ . '/../config/config.php';
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, created_at) VALUES (:uid, :action, :details, NOW())");
        
        // Si no hay usuario (ej. error de sistema), usamos 0 o NULL según permita tu BD.
        // En tu SQL user_id es NOT NULL, así que usaremos un usuario 'sistema' o el ID 1 si es admin, 
        // o manejaremos el caso. Para este ejemplo, asegúrate de tener un usuario 'System' o permite NULL en la BD.
        // Asumiremos que si es null, intentamos guardar 0 (o ajusta tu BD para permitir NULL).
        $uid = $userId ?? 0; 

        $stmt->execute([
            ':uid' => $uid,
            ':action' => $action,
            ':details' => $details
        ]);

    } catch (Exception $e) {
        // Si falla el log, guardamos en archivo de texto como respaldo de emergencia
        error_log("[FALLO AUDIT] $action: $details - " . $e->getMessage());
    }
}
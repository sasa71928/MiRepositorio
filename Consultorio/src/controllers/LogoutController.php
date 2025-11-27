<?php
/**
 * Controlador de Cierre de Sesión.
 * Asegura un cierre de sesión seguro y registra la acción en la bitácora de auditoría.
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/Logger.php'; // Para registrar la auditoría

function handleLogout(): void {
    // 1. [MEJORA] Registrar la auditoría ANTES de destruir la sesión
    $userId = $_SESSION['user']['id'] ?? 0;
    log_audit($userId, 'logout_success', 'Cierre de sesión manual');

    // 2. Ejecutar el cierre de sesión completo
    logout_user();

    // Nota: La función logout_user() en helpers/auth.php debe ser la que destruya la sesión
}
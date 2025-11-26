<?php
/**
 * Helper para validación de datos.
 * Mejora la Mantenibilidad (ISO 25010) centralizando lógica repetitiva.
 */

function validar_requeridos(array $campos, array $datos): array {
    $errores = [];
    foreach ($campos as $campo => $nombreLegible) {
        if (empty(trim($datos[$campo] ?? ''))) {
            $errores[] = "El campo '$nombreLegible' es obligatorio.";
        }
    }
    return $errores;
}

function validar_email($email): ?string {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El formato del correo electrónico no es válido.";
    }
    return null;
}

function validar_contrasena($pass, $confirm): array {
    $errores = [];
    if (strlen($pass) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($pass !== $confirm) {
        $errores[] = "Las contraseñas no coinciden.";
    }
    return $errores;
}
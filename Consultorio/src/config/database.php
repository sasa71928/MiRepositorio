<?php
// src/config/database.php

// Carga la configuración de conexión
$config = include __DIR__ . '/config.php';

// Importar Logger manualmente si no ha sido cargado
require_once __DIR__ . '/../helpers/Logger.php';

$host     = $config['db']['host'];
$dbname   = $config['db']['name'];
$user     = $config['db']['user'];
$pass     = $config['db']['password'];
$charset  = $config['db']['charset'] ?? 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
} catch (PDOException $e) {
    // MEJORA DE FIABILIDAD:
    // 1. No mostramos el error real al usuario (seguridad).
    // 2. Registramos el error internamente.
    
    // Escribir en log de PHP (fallback)
    error_log("Error Crítico BD: " . $e->getMessage());
    
    // Redirigir a página de error amigable
    // Asegúrate que la ruta sea correcta según tu estructura
    require __DIR__ . '/../views/public/error_500.php';
    exit;
}
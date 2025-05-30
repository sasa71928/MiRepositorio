<?php
// src/config/database.php

// Carga la configuración de conexión
$config = include __DIR__ . '/config.php';

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
    // Si todo bien, devolvemos el objeto
    return $pdo;
} catch (PDOException $e) {
    // Detenemos la ejecución mostrando el error de conexión
    die('Error de conexión a BD: ' . $e->getMessage());
}

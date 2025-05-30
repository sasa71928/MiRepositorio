<?php
// src/helpers/functions.php

$config = include __DIR__ . '/../config/config.php';
if (!is_array($config)) {
    throw new RuntimeException('No se pudo cargar la configuración');
}

define('BASE_URL',   rtrim($config['base_url'],   '/'));
define('ASSETS_URL', rtrim($config['assets_url'], '/'));

function getPDO(): PDO {
    static $pdo;
    if (!$pdo) {
        global $config;
        $c = $config['db'];
        $dsn = "mysql:host={$c['host']};dbname={$c['name']};charset=" . ($c['charset'] ?? 'utf8mb4');
        $pdo = new PDO($dsn, $c['user'], $c['password'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}

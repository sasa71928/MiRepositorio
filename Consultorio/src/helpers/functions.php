<?php
/**
 * src/helpers/functions.php
 * 
 * Aquí definimos todas las constantes globales (BASE_URL, ASSETS_URL, etc.)
 * y cualquier otra función “de apoyo” que usemos en todo el proyecto.
 */

// 1) Cargamos la configuración general
$config = include __DIR__ . '/../config/config.php';

// 2) Definimos BASE_URL si no existía
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}

// 3) Definimos ASSETS_URL si no existía
if (! defined('ASSETS_URL')) {
    // asumimos que en config.php hay algo como 'assets_url' => 'http://localhost/ProyectoConsultorio/...'
    define('ASSETS_URL', rtrim($config['assets_url'], '/'));
}

/**
 * Aquí podrías agregar otras funciones de utilidad, por ejemplo:
 */

if (! function_exists('dd')) {
    /**
     * Ejemplo de “dump and die” para depuración.
     */
    function dd($var) {
        echo '<pre>'; var_dump($var); echo '</pre>';
        exit;
    }
}

// Si tienes más funciones auxiliares, colócalas aquí...

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

<?php
// public/index.php

require_once __DIR__ . '/../src/helpers/functions.php';
require_once __DIR__ . '/../src/helpers/auth.php';

$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME']));
$request  = (strpos($uri, $basePath) === 0)
             ? substr($uri, strlen($basePath))
             : $uri;

if ($request === '' || $request === false) {
    $request = '/';
}

switch ($request) {
    case '/':
        include __DIR__ . '/../src/views/public/welcome.php';
        break;

    case '/login':
        require_once __DIR__ . '/../src/controllers/LoginController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            handleLogin();
        } else {
            showLogin();
        }
        break;

    case '/logout':
        require_once __DIR__ . '/../src/controllers/LogoutController.php';
        handleLogout();
        break;

    default:
        http_response_code(404);
        include __DIR__ . '/errores.php';
        break;
}

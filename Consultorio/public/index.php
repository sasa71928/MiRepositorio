<?php
// 1) Obtén sólo la ruta (sin query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// 2) Calcula el prefijo hasta public
$basePath = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME']));
// 3) Quita el prefijo
if (strpos($uri, $basePath) === 0) {
    $request = substr($uri, strlen($basePath));
} else {
    $request = $uri;
}
// 4) Normaliza vacío a '/'
if ($request === '' || $request === false) {
    $request = '/';
}

switch ($request) {
    case '/':
        require_once __DIR__ . '/../src/views/public/welcome.php';
        break;

    case '/login':
        require_once __DIR__ . '/../src/helpers/auth.php';
        require_once __DIR__ . '/../src/controllers/LoginController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = handleLogin($_POST['correo'], $_POST['contrasena']);
            include __DIR__ . '/login.php';
        } else {
            showLogin();
        }
        break;

    case '/logout':
        require_once __DIR__ . '/../src/controllers/LogoutController.php';
        handleLogout();
        break;

    default:
        require_once __DIR__ . '/errores.php';
        break;
}

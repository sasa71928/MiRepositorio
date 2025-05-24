<?php
// 1) Obtén sólo la ruta (sin query string)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 2) Averigua el prefijo hasta tu carpeta public,
//    p.ej. "/ProyectoConsultorio/Consultorio/public"
$basePath = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME']));

// 3) Le quitas ese prefijo a la URI
if (strpos($uri, $basePath) === 0) {
    $request = substr($uri, strlen($basePath));
} else {
    $request = $uri;
}

// 4) Si quedó vacío, lo convertimos en "/" para que case correctamente
if ($request === '' || $request === false) {
    $request = '/';
}

switch($request){
    case '/':
        require_once __DIR__.'/../src/views/public/welcome.php';
        break;
    case '/login':
        require_once __DIR__.'/login.php';
        break;
    case '/products':
        require_once __DIR__.'/../src/views/admin/products/index.php';
        break;
    case '/products/form':
        require_once __DIR__.'/../src/views/admin/products/form.php';
        break;
    case '/logout':
        require_once __DIR__.'/../src/controllers/LogoutController.php';
        break;  
    default:
    require_once __DIR__.'/errores.php';
        break;
}
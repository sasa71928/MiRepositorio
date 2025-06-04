<?php
// public/index.php

// 1) Cargar configuración global
$config = include __DIR__ . '/../src/config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}
if (! defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim($config['assets_url'], '/'));
}

// 2) Obtener la ruta solicitada (sin query string)
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME']));

// 3) Quitar el prefijo de “/ProyectoConsultorio/Consultorio/public”
if (strpos($uri, $basePath) === 0) {
    $request = substr($uri, strlen($basePath));
} else {
    $request = $uri;
}

// 4) Normalizar ruta vacía a "/"
if ($request === '' || $request === false) {
    $request = '/';
}

switch ($request) {
    case '/':
        require_once __DIR__ . '/../src/views/public/welcome.php';
        break;

    case '/login':
        require_once __DIR__ . '/../src/controllers/LoginController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = handleLogin($_POST['correo'], $_POST['contrasena']);
            // after POST, vuelve a mostrar la vista con $error
            include __DIR__ . '/login.php';
        } else {
            showLogin();
        }
        break;

    case '/logout':
        require_once __DIR__ . '/../src/controllers/LogoutController.php';
        handleLogout();
        break;

    case '/registro':
        require_once __DIR__ . '/../src/controllers/RegistrationController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = handleRegister($_POST);
            // Si hubo errores, cargamos la vista pasándole el arreglo $errors y $postedData
            include __DIR__ . '/../src/views/public/registro.php';
        } else {
            showRegister();
        }
        break;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST; // todo el array enviado
            $error = handleRegister($data);
            // luego de procesar POST, recargamos la misma vista para mostrar errores (si hay)
            include __DIR__ . '/registro.php';
        } else {
            // simplemente muestro formulario
            include __DIR__ . '/registro.php';
        }
        break;
    case '/profile':
        require_once __DIR__ . '/../src/helpers/auth.php';
        require_login(); // obligar a iniciar sesión
        require_once __DIR__ . '/../src/controllers/ProfileController.php';
        showProfile();
        break;
    case '/update_personal':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/controllers/ProfileController.php';
            update_personal();
        } else {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
        break;
    case '/update_medical':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/controllers/ProfileController.php';
            update_medical();
        } else {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
        break;

    case '/change_password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/controllers/ProfileController.php';
            change_password();
        } else {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
        break;

    case '/update_preferences':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../src/controllers/ProfileController.php';
            update_preferences();
        } else {
            header('Location: ' . BASE_URL . '/profile');
            exit;
        }
        break;
    case '/recuperarContrasena':
            require_once __DIR__ . '/../src/views/public/recuperarContrasena.php';
            break;
    case '/recuperarContrasena/enviarCodigo':
        require_once __DIR__ . '/../src/controllers/RecoverPasswordController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            solicitar_codigo();
        }else {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        break;
    case '/recuperarContrasena/verificarCodigo':
    require_once __DIR__ . '/../src/controllers/RecoverPasswordController.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verificar_codigo();
    }else {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
    break;
    case '/recuperarContrasena/resetPassword':
        require_once __DIR__ . '/../src/controllers/RecoverPasswordController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            reset_password();
        } else {
            header('Location: ' . BASE_URL . '/');
            exit;
        }
        break;
    default:
        // Cualquier otra ruta → 404
        require_once __DIR__ . '/errores.php';
        break;
}

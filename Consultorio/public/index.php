<?php
session_start(); // NECESARIO para que $_SESSION funcione


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
            require_once __DIR__ . '/../src/helpers/auth.php';
            if (is_logged_in()) {
                if (is_admin()) {
                    header('Location: ' . BASE_URL . '/adminDoctors');
                } elseif (is_doctor()) {
                    header('Location: ' . BASE_URL . '/doctor-home');
                } else {
                    header('Location: ' . BASE_URL . '/appointments/mine');
                }
                exit;
            }

            require_once __DIR__ . '/../src/views/public/welcome.php';
        break;

        case '/login':
            require_once __DIR__ . '/../src/controllers/LoginController.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $error = handleLogin($_POST['correo'], $_POST['contrasena']);
                // after POST, vuelve a mostrar la vista con $error
                include __DIR__ . '/../src/views/public/login.php';
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
        case '/adminDoctors':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();
            if ($_SESSION['user']['role'] !== 'admin') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/AdminController.php';
            $totalDoctors = obtenerTotalDoctores();
            $totalPatients = obtenerTotalPacientes();
            require_once __DIR__ . '/../src/views/admin/vistaAdmin.php';
        break;

        case '/adminDoctors/gestionar':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();
            if ($_SESSION['user']['role'] !== 'admin') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }
            require_once __DIR__ . '/../src/views/admin/doctors/gestionDoctores.php';
        break;

        // [CORRECCIÓN] Devolver JSON para que el modal muestre éxito/error
        case '/adminDoctors/crearDoctor':
            require_once __DIR__ . '/../src/controllers/AdminController.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (crearDoctor($_POST)) {
                    // Éxito: 200 OK
                    http_response_code(200);
                    echo json_encode(['status' => 'success']);
                } else {
                    // Error: 500 Internal Server Error
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo crear el doctor']);
                }
                exit; 
            }
        break;

        // [CORRECCIÓN] Devolver JSON para que el modal muestre éxito/error
        case '/adminDoctors/editarDoctor':
            require_once __DIR__ . '/../src/controllers/AdminController.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (editarDoctor($_POST)) {
                    // Éxito
                    http_response_code(200);
                    echo json_encode(['status' => 'success']);
                } else {
                    // Error
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el doctor']);
                }
                exit;
            }
        break;

        // [NUEVO] Ruta para cambiar estado (Suspender/Activar)
        case '/adminDoctors/cambiarEstado':
            require_once __DIR__ . '/../src/controllers/AdminController.php';
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $id = $_POST['id'] ?? 0;
                $estado = $_POST['estado'] ?? 1;
                
                if (cambiarEstadoDoctor($id, $estado)) {
                    http_response_code(200);
                    echo json_encode(['status' => 'success']);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error']);
                }
                exit;
            }
        break;

        case '/appointments/create':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'user') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/AppointmentController.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = $_POST;
                $data['user_id'] = $_SESSION['user']['id'];
                $resultado = crearCita($data);

                if (isset($resultado['success'])) {
                    header('Location: ' . BASE_URL . '/appointments/create?success=1');
                } else {
                    $msg = urlencode($resultado['error']);
                    header('Location: ' . BASE_URL . '/appointments/create?error=1&msg=' . $msg);
                }
                exit;
            } else {
                include __DIR__ . '/../src/views/appointments/crearCita.php';
            }
        break;

        case '/appointments/mine':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'user') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/AppointmentController.php';
            $misCitas = obtenerCitasPorUsuario($_SESSION['user']['id']);

            include __DIR__ . '/../src/views/appointments/misCitas.php';
        break;

        case '/appointments/cancelar':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'user') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                header('Location: ' . BASE_URL . '/appointments/mine');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/AppointmentController.php';
            cancelarCita((int)$_GET['id'], $_SESSION['user']['id']);

            header('Location: ' . BASE_URL . '/appointments/mine');
        exit;

        case '/ratings/valoraciones':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'user') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/RatingController.php';
            require_once __DIR__ . '/../src/controllers/AppointmentController.php';

            $userId = $_SESSION['user']['id'];
            $valoraciones = obtenerValoracionesUsuario($userId);
            $citasPendientes = obtenerCitasCompletadasNoValoradas($userId);

            include __DIR__ . '/../src/views/ratings/valoraciones.php';
            break;
        
            case '/ratings/guardarValoracion':
            require_once __DIR__ . '/../src/helpers/auth.php'; 
            require_once __DIR__ . '/../src/controllers/RatingController.php';
            require_login();

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                guardarValoracion($_POST);
                header('Location: ' . BASE_URL . '/ratings/valoraciones?success=1');
                exit;
            }
        break;

        case '/doctor-home':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/DoctorController.php';
            $citas = obtenerCitasDelDoctor($_SESSION['user']['id']);

            require_once __DIR__ . '/../src/views/doctors/doctorVista.php';
        break;

        case '/consultaCita':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                header('Location: ' . BASE_URL . '/doctor-home'); // o error 404
                exit;
            }

            require_once __DIR__ . '/../src/controllers/DoctorController.php';
            $cita = obtenerCitaPorIdYDoctor($_GET['id'], $_SESSION['user']['id']);

            if (!$cita) {
                header('Location: ' . BASE_URL . '/doctor-home');
                exit;
            }

            require_once __DIR__ . '/../src/views/doctors/consultaCita.php';
        break;

        case '/doctor/cancelarCita':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if ($_SESSION['user']['role'] !== 'doctor') {
                header('Location: ' . BASE_URL . '/');
                exit;
            }

            if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
                header('Location: ' . BASE_URL . '/doctor-home');
                exit;
            }

            require_once __DIR__ . '/../src/controllers/DoctorController.php';
            cancelarCitaDoctor((int)$_GET['id'], $_SESSION['user']['id']);

            header('Location: ' . BASE_URL . '/doctor-home');
            exit;
        break;

        case '/appointments/complete':
            require_once __DIR__ . '/../src/controllers/AppointmentController.php';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $resultado = completarConsulta($_POST);

                if (isset($resultado['success'])) {
                    header('Location: ' . BASE_URL . '/doctor-home');
                } else {
                    $msg = urlencode($resultado['error']);
                    header('Location: ' . BASE_URL . '/appointments/complete-error?msg=' . $msg);
                }
                exit;
            }
        break;

        case '/listaPacientes':
            require_once __DIR__ . '/../src/controllers/AppointmentController.php';
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            $doctorId = $_SESSION['user']['id'];
            $pacientes = obtenerPacientesDelDoctor($doctorId);

            // Aquí agregas las citas a cada paciente
            foreach ($pacientes as &$paciente) {
                $paciente['citas'] = obtenerCitasPorUsuario($paciente['id']);
            }

            include __DIR__ . '/../src/views/doctors/listaPacientes.php';
        break;

        case '/departamento':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if (!is_admin()) {
                header('Location: ' . BASE_URL);
                exit;
            }

            require_once __DIR__ . '/../src/controllers/AdminController.php';
            $departamentos = obtenerDepartamentos(); // <- aquí cargas los datos

            include __DIR__ . '/../src/views/admin/departamentos.php'; // <- solo se carga la vista
        break;

        case '/departamento/crear':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../src/controllers/AdminController.php';
                crearDepartamento($_POST['nombre']);
            }

            header('Location: ' . BASE_URL . '/departamento');
        exit;

        case '/departamento/editar':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../src/controllers/AdminController.php';
                editarDepartamento($_POST['id'], $_POST['nombre']);
            }

            header('Location: ' . BASE_URL . '/departamento');
        exit;

        case '/departamento/eliminar':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if (is_admin() && $_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once __DIR__ . '/../src/controllers/AdminController.php';
                eliminarDepartamento($_POST['id']);
            }

            header('Location: ' . BASE_URL . '/departamento');
        exit;

        case '/reportesAdmin':
            require_once __DIR__ . '/../src/helpers/auth.php';
            require_login();

            if (!is_admin()) {
                header('Location: ' . BASE_URL);
                exit;
            }

            require_once __DIR__ . '/../src/controllers/ReporteController.php';
            $reporte = generarReporteGeneral(); // función que retorna datos agregados

            include __DIR__ . '/../src/views/admin/reportes.php';
        break;

        case '/reporte/enviar':
            require_once __DIR__ . '/../src/controllers/ReportePDFController.php';
            $correo = $_SESSION['user']['email'] ?? 'osalazarsalas@gmail.com';

            if (enviarReporteResumen($correo)) {
                header('Location: ' . BASE_URL . '/');
            } else {
                header('Location: ' . BASE_URL . '/');
            }
        exit;

    default:
        require_once __DIR__ . '/errores.php';
    break;
}

<?php
require_once __DIR__ . '/../controllers/DoctorController.php';
require_once __DIR__ . '/../helpers/auth.php';

require_login();
$doctorId = $_SESSION['user']['id'];

if (!isset($_GET['id'])) {
    header('Location: doctorHome.php');
    exit;
}

$citaId = intval($_GET['id']);

if (cancelarCita($citaId, $doctorId)) {
    // opcional: mensaje flash o log
}

header('Location: doctorHome.php');
exit;

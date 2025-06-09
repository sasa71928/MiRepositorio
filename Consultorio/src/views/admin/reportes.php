<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

$departamentos = obtenerDepartamentosConDoctores();
$totalConsultas = contarTotalConsultas();
$nuevosPacientes = contarNuevosPacientes();
$ingresosTotales = calcularIngresosTotales();
$doctoresActivos = obtenerDoctoresMasActivos();
?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
<main class="main">
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <section class="content-header">
                <div class="header-title">
                    <h1>Reportes y Estadísticas</h1>
                    <p>Visualiza el rendimiento de la clínica</p>
                </div>
            </section>

            <section class="stats-section">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Total Consultas</h3>
                            <p><?= $totalConsultas ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Nuevos Pacientes</h3>
                            <p><?= $nuevosPacientes ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>Ingresos</h3>
                            <p>$<?= number_format($ingresosTotales, 2) ?></p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="reports-container">
                <section class="report-section">
                    <div class="section-header">
                        <h2>Doctores más Activos</h2>
                    </div>
                    <div class="table-container">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Doctor</th>
                                    <th>Departamento</th>
                                    <th>Consultas</th>
                                    <th>Valoración</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($doctoresActivos as $doc): ?>
                                <tr>
                                    <td><?= htmlspecialchars($doc['nombre']) ?></td>
                                    <td><?= htmlspecialchars($doc['departamento']) ?></td>
                                    <td><?= $doc['consultas'] ?></td>
                                    <td>
                                        <div class="rating">
                                            <span class="rating-value"><?= number_format($doc['valoracion'], 1) ?></span>
                                            <div class="stars">
                                                <?php
                                                $entera = floor($doc['valoracion']);
                                                $decimal = $doc['valoracion'] - $entera;
                                                for ($i = 0; $i < $entera; $i++) echo '<i class="fas fa-star"></i>';
                                                if ($decimal >= 0.5) echo '<i class="fas fa-star-half-alt"></i>';
                                                ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <form action="<?= BASE_URL ?>/reporte/enviar" method="POST">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-pdf"></i> Generar y Enviar
                            </button>
                        </form>

                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

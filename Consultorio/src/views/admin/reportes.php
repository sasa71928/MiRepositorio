<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

// Obtener datos
$departamentos = obtenerDepartamentosConDoctores();
$totalConsultas = contarTotalConsultas();
$nuevosPacientes = contarNuevosPacientes();
$ingresosTotales = calcularIngresosTotales();
$doctoresActivos = obtenerDoctoresMasActivos();
$statsLogs = obtenerEstadisticasLogs();

// Datos para Gráficas
$nombresDoctores = array_column($doctoresActivos, 'nombre');
$consultasDoctores = array_column($doctoresActivos, 'consultas');

$nombresDeptos = array_column($departamentos, 'name');
$totalDeptos = array_column($departamentos, 'total_doctores');

// Datos Logs
$labelsLogs = [];
$dataLogs = [];
$colorsLogs = [];

foreach ($statsLogs as $log) {
    $accion = ucwords(str_replace('_', ' ', $log['action']));
    $labelsLogs[] = $accion;
    $dataLogs[] = $log['total'];
    
    if (str_contains(strtolower($accion), 'error') || str_contains(strtolower($accion), 'failed')) {
        $colorsLogs[] = 'rgba(220, 53, 69, 0.7)'; // Rojo
    } elseif (str_contains(strtolower($accion), 'creado') || str_contains(strtolower($accion), 'success') || str_contains(strtolower($accion), 'completada')) {
        $colorsLogs[] = 'rgba(40, 167, 69, 0.7)'; // Verde
    } else {
        $colorsLogs[] = 'rgba(54, 162, 235, 0.7)'; // Azul
    }
}
?>

<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Estilos para las pestañas */
    .tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
    }
    .tab-btn {
        padding: 10px 20px;
        cursor: pointer;
        background: #f8f9fa;
        border: 1px solid #ddd;
        border-bottom: none;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
        font-weight: 600;
        color: #555;
        transition: all 0.3s;
    }
    .tab-btn:hover {
        background: #e9ecef;
    }
    .tab-btn.active {
        background: #fff;
        color: #1977cc;
        border-bottom: 1px solid #fff;
        margin-bottom: -1px;
    }
    .tab-content {
        display: none;
        padding: 20px;
        background: #fff;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 5px 5px;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<main class="main">
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <section class="content-header">
                <div class="header-title">
                    <h1>Reportes y Estadísticas</h1>
                    <p>Visualiza el rendimiento y la seguridad de la clínica</p>
                </div>
            </section>

            <!-- Pestañas de Navegación -->
            <div class="tabs">
                <div class="tab-btn active" onclick="openTab(event, 'tab-general')">General</div>
                <div class="tab-btn" onclick="openTab(event, 'tab-audit')">Auditoría del Sistema</div>
                <div class="tab-btn" onclick="openTab(event, 'tab-listado')">Listado Detallado</div>
            </div>

            <!-- Contenido Pestaña 1: General -->
            <div id="tab-general" class="tab-content active">
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                            <div class="stat-content"><h3>Total Consultas</h3><p><?= $totalConsultas ?></p></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-content"><h3>Nuevos Pacientes</h3><p><?= $nuevosPacientes ?></p></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                            <div class="stat-content"><h3>Ingresos</h3><p>$<?= number_format($ingresosTotales, 2) ?></p></div>
                        </div>
                    </div>
                </section>

                <section class="charts-section" style="margin-top: 30px;">
                    <div class="charts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">
                        <div class="chart-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <h3>Top Doctores (Consultas)</h3>
                            <canvas id="chartDoctores"></canvas>
                        </div>
                        <div class="chart-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                            <h3>Distribución por Departamento</h3>
                            <canvas id="chartDeptos" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Contenido Pestaña 2: Auditoría -->
            <div id="tab-audit" class="tab-content">
                <div class="chart-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3>Actividad del Sistema (Auditoría)</h3>
                    <p style="font-size: 14px; color: #666; margin-bottom: 15px;">Frecuencia de eventos registrados (éxitos vs errores)</p>
                    <div style="height: 400px;">
                        <canvas id="chartLogs"></canvas>
                    </div>
                </div>
            </div>

            <!-- Contenido Pestaña 3: Listado -->
            <div id="tab-listado" class="tab-content">
                <section class="report-section">
                    <div class="section-header"><h2>Detalle de Doctores</h2></div>
                    <div class="table-container">
                        <table class="report-table">
                            <thead>
                                <tr><th>Doctor</th><th>Departamento</th><th>Consultas</th><th>Valoración</th></tr>
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
                        <br>
                        <form action="<?= BASE_URL ?>/reporte/enviar" method="POST">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-pdf"></i> Generar y Enviar Reporte
                            </button>
                        </form>
                    </div>
                </section>
            </div>

        </div>
    </div>
</main>

<script>
    // Función para cambiar pestañas
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
    }

    // --- Gráficas ---
    const ctxDoc = document.getElementById('chartDoctores').getContext('2d');
    new Chart(ctxDoc, {
        type: 'bar',
        data: {
            labels: <?= json_encode($nombresDoctores) ?>,
            datasets: [{
                label: 'Consultas Atendidas',
                data: <?= json_encode($consultasDoctores) ?>,
                backgroundColor: 'rgba(25, 119, 204, 0.7)',
                borderColor: 'rgba(25, 119, 204, 1)',
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    const ctxDep = document.getElementById('chartDeptos').getContext('2d');
    new Chart(ctxDep, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($nombresDeptos) ?>,
            datasets: [{
                data: <?= json_encode($totalDeptos) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
                hoverOffset: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });

    const ctxLogs = document.getElementById('chartLogs').getContext('2d');
    new Chart(ctxLogs, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labelsLogs) ?>,
            datasets: [{
                label: 'Cantidad de Eventos',
                data: <?= json_encode($dataLogs) ?>,
                backgroundColor: <?= json_encode($colorsLogs) ?>,
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: { legend: { display: false } }
        }
    });
</script>
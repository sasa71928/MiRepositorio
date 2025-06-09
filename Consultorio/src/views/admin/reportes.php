<?php include_once __DIR__.'/../layouts/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Reportes</title>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <main class="main">
        <div class="dashboard-container">
            <!-- Contenido Principal -->
            <div class="dashboard-content">
                <!-- Sección de arriba -->
                <section class="content-header">
                    <div class="header-title">
                        <h1>Reportes y Estadísticas</h1>
                        <p>Visualiza el rendimiento de la clínica</p>
                    </div>
                </section>
            
                <!-- Resumen de Estadísticas -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Total Consultas</h3>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Nuevos Pacientes</h3>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Ingresos</h3>
                            </div>
                        </div>

                    </div>
                </section>
                
                <!-- Tablas de Reportes -->
                <div class="reports-container">
                    <!-- Doctores más Activos -->
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
                                    <tr>
                                        <td>Dr. Carlos Rodríguez</td>
                                        <td>Medicina General</td>
                                        <td>42</td>
                                        <td>
                                            <div class="rating">
                                                <span class="rating-value">4.8</span>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dra. Ana Martínez</td>
                                        <td>Pediatría</td>
                                        <td>38</td>
                                        <td>
                                            <div class="rating">
                                                <span class="rating-value">4.9</span>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dr. Miguel Sánchez</td>
                                        <td>Cardiología</td>
                                        <td>35</td>
                                        <td>
                                            <div class="rating">
                                                <span class="rating-value">4.7</span>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Dra. Laura Gómez</td>
                                        <td>Dermatología</td>
                                        <td>32</td>
                                        <td>
                                            <div class="rating">
                                                <span class="rating-value">4.6</span>
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star-half-alt"></i>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    
                    <!-- Servicios más Solicitados -->
                    <section class="report-section">
                        <div class="section-header">
                            <h2>Servicios más Solicitados</h2>
                        </div>
                        <div class="table-container">
                            <table class="report-table">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Departamento</th>
                                        <th>Solicitudes</th>
                                        <th>Ingresos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Consulta General</td>
                                        <td>Medicina General</td>
                                        <td>86</td>
                                        <td>$12,900</td>
                                    </tr>
                                    <tr>
                                        <td>Revisión Pediátrica</td>
                                        <td>Pediatría</td>
                                        <td>64</td>
                                        <td>$9,600</td>
                                    </tr>
                                    <tr>
                                        <td>Análisis de Sangre</td>
                                        <td>Laboratorio</td>
                                        <td>58</td>
                                        <td>$8,700</td>
                                    </tr>
                                    <tr>
                                        <td>Radiografía</td>
                                        <td>Imagenología</td>
                                        <td>42</td>
                                        <td>$10,500</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>   
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Administración</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <main class="main">

        <?php include_once __DIR__.'/../layouts/header.php'; ?>
        <div class="dashboard-container">
            
            <!--Contenido Principal-->
            <div class="dashboard-content">
                <!-- Seccion de bienvenida -->
                <section class="welcome-section">
                    <div class="welcome-header">
                        <div>
                            <h1>Bienvenido, Administrador</h1>
                        </div>
                    </div>
                </section>
                
                <!-- Seccion de estadisticas -->
                <section class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Total Doctores</h3>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-content">
                                <h3>Total Pacientes</h3>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Accesos Rápidos -->
                <section class="quick-access-section">
                    <div class="section-header">
                        <h2>Accesos Rápidos</h2>
                    </div>
                    
                    <div class="quick-access-grid">
                        <a href="gestionDoctores.php" class="quick-access-card">
                            <div class="quick-access-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <h3>Gestionar Doctores</h3>
                            <p>Agregar, editar o eliminar doctores del sistema</p>
                        </a>
                        
                        <a href="reportesAdmin.php" class="quick-access-card">
                            <div class="quick-access-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3>Ver Reportes</h3>
                            <p>Consultar estadísticas y reportes de la clínica</p>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
</body>
</html>
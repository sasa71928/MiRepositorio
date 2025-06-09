<?php
include_once __DIR__.'/../layouts/header.php';

$pacienteNombre = htmlspecialchars($cita['first_name'] . ' ' . $cita['last_name']);
$pacienteTelefono = htmlspecialchars($cita['phone']);
$fecha = date('d/m/Y', strtotime($cita['scheduled_at']));
$hora = date('H:i', strtotime($cita['scheduled_at']));
$estado = htmlspecialchars($cita['status']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Consulta Médica</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/doctor.css" />
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include_once __DIR__.'/../layouts/header.php'; ?>

    <main class="main">
        <!-- Sección de consulta médica -->
        <section id="consulta-medica" class="consulta-medica section">
            <div class="container">
                <div class="page-header">
                    <div class="back-button">
                        <a href="<?= BASE_URL ?>" class="btn-outline"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
                    </div>
                    <h2>Cita Médica</h2>
                </div>

                <div class="consulta-container">
                    <!-- Información del paciente -->
                    <div class="consulta-card">
                        <div class="card-header">
                            <h3><i class="fas fa-user-injured"></i> Información del Paciente</h3>
                        </div>
                        <div class="card-body">
                            <div class="patient-summary">
                                <div class="patient-info">
                                    <h4><?= $pacienteNombre ?></h4>
                                    <p><i class="fas fa-phone"></i> <?= $pacienteTelefono ?></p>
                                </div>
                                <div class="appointment-info">
                                    <div class="appointment-date">
                                        <span class="info-label">Fecha de Cita:</span>
                                        <span class="info-value"><?= $fecha ?></span>
                                    </div>
                                    <div class="appointment-time">
                                        <span class="info-label">Hora:</span>
                                        <span class="info-value"><?= $hora ?></span>
                                    </div>
                                    <div class="appointment-status">
                                        <span class="info-label">Estado:</span>
                                        <span class="status <?= $estado ?>"><?= ucfirst($estado) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de consulta -->
                    <form id="consulta-form" class="consulta-form" method="post" action="<?= BASE_URL ?>/appointments/complete">
                        <div class="consulta-card">
                            <div class="card-header">
                                <h3><i class="fas fa-stethoscope"></i> Registro de Consulta</h3>
                            </div>
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($cita['id']) ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="padecimiento">Padecimiento / Enfermedad <span class="required">*</span></label>
                                    <textarea id="padecimiento" name="padecimiento" rows="3" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="medicamento">Medicamento <span class="required">*</span></label>
                                    <textarea id="medicamento" name="medicamento" rows="3" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="instrucciones">Instrucciones del Tratamiento <span class="required">*</span></label>
                                    <textarea id="instrucciones" name="instrucciones" rows="4" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="observaciones">Observaciones Adicionales</label>
                                    <textarea id="observaciones" name="observaciones" rows="3"></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn-primary btn-large"><i class="fas fa-check-circle"></i> Completar Consulta</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Script para la funcionalidad de la consulta -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const consultaForm = document.getElementById('consulta-form');
        const btnGuardar = document.getElementById('btn-guardar');
        const btnImprimir = document.getElementById('btn-imprimir');
        
            e.preventDefault();
            
        // Manejar el guardado de borrador
        btnGuardar.addEventListener('click', function() {
            // Aquí se implementaría la lógica para guardar un borrador
            alert('Borrador guardado correctamente.');
        });
        
        // Manejar la impresión
        btnImprimir.addEventListener('click', function() {
            window.print();
        });
    });
    </script>

</body>
</html>

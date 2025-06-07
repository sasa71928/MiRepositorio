<?php
$proximas = [];
$pasadas = [];
$canceladas = [];

foreach ($misCitas as $cita) {
    $timestamp = strtotime($cita['scheduled_at']);

    if ($cita['status'] === 'cancelada') {
        $canceladas[] = $cita;
    } elseif ($cita['status'] === 'completada') {
        $pasadas[] = $cita;
    } elseif ($cita['status'] === 'pendiente') {
        if ($timestamp >= time()) {
            $proximas[] = $cita;
        } else {
            $pasadas[] = $cita;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - CliniGest</title>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    <main class="main">
        <section class="appointments-section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Mis Citas</h2>
                    <p>Gestiona tus citas médicas programadas</p>
                </div>
                
                <div class="appointments-container">
                    <div class="appointments-header">
                        <div class="appointments-tabs">
                            <button class="tab-btn active" data-tab="upcoming">Próximas</button>
                            <button class="tab-btn" data-tab="past">Pasadas</button>
                            <button class="tab-btn" data-tab="canceled">Canceladas</button>
                        </div>
                        <a href="<?= BASE_URL ?>/appointments/create" class="btn btn-primary">Nueva Cita</a>
                    </div>
                    <div class="appointments-content">
                            <div id="upcoming" class="tab-content active">
                            <div class="appointments-list">
                                <?php if (empty($proximas)): ?>
                                <p style="text-align:center;">No tienes citas próximas.</p>
                                <?php else: ?>
                                <?php foreach ($proximas as $cita): ?>
                                    <div class="appointment-card">
                                    <div class="appointment-date">
                                        <div class="date-day"><?= date('d', strtotime($cita['scheduled_at'])) ?></div>
                                        <div class="date-month"><?= date('M', strtotime($cita['scheduled_at'])) ?></div>
                                        <div class="date-year"><?= date('Y', strtotime($cita['scheduled_at'])) ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <h3><?= $cita['reason'] ?: 'Consulta médica' ?></h3>
                                        <div class="appointment-info">
                                        <p><strong>Doctor:</strong> Dr. <?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?></p>
                                        <p><strong>Departamento:</strong> <?= htmlspecialchars($cita['departamento']) ?></p>
                                        <p><strong>Hora:</strong> <?= date('h:i A', strtotime($cita['scheduled_at'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="<?= BASE_URL ?>/appointments/cancelar?id=<?= $cita['id'] ?>" class="btn btn-danger">Cancelar</a>
                                    </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            </div>
                     
                            <div id="past" class="tab-content">
                            <div class="appointments-list">
                                <?php if (empty($pasadas)): ?>
                                <p style="text-align:center;">No tienes citas pasadas.</p>
                                <?php else: ?>
                                <?php foreach ($pasadas as $cita): ?>
                                    <div class="appointment-card past">
                                    <div class="appointment-date">
                                        <div class="date-day"><?= date('d', strtotime($cita['scheduled_at'])) ?></div>
                                        <div class="date-month"><?= date('M', strtotime($cita['scheduled_at'])) ?></div>
                                        <div class="date-year"><?= date('Y', strtotime($cita['scheduled_at'])) ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <h3><?= $cita['reason'] ?: 'Consulta médica' ?></h3>
                                        <div class="appointment-info">
                                        <p><strong>Doctor:</strong> Dr. <?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?></p>
                                        <p><strong>Departamento:</strong> <?= htmlspecialchars($cita['departamento']) ?></p>
                                        <p><strong>Hora:</strong> <?= date('h:i A', strtotime($cita['scheduled_at'])) ?></p>
                                        </div>
                                        <div class="appointment-status <?= $cita['status'] === 'completada' ? 'completed' : 'vencida' ?>">
                                        <span><?= $cita['status'] === 'completada' ? 'Completada' : 'Vencida' ?></span>
                                        </div>
                                    </div>
                                    <?php if ($cita['status'] === 'completada'): ?>
                                        <div class="appointment-actions">
                                            <a href="<?= BASE_URL ?>/ratings/valoraciones" class="btn btn-primary">Valorar</a>
                                        </div>
                                    <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            </div>

                       
                    <div id="canceled" class="tab-content">
                    <div class="appointments-list">
                        <?php if (empty($canceladas)): ?>
                        <p style="text-align:center;">No tienes citas canceladas.</p>
                        <?php else: ?>
                        <?php foreach ($canceladas as $cita): ?>
                            <div class="appointment-card canceled">
                            <div class="appointment-date">
                                <div class="date-day"><?= date('d', strtotime($cita['scheduled_at'])) ?></div>
                                <div class="date-month"><?= date('M', strtotime($cita['scheduled_at'])) ?></div>
                                <div class="date-year"><?= date('Y', strtotime($cita['scheduled_at'])) ?></div>
                            </div>
                            <div class="appointment-details">
                                <h3><?= $cita['reason'] ?: 'Consulta médica' ?></h3>
                                <div class="appointment-info">
                                <p><strong>Doctor:</strong> Dr. <?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?></p>
                                <p><strong>Departamento:</strong> <?= htmlspecialchars($cita['departamento']) ?></p>
                                <p><strong>Hora:</strong> <?= date('h:i A', strtotime($cita['scheduled_at'])) ?></p>
                                </div>
                                <div class="appointment-status canceled">
                                <span>Cancelada</span>
                                </div>
                            </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    
                    this.classList.add('active');
                    
                    
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
    </script>
</body>
</html>

<style>
        /* Estilos para la sección de citas */
    .appointments-section {
    padding: 80px 0;
    background-color: #f8f9fa;
    }

    .container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 0 15px;
    }

    .section-title {
    text-align: center;
    margin-bottom: 50px;
    }

    .section-title h2 {
    font-size: 36px;
    font-weight: 700;
    color: #2c4964;
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 15px;
    display: inline-block;
    }

    .section-title h2::after {
    content: "";
    position: absolute;
    display: block;
    width: 50px;
    height: 3px;
    background: #1977cc;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    }

    .section-title p {
    margin: 0;
    font-size: 16px;
    color: #555;
    max-width: 700px;
    margin: 0 auto;
    }

    .appointments-container {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    }

    .appointments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    border-bottom: 1px solid #e0e0e0;
    }

    .appointments-tabs {
    display: flex;
    gap: 10px;
    }

    .tab-btn {
    background: none;
    border: none;
    padding: 10px 15px;
    font-size: 16px;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.3s;
    }

    .tab-btn.active {
    color: #1977cc;
    border-bottom-color: #1977cc;
    }

    .tab-btn:hover:not(.active) {
    color: #2c4964;
    }

    .btn {
    padding: 10px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    }

    .btn-primary {
    background-color: #1977cc;
    color: white;
    border: none;
    }

    .btn-primary:hover {
    background-color: #166ab5;
    transform: translateY(-2px);
    }

    .btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    }

    .btn-secondary:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
    }

    .btn-danger {
    background-color: #dc3545;
    color: white;
    border: none;
    }

    .btn-danger:hover {
    background-color: #c82333;
    transform: translateY(-2px);
    }

    .appointments-content {
    padding: 30px;
    }

    .tab-content {
    display: none;
    }

    .tab-content.active {
    display: block;
    }

    .appointments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
    }

    .appointment-card {
    display: flex;
    background-color: #f8f9fa;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s;
    }

    .appointment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }

    .appointment-date {
    background-color: #1977cc;
    color: white;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 100px;
    }

    .date-day {
    font-size: 28px;
    font-weight: 700;
    }

    .date-month {
    font-size: 16px;
    text-transform: uppercase;
    }

    .date-year {
    font-size: 14px;
    }

    .appointment-details {
    flex: 1;
    padding: 20px;
    position: relative;
    }

    .appointment-details h3 {
    font-size: 18px;
    font-weight: 700;
    color: #2c4964;
    margin-bottom: 10px;
    }

    .appointment-info {
    display: flex;
    flex-wrap: wrap;
    gap: 10px 30px;
    }

    .appointment-info p {
    margin: 0;
    font-size: 14px;
    color: #6c757d;
    }

    .appointment-status {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    }

    .appointment-status.completed {
    background-color: rgba(25, 192, 121, 0.15);
    color: #19c079;
    }

    .appointment-status.canceled {
    background-color: rgba(220, 53, 69, 0.15);
    color: #dc3545;
    }

    .appointment-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    }

    .appointment-card.past .appointment-date {
    background-color: #6c757d;
    }

    .appointment-card.canceled .appointment-date {
    background-color: #dc3545;
    }

    /* Estilos para la página de cancelación de cita */
    .cancel-section {
    padding: 80px 0;
    background-color: #f8f9fa;
    }

    .cancel-container {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 800px;
    margin: 0 auto;
    }

    .cancel-header {
    background-color: #f8f9fa;
    padding: 20px 30px;
    border-bottom: 1px solid #e0e0e0;
    }

    .cancel-header h2 {
    font-size: 24px;
    font-weight: 700;
    color: #2c4964;
    margin: 0;
    }

    .cancel-content {
    padding: 30px;
    }

    .appointment-summary {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 30px;
    }

    .appointment-icon {
    width: 60px;
    height: 60px;
    background-color: rgba(220, 53, 69, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc3545;
    font-size: 24px;
    }

    .appointment-details h3 {
    font-size: 18px;
    font-weight: 700;
    color: #2c4964;
    margin: 0 0 10px 0;
    }

    .appointment-details p {
    margin: 0 0 5px 0;
    font-size: 14px;
    color: #6c757d;
    }

    .cancel-warning {
    margin-bottom: 30px;
    text-align: center;
    }

    .cancel-warning p {
    margin-bottom: 10px;
    font-size: 16px;
    color: #2c4964;
    }

    .warning-text {
    color: #dc3545;
    font-weight: 600;
    }

    .form-group {
    margin-bottom: 20px;
    }

    .form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #2c4964;
    margin-bottom: 8px;
    }

    .form-group select,
    .form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s;
    }

    .form-group select:focus,
    .form-group textarea:focus {
    outline: none;
    border-color: #1977cc;
    }

    .form-group textarea {
    resize: vertical;
    min-height: 100px;
    }

    .form-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 30px;
    }

    /* Responsivo */
    @media (max-width: 768px) {
    .section-title h2 {
        font-size: 30px;
    }

    .appointments-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }

    .appointment-card {
        flex-direction: column;
    }

    .appointment-date {
        flex-direction: row;
        gap: 10px;
        padding: 15px;
        justify-content: center;
    }

    .appointment-status {
        position: static;
        margin-top: 15px;
        display: inline-block;
    }

    .appointment-summary {
        flex-direction: column;
        text-align: center;
    }

    .form-actions {
        flex-direction: column;
    }
    }

    @media (max-width: 480px) {
    .appointments-tabs {
        width: 100%;
        justify-content: space-between;
    }

    .tab-btn {
        padding: 10px;
        font-size: 14px;
    }

    .appointment-info {
        flex-direction: column;
        gap: 5px;
    }
    }

</style>
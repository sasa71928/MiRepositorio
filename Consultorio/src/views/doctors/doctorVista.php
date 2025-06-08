<?php
require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();
$doctorId = $_SESSION['user']['id'];
$citas = obtenerCitasDelDoctor($doctorId);
?>
<body>
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/doctor.css" />
    <main class="main">
        <section id="doctor-agenda" class="doctor-agenda section">
            <div class="container">
                <div class="page-header">
                    <h2>Mi Agenda</h2>
                </div>

                <div class="calendar-controls">
                    <div class="date-navigation">
                        <button class="nav-btn"><i class="fas fa-chevron-left"></i></button>
                        <h3><?= date('F Y') ?></h3>
                        <button class="nav-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="view-options">
                        <button class="view-btn active">Día</button>
                        <button class="view-btn">Semana</button>
                        <button class="view-btn">Mes</button>
                    </div>
                </div>

                <div class="calendar-day-view">
                    <div class="time-column">
                        <?php for ($h = 8; $h <= 18; $h++): ?>
                            <div class="time-slot"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</div>
                        <?php endfor; ?>
                    </div>
                    <div class="appointments-column">
                        <?php foreach ($citas as $cita): ?>
                            <?php
                                $paciente = htmlspecialchars($cita['first_name'] . ' ' . $cita['last_name']);
                                $inicio = date('H:i', strtotime($cita['scheduled_at']));
                                $fin = date('H:i', strtotime('+45 minutes', strtotime($cita['scheduled_at'])));
                                $top = (intval(date('H', strtotime($cita['scheduled_at']))) - 8) * 60;
                            ?>
                            <div class="appointment-item" style="top: <?= $top ?>px; height: 45px;">
                                <div class="appointment-content consulta">
                                    <div class="appointment-time"><?= $inicio ?> - <?= $fin ?></div>
                                    <div class="appointment-title"><?= $paciente ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="upcoming-appointments">
                    <div class="section-header">
                        <h3>Próximas Citas</h3>
                    </div>
                    <div class="appointments-table-container">
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $cita): ?>
                                    <tr>
                                        <td><?= date('H:i', strtotime($cita['scheduled_at'])) ?></td>
                                        <td><?= htmlspecialchars($cita['first_name'] . ' ' . $cita['last_name']) ?></td>
                                        <td>
                                        <span class="status <?= htmlspecialchars($cita['status']) ?>"></span>
                                        <?= ucfirst($cita['status']) ?>
                                    </td>

                                        <td>
                                            <a href="<?= BASE_URL ?>/consultaCita?id=<?= $cita['id'] ?>" class="action-icon">

                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewButtons = document.querySelectorAll('.view-btn');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    viewButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            const prevButton = document.querySelector('.nav-btn:first-child');
            const nextButton = document.querySelector('.nav-btn:last-child');
            prevButton.addEventListener('click', () => console.log('Prev'));
            nextButton.addEventListener('click', () => console.log('Next'));
        });
    </script>
</body>

</html>

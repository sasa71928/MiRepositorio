<?php
require_once __DIR__ . '/../../controllers/DoctorController.php';
require_once __DIR__ . '/../../helpers/auth.php';

require_login();
$doctorId = $_SESSION['user']['id'];
$citas = obtenerCitasDelDoctor($doctorId);

$citasFiltradas = array_filter($citas, function($cita) {
    return in_array($cita['status'], ['pendiente', 'confirmada']);
});
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
                        <h3 id="calendar-current-title"></h3>

                        <button class="nav-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="view-options">
                        <button class="view-btn active">Día</button>
                        <button class="view-btn">Semana</button>
                        <button class="view-btn">Mes</button>
                    </div>
                </div>

<div class="calendar-views">
    <div id="day-view" class="calendar-day-view calendar-view">
        <div class="time-column">
            <?php for ($h = 8; $h <= 18; $h++): ?>
                <div class="time-slot"><?= str_pad($h, 2, '0', STR_PAD_LEFT) ?>:00</div>
            <?php endfor; ?>
        </div>
        <div class="appointments-column">
            <?php foreach ($citasFiltradas as $cita): ?>
                                    <?php
                                        $paciente = htmlspecialchars($cita['first_name'] . ' ' . $cita['last_name']);
                                        $inicio = date('H:i', strtotime($cita['scheduled_at']));
                                        $fin = date('H:i', strtotime('+45 minutes', strtotime($cita['scheduled_at'])));
                                        $top = (intval(date('H', strtotime($cita['scheduled_at']))) - 8) * 60;
                                    ?>
                    <div class="appointment-item"
                        data-date="<?= date('Y-m-d', strtotime($cita['scheduled_at'])) ?>"
                        data-top="<?= $top ?>">




                                        <div class="appointment-content consulta">
                                            <div class="appointment-time"><?= $inicio ?> - <?= $fin ?></div>
                                            <div class="appointment-title"><?= $paciente ?></div>
                                        </div>
                                    </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="week-view" class="calendar-view" style="display: none;">
        <div class="week-grid"></div>
    </div>
    <div id="month-view" class="calendar-view" style="display: none;">
        <p>Vista mensual próximamente...</p>
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
                                    <?php if (empty($citasFiltradas)): ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center;">Sin citas próximas</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($citasFiltradas as $cita): ?>

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
                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewButtons = document.querySelectorAll('.view-btn');
        const views = {
            'Día': document.getElementById('day-view'),
            'Semana': document.getElementById('week-view'),
            'Mes': document.getElementById('month-view')
        };

        const dateHeader = document.getElementById('calendar-current-title');
        let currentDate = new Date();
        let currentView = 'Día';

        function getISODate(date) {
            return date.toISOString().split('T')[0];
        }

function updateHeader() {
    const options = { month: 'long', year: 'numeric' };
    const dayOptions = { day: 'numeric', month: 'long', year: 'numeric' };

    const weekView = document.getElementById('week-view');
    const weekGrid = weekView.querySelector('.week-grid');
    if (weekGrid) weekGrid.innerHTML = '';

if (currentView === 'Día') {
    dateHeader.textContent = currentDate.toLocaleDateString('es-MX', dayOptions);

    const todayStr = getISODate(currentDate);
    const dayAppointments = document.querySelectorAll('#day-view .appointment-item');

    dayAppointments.forEach(el => {
        const date = el.dataset.date;
        if (date === todayStr) {
            el.style.display = 'block';
            el.style.top = el.dataset.top + 'px';
            el.style.height = '45px';
            el.style.position = 'absolute';
        } else {
            el.style.display = 'none';
        }

    });
}else if (currentView === 'Semana') {
        const startOfWeek = new Date(currentDate);
        const endOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - currentDate.getDay() + 1);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        const format = d => d.toLocaleDateString('es-MX', { day: 'numeric', month: 'short' });
        dateHeader.textContent = `${format(startOfWeek)} - ${format(endOfWeek)}`;

        const weekDates = [];
        for (let i = 0; i < 7; i++) {
            const d = new Date(startOfWeek);
            d.setDate(startOfWeek.getDate() + i);
            const dateStr = getISODate(d);
            weekDates.push(dateStr);

            const dayColumn = document.createElement('div');
            dayColumn.className = 'week-day-column';

            const label = document.createElement('div');
            label.className = 'week-day-label';
            label.textContent = d.toLocaleDateString('es-MX', { weekday: 'short', day: 'numeric' });
            dayColumn.appendChild(label);

            document.querySelectorAll('.appointment-item').forEach(el => {
                if (el.dataset.date === dateStr) {
                    const hora = el.querySelector('.appointment-time')?.textContent || '';
                    const nombre = el.querySelector('.appointment-title')?.textContent || '';

                    const item = document.createElement('div');
                    item.className = 'week-appointment';
                    item.innerHTML = `<div>${hora}</div><div>${nombre}</div>`;
                    dayColumn.appendChild(item);
                }
            });

            if (weekGrid) weekGrid.appendChild(dayColumn);
        }
    } else {
        dateHeader.textContent = currentDate.toLocaleDateString('es-MX', options);
    }
}


        function changeMonth(offset) {
            currentDate.setMonth(currentDate.getMonth() + offset);
            updateHeader();
        }

        function changeDay(offset) {
            currentDate.setDate(currentDate.getDate() + offset);
            updateHeader();
        }

        function changeWeek(offset) {
            currentDate.setDate(currentDate.getDate() + (offset * 7));
            updateHeader();
        }

        viewButtons.forEach(button => {
            button.addEventListener('click', function () {
                viewButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                currentView = this.textContent;

                for (const key in views) {
                    views[key].style.display = (this.textContent === key) ? 'block' : 'none';
                }
                updateHeader();
            });
        });

        const prevButton = document.querySelector('.nav-btn:first-child');
        const nextButton = document.querySelector('.nav-btn:last-child');

        prevButton.addEventListener('click', () => {
            if (currentView === 'Día') changeDay(-1);
            else if (currentView === 'Semana') changeWeek(-1);
            else changeMonth(-1);
        });

        nextButton.addEventListener('click', () => {
            if (currentView === 'Día') changeDay(1);
            else if (currentView === 'Semana') changeWeek(1);
            else changeMonth(1);
        });

        updateHeader();
    });
</script>
</body>

</html>

<style>
    /*Semamna */
    .week-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
        margin-top: 10px;
    }
    .week-day-column {
        background-color: #f9f9f9;
        padding: 5px;
        border: 1px solid #ccc;
        min-height: 150px;
        border-radius: 8px;
    }
    .week-day-label {
        font-weight: bold;
        margin-bottom: 5px;
        text-align: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 4px;
    }
    .week-appointment {
    background-color: #e8f0fe;
    padding: 4px 6px;
    margin: 4px 0;
    border-left: 3px solid #4285f4;
    border-radius: 4px;
    font-size: 0.85rem;
}
.appointments-column {
    position: relative;
    height: 660px;
}
.appointment-item {
    position: absolute;
    width: 100%;
}



</style>
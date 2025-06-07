<?php
require_once __DIR__ . '/../../controllers/RatingController.php';
require_once __DIR__ . '/../../controllers/AppointmentController.php';

$userId = $_SESSION['user']['id'];
$valoraciones = obtenerValoracionesUsuario($userId);
$misCitas = obtenerCitasPorUsuario($userId);

// Citas completadas sin valoración
$valoradasIds = array_column($valoraciones, 'appointment_id');
$citasPendientes = array_filter($misCitas, function ($cita) use ($valoradasIds) {
    return $cita['status'] === 'completada' && !in_array($cita['id'], $valoradasIds);
});
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Valoraciones - CliniGest</title>
</head>
<body>
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    
<main class="main">
    <section class="ratings-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Mis Valoraciones</h2>
                <p>Gestiona tus valoraciones de médicos y servicios</p>
            </div>

            <div class="ratings-container">
                <div class="ratings-header">
                    <div class="ratings-tabs">
                        <button class="tab-btn active" data-tab="my-ratings">Mis Valoraciones</button>
                        <button class="tab-btn" data-tab="pending-ratings">Pendientes de Valorar</button>
                    </div>
                </div>

                <div class="ratings-content">
                    <!-- Tab: Mis Valoraciones -->
                    <div id="my-ratings" class="tab-content active">
                        <div class="ratings-list">
                            <?php if (empty($valoraciones)): ?>
                                <p style="text-align:center;">Aún no has realizado ninguna valoración.</p>
                            <?php else: ?>
                                <?php foreach ($valoraciones as $val): ?>
                                <div class="rating-card">
                                    <div class="rating-header">
                                        <div class="doctor-info">
                                            <div>
                                                <h3>Dr. <?= htmlspecialchars($val['doctor_first_name'] . ' ' . $val['doctor_last_name']) ?></h3>
                                                <p><?= date('d M, Y', strtotime($val['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rating-body">
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?= $i <= $val['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                            <?php endfor; ?>
                                            <span><?= $val['rating'] ?></span>
                                        </div>
                                        <p class="rating-comment"><?= htmlspecialchars($val['comment']) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tab: Pendientes de Valorar -->
                    <div id="pending-ratings" class="tab-content">
                        <div class="ratings-list">
                            <?php if (empty($citasPendientes)): ?>
                                <p style="text-align:center;">No tienes valoraciones pendientes.</p>
                            <?php else: ?>
                            <?php foreach ($citasPendientes as $cita): ?>
                                <div class="rating-card pending">
                                    <div class="rating-header">
                                        <div class="doctor-info">
                                            <div>
                                                <h3>Dr. <?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?></h3>
                                                <p><?= htmlspecialchars($cita['departamento']) ?></p>
                                            </div>
                                        </div>
                                        <div class="rating-date">
                                            <p>Consulta: <?= date('d M, Y', strtotime($cita['scheduled_at'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="rating-body">
                                        <p class="pending-message">
                                            Tienes pendiente valorar esta consulta. Tu opinión nos ayuda a mejorar nuestros servicios.
                                        </p>
                                    </div>

                                    <button
                                        class="btn btn-primary valorar-btn"
                                        data-id="<?= $cita['id'] ?>"
                                        data-doctor-id="<?= $cita['doctor_id'] ?>"
                                        data-doctor="<?= htmlspecialchars($cita['doctor_first_name'] . ' ' . $cita['doctor_last_name']) ?>"
                                    >
                                        Valorar Ahora
                                    </button>
                                </div>
                            <?php endforeach; ?>


                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

        
        <!-- Modal para valorar la cita -->
        <div class="rating-modal" id="ratingModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Valorar Consulta</h3>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="doctor-info">
                        <div>
                            <h4>Dr. Carlos Rodríguez</h4>
                        </div>
                    </div>
                    
                <form class="rating-form" method="POST" action="<?= BASE_URL ?>/ratings/guardarValoracion">
                    <input type="hidden" name="appointment_id" id="modal-appointment-id">
                    <input type="hidden" name="doctor_id" id="modal-doctor-id">
                    <input type="hidden" name="score" id="modal-score">

                    <div class="form-group">
                        <label>¿Cómo calificarías tu experiencia?</label>
                        <div class="star-rating">
                            <i class="far fa-star" data-rating="1"></i>
                            <i class="far fa-star" data-rating="2"></i>
                            <i class="far fa-star" data-rating="3"></i>
                            <i class="far fa-star" data-rating="4"></i>
                            <i class="far fa-star" data-rating="5"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="rating-comment">Comentarios (opcional)</label>
                        <textarea id="rating-comment" name="comment" class="form-control" rows="4" placeholder="Comparte tu experiencia..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline cancel-btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Valoración</button>
                    </div>
                </form>

                </div>
            </div>
        </div>
    </main>
    
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    const doctorIdInput = document.getElementById('modal-doctor-id');
    const valorarBtns = document.querySelectorAll('.valorar-btn');
    const ratingModal = document.getElementById('ratingModal');
    const closeBtn = document.querySelector('.close-btn');
    const cancelBtn = document.querySelector('.cancel-btn');
    const appointmentIdInput = document.getElementById('modal-appointment-id');
    const doctorNameField = document.querySelector('#ratingModal .doctor-info h4');
    const scoreInput = document.getElementById('modal-score');

    // ✅ Define funciones primero
    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = star.getAttribute('data-rating');
            if (starRating <= rating) {
                star.classList.remove('far');
                star.classList.add('fas');
            } else {
                star.classList.remove('fas');
                star.classList.add('far');
            }
        });
    }

    function resetStars() {
        scoreInput.value = 0;
        stars.forEach(star => {
            star.classList.remove('fas');
            star.classList.add('far');
        });
    }

    function setRating(rating) {
        scoreInput.value = rating;
        highlightStars(rating);
    }

    // Pestañas
    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            this.classList.add('active');
            const tabId = this.getAttribute('data-tab');
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Mostrar modal de valoración
        valorarBtns.forEach(button => {
            button.addEventListener('click', function () {
                const doctor = this.dataset.doctor;
                const doctorId = this.dataset.doctorId;
                const appointmentId = this.dataset.id;

                doctorNameField.textContent = 'Dr. ' + doctor;
                doctorIdInput.value = doctorId; // 👈 aquí
                appointmentIdInput.value = appointmentId;
                resetStars();
                ratingModal.style.display = 'flex';
            });
        });

    // Cierre del modal
    closeBtn.addEventListener('click', () => {
        ratingModal.style.display = 'none';
    });

    cancelBtn.addEventListener('click', () => {
        ratingModal.style.display = 'none';
    });

    // Estrellas interactivas
    const stars = document.querySelectorAll('.star-rating i');

    stars.forEach(star => {
        star.addEventListener('mouseover', function () {
            highlightStars(this.dataset.rating);
        });

        star.addEventListener('mouseout', function () {
            highlightStars(scoreInput.value);
        });

        star.addEventListener('click', function () {
            setRating(this.dataset.rating);
        });
    });
});
</script>


</body>
</html>

<style>
        /* Estilos para la página de valoraciones */
    .ratings-section {
        padding: 60px 0;
    }

    .ratings-container {
        margin-top: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .ratings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .ratings-tabs {
        display: flex;
        gap: 10px;
    }

    .tab-btn {
        padding: 8px 16px;
        background-color: transparent;
        border: none;
        border-radius: 4px;
        color: #2c4964;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
    }

    .tab-btn.active,
    .tab-btn:hover {
        background-color: #1977cc;
        color: white;
    }

    .ratings-content {
        padding: 20px;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .ratings-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .rating-card {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .rating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .doctor-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .doctor-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .doctor-info h3 {
        margin: 0 0 5px;
        color: #2c4964;
        font-size: 18px;
    }

    .doctor-info p {
        margin: 0;
        color: #1977cc;
        font-size: 14px;
    }

    .rating-date p {
        margin: 0;
        color: #6c757d;
        font-size: 14px;
    }

    .rating-body {
        margin-bottom: 15px;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .rating-stars i {
        color: #ffc107;
        margin-right: 2px;
    }

    .rating-stars span {
        margin-left: 10px;
        font-weight: 600;
    }

    .rating-comment {
        margin: 0;
        color: #555;
        font-size: 14px;
        line-height: 1.6;
    }

    .rating-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .btn-outline {
        background-color: transparent;
        border: 1px solid #1977cc;
        color: #1977cc;
    }

    .btn-outline:hover {
        background-color: #1977cc;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-primary {
        background-color: #1977cc;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1c84e3;
    }

    .rating-card.pending {
        border: 1px dashed #1977cc;
    }

    .pending-message {
        color: #1977cc;
        font-style: italic;
        margin: 0;
    }

    /* Modal de valoracion */
    .rating-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-header h3 {
        margin: 0;
        color: #2c4964;
    }

    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #6c757d;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .doctor-info {
        margin-bottom: 20px;
    }

    .rating-form .form-group {
        margin-bottom: 20px;
    }

    .rating-form label {
        display: block;
        margin-bottom: 8px;
        color: #2c4964;
        font-weight: 500;
    }

    .star-rating {
        display: flex;
        gap: 5px;
        font-size: 24px;
        color: #ffc107;
        cursor: pointer;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        resize: vertical;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .ratings-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }
</style>
 <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Valoraciones - CliniGest</title>
    <link href="valoraciones.css" rel="stylesheet">
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
                        <div id="my-ratings" class="tab-content active">
                            <div class="ratings-list">
                                <!-- Carta de valoracion -->
                                <div class="rating-card">
                                    <div class="rating-header">
                                        <div class="doctor-info">
                                            <div>
                                                <h3>Dr. Luis Sánchez</h3>
                                                <p>Dermatología</p>
                                            </div>
                                        </div>
                                        <div class="rating-date">
                                            <p>05 May, 2025</p>
                                        </div>
                                    </div>
                                    <div class="rating-body">
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>4.0</span>
                                        </div>
                                        <p class="rating-comment">
                                            Excelente atención, el doctor fue muy amable y profesional. El diagnóstico fue acertado y el tratamiento efectivo. Recomendaría al Dr. Sánchez a cualquier persona con problemas dermatológicos.
                                        </p>
                                    </div>
                                </div>
                                
                                <!--Carta de valoracion-->
                                <div class="rating-card">
                                    <div class="rating-header">
                                        <div class="doctor-info">
                                            <div>
                                                <h3>Dra. María González</h3>
                                                <p>Laboratorio</p>
                                            </div>
                                        </div>
                                        <div class="rating-date">
                                            <p>20 Abr, 2025</p>
                                        </div>
                                    </div>
                                    <div class="rating-body">
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span>4.5</span>
                                        </div>
                                        <p class="rating-comment">
                                            Servicio rápido y eficiente. El personal fue muy profesional y los resultados estuvieron listos antes de lo esperado. Las instalaciones son modernas y limpias.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="pending-ratings" class="tab-content">
                            <div class="ratings-list">

                                <!-- Pendientes de valorar -->
                                <div class="rating-card pending">
                                    <div class="rating-header">
                                        <div class="doctor-info">
                                            <div>
                                                <h3></h3>
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="rating-date">
                                            <p>Consulta: 15 Jun, 2025</p>
                                        </div>
                                    </div>
                                    <div class="rating-body">
                                        <p class="pending-message">
                                            Tienes pendiente valorar esta consulta. Tu opinión nos ayuda a mejorar nuestros servicios.
                                        </p>
                                    </div>
                                    <div class="rating-footer">
                                        <button class="btn btn-primary">Valorar Ahora</button>
                                    </div>
                                </div>
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
                    
                    <form class="rating-form">
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
                            <textarea id="rating-comment" class="form-control" rows="4" placeholder="Comparte tu experiencia..."></textarea>
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
            
            // Modal para valorar
            const rateButtons = document.querySelectorAll('.btn-primary');
            const ratingModal = document.getElementById('ratingModal');
            const closeBtn = document.querySelector('.close-btn');
            const cancelBtn = document.querySelector('.cancel-btn');
            
            rateButtons.forEach(button => {
                if (button.textContent === 'Valorar Ahora') {
                    button.addEventListener('click', function() {
                        ratingModal.style.display = 'flex';
                    });
                }
            });
            
            closeBtn.addEventListener('click', function() {
                ratingModal.style.display = 'none';
            });
            
            cancelBtn.addEventListener('click', function() {
                ratingModal.style.display = 'none';
            });
            
            // Empezar a valorar
            const stars = document.querySelectorAll('.star-rating i');
            
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.getAttribute('data-rating');
                    highlightStars(rating);
                });
                
                star.addEventListener('mouseout', function() {
                    resetStars();
                });
                
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    setRating(rating);
                });
            });
            
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
                stars.forEach(star => {
                    star.classList.remove('fas');
                    star.classList.add('far');
                });
            }
            
            function setRating(rating) {
                // aqui es donde se guarda la valoracion
                console.log('Rating set to: ' + rating);
            }
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
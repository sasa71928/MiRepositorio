<?php
// src/views/public/profile.php

// 1) Incluimos el header (que ya tiene el menú dinámico según rol)
include_once __DIR__ . '/../layouts/header.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css" />
</head>

<body>
    <main class="main">
        <section class="profile-section">
            <div class="container">
                <div class="section-title" data-aos="fade-up">
                    <h2>Mi Perfil</h2>
                    <p>Gestiona tu información personal y preferencias</p>
                </div>
                
                <div class="profile-container">
                    <div class="profile-sidebar">
                        <div class="profile-menu">
                            <ul>
                                <li class="active"><a href="#personal-info">Información Personal</a></li>
                                <li><a href="#medical-info">Información Médica</a></li>
                                <li><a href="#security">Seguridad</a></li>
                                <li><a href="#preferences">Preferencias</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="profile-content">
                        <!-- ====================== Pestaña: Información Personal ====================== -->
                        <div id="personal-info" class="profile-tab active">
                            <h3>Información Personal</h3>
                            <form class="profile-form" action="<?= BASE_URL ?>/profile/update_personal" method="POST">
                                <!-- Si deseas editar y guardar, apunta a una ruta tipo /profile/update_personal -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="first_name">Nombre</label>
                                        <input type="text"
                                               id="first_name"
                                               name="first_name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['first_name']) ?>"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Apellido</label>
                                        <input type="text"
                                               id="last_name"
                                               name="last_name"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['last_name']) ?>"
                                               required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['email']) ?>"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <input type="tel"
                                               id="phone"
                                               name="phone"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['phone']) ?>">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="birthdate">Fecha de Nacimiento</label>
                                        <input type="date"
                                               id="birthdate"
                                               name="birthdate"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['birthdate']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <input type="text"
                                               id="address"
                                               name="address"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['address']) ?>">
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="city">Ciudad</label>
                                        <input type="text"
                                               id="city"
                                               name="city"
                                               class="form-control"
                                               value="<?= htmlspecialchars($user['city']) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Rol</label>
                                        <input type="text"
                                               id="role"
                                               name="role"
                                               class="form-control"
                                               value="<?= htmlspecialchars(ucfirst($user['role'])) ?>"
                                               disabled>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- ====================== Pestaña: Información Médica ====================== -->
                        <div id="medical-info" class="profile-tab">
                            <h3>Información Médica</h3>
                            <form class="profile-form" action="<?= BASE_URL ?>/profile/update_medical" method="POST">
                                <div class="form-group">
                                    <label for="blood_type">Tipo de Sangre</label>
                                    <select id="blood_type"
                                            name="blood_type"
                                            class="form-control">
                                        <option value="">--Seleccione--</option>
                                        <?php 
                                          $tipos = ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
                                          foreach ($tipos as $tipo): 
                                            $sel = ($user['blood_type'] === $tipo) ? 'selected' : '';
                                        ?>
                                          <option value="<?= $tipo ?>" <?= $sel ?>><?= $tipo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="allergies">Alergias</label>
                                    <textarea id="allergies"
                                              name="allergies"
                                              class="form-control"
                                              rows="3"><?= htmlspecialchars($user['allergies']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="chronic_diseases">Enfermedades Crónicas</label>
                                    <textarea id="chronic_diseases"
                                              name="chronic_diseases"
                                              class="form-control"
                                              rows="3"><?= htmlspecialchars($user['chronic_diseases']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="current_medications">Medicamentos Actuales</label>
                                    <textarea id="current_medications"
                                              name="current_medications"
                                              class="form-control"
                                              rows="3"><?= htmlspecialchars($user['current_medications']) ?></textarea>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- ====================== Pestaña: Seguridad ====================== -->
                        <div id="security" class="profile-tab">
                            <h3>Seguridad</h3>
                            <form class="profile-form" action="<?= BASE_URL ?>/profile/change_password" method="POST">
                                <div class="form-group">
                                    <label for="current_password">Contraseña Actual</label>
                                    <input type="password"
                                           id="current_password"
                                           name="current_password"
                                           class="form-control"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">Nueva Contraseña</label>
                                    <input type="password"
                                           id="new_password"
                                           name="new_password"
                                           class="form-control"
                                           required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirmar Contraseña</label>
                                    <input type="password"
                                           id="confirm_password"
                                           name="confirm_password"
                                           class="form-control"
                                           required>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- ====================== Pestaña: Preferencias ====================== -->
                        <div id="preferences" class="profile-tab">
                            <h3>Preferencias</h3>
                            <form class="profile-form" action="<?= BASE_URL ?>/profile/update_preferences" method="POST">
                                <div class="form-group">
                                    <label>Notificaciones</label>
                                    <div class="checkbox-group">
                                        <div class="checkbox-item">
                                            <input type="checkbox"
                                                   id="notif_email"
                                                   name="notify_email"
                                                   <?= $user['notify_email'] ? 'checked' : '' ?>>
                                            <label for="notif_email">Por correo</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox"
                                                   id="notif_sms"
                                                   name="notify_sms"
                                                   <?= $user['notify_sms'] ? 'checked' : '' ?>>
                                            <label for="notif_sms">Por SMS</label>
                                        </div>
                                        <div class="checkbox-item">
                                            <input type="checkbox"
                                                   id="notif_whatsapp"
                                                   name="notify_whatsapp"
                                                   <?= $user['notify_whatsapp'] ? 'checked' : '' ?>>
                                            <label for="notif_whatsapp">Por WhatsApp</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Recordatorio de Citas:</label>
                                    <div class="radio-group">
                                        <div class="radio-item">
                                            <input type="radio"
                                                   id="reminder_1"
                                                   name="reminder_days"
                                                   value="1"
                                                   <?= ($user['reminder_days'] === '1') ? 'checked' : '' ?>>
                                            <label for="reminder_1">1 día antes</label>
                                        </div>
                                        <div class="radio-item">
                                            <input type="radio"
                                                   id="reminder_2"
                                                   name="reminder_days"
                                                   value="2"
                                                   <?= ($user['reminder_days'] === '2') ? 'checked' : '' ?>>
                                            <label for="reminder_2">2 días antes</label>
                                        </div>
                                        <div class="radio-item">
                                            <input type="radio"
                                                   id="reminder_7"
                                                   name="reminder_days"
                                                   value="7"
                                                   <?= ($user['reminder_days'] === '7') ? 'checked' : '' ?>>
                                            <label for="reminder_7">1 semana antes</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                                </div>
                            </form>
                        </div>
                    </div> <!-- .profile-content -->
                </div>     <!-- .profile-container -->
            </div>         <!-- .container -->
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.profile-menu a');
            const tabContents = document.querySelectorAll('.profile-tab');
            
            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    document.querySelectorAll('.profile-menu li').forEach(item => {
                        item.classList.remove('active');
                    });
                    tabContents.forEach(tab => {
                        tab.classList.remove('active');
                    });
                    
                    this.parentElement.classList.add('active');
                    const targetTab = document.querySelector(this.getAttribute('href'));
                    targetTab.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>

<style>
    /* Estilos para la página de perfil */
    .profile-section {
        padding: 60px 0;
    }

    .profile-container {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }

    .profile-sidebar {
        flex: 0 0 250px;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .profile-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .profile-menu li {
        margin-bottom: 5px;
    }

    .profile-menu a {
        display: block;
        padding: 10px 15px;
        color: #2c4964;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s;
    }

    .profile-menu li.active a,
    .profile-menu a:hover {
        background-color: #1977cc;
        color: white;
    }

    .profile-content {
        flex: 1;
        background-color: #fff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .profile-tab {
        display: none;
    }

    .profile-tab.active {
        display: block;
    }

    .profile-tab h3 {
        color: #2c4964;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .profile-form .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 20px;
    }

    .profile-form .form-group {
        flex: 1;
        min-width: 250px;
    }

    .profile-form label {
        display: block;
        margin-bottom: 5px;
        color: #2c4964;
        font-weight: 500;
    }

    .profile-form .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .profile-form .form-control:focus {
        border-color: #1977cc;
        outline: none;
    }

    .profile-form textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .checkbox-group,
    .radio-group {
        margin-top: 10px;
    }

    .checkbox-item,
    .radio-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .checkbox-item input,
    .radio-item input {
        margin-right: 10px;
    }

    .form-actions {
        margin-top: 30px;
        text-align: right;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .btn-primary {
        background-color: #1977cc;
        color: white;
    }

    .btn-primary:hover {
        background-color: #1c84e3;
    }
</style>

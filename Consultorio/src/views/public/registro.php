<?php
// src/views/public/registro.php

// 1) Iniciar sesión y cargar constantes
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/functions.php';

// Definir BASE_URL y ASSETS_URL
$config = include __DIR__ . '/../../config/config.php';
if (! defined('BASE_URL')) {
    define('BASE_URL', rtrim($config['base_url'], '/'));
}
if (! defined('ASSETS_URL')) {
    define('ASSETS_URL', rtrim($config['assets_url'], '/'));
}

// 2) Si ya está autenticado, redirigir a "/"
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

// 3) Leer valores previos y errores de sesión (si existen)
$data   = $_SESSION['registro_data']   ?? [];
$errors = $_SESSION['registro_errors'] ?? [];
// Limpio para no repetir
unset($_SESSION['registro_errors'], $_SESSION['registro_data']);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>CliniGest – Registro Paciente</title>
      <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/registro.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    </head>
  <body>

    <main class="container registro-container">
      <h2>Crear Cuenta (Paciente)</h2>

      <!-- Mostrar lista de errores, si hay -->
      <?php if (! empty($errors)): ?>
        <script>
          alert(`<?= implode('\n• ', array_map('addslashes', $errors)) ?>`);
        </script>
      <?php endif; ?>


      <form action="<?= BASE_URL ?>/registro" method="POST" id="registroForm">
      <!-- Paso 1: Datos Personales -->
      <div class="step active" data-step="1">
        <fieldset>
          <legend>Datos Personales</legend>

          <label>Nombre de Usuario: <span class="required">*</span>
            <input type="text" name="username" value="<?= htmlspecialchars($data['username'] ?? '') ?>" required>
          </label>

          <label>Nombre: <span class="required">*</span>
            <input type="text" name="first_name" value="<?= htmlspecialchars($data['first_name'] ?? '') ?>" required>
          </label>

          <label>Apellidos: <span class="required">*</span>
            <input type="text" name="last_name" value="<?= htmlspecialchars($data['last_name'] ?? '') ?>" required>
          </label>

          <label>Correo: <span class="required">*</span>
            <input type="email" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" required>
          </label>

          <label>Telefono: <span class="required">*</span>
            <input type="text" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>" required>
          </label>

          <label>Fecha de Nacimiento: <span class="required">*</span>
            <input type="date" name="birthdate" value="<?= htmlspecialchars($data['birthdate'] ?? '') ?>" required>
          </label>

          <label>Dirección: <span class="required">*</span>
            <input type="text" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>" required>
          </label>

          <label>Ciudad: <span class="required">*</span>
            <input type="text" name="city" value="<?= htmlspecialchars($data['city'] ?? '') ?>" required>
          </label>
        </fieldset>

        <div class="step-buttons">
          <button type="button" class="next-btn" onclick="nextStep()">Siguiente</button>
        </div>
      </div>

        <!-- Paso 2: Contrasena -->
      <div class="step" data-step="2">
        <fieldset>
          <label>Contraseña:<span class="required">*</span>
            <input type="password" name="password" required>
          </label>

          <label>Confirmar Contraseña:<span class="required">*</span>
            <input type="password" name="confirm_password" required>
          </label>
        </fieldset>

        <div class="step-buttons">
          <button type="button" class="prev-btn" onclick="prevStep()">Anterior</button>
          <button type="button" class="next-btn" onclick="nextStep()">Siguiente</button>
        </div>

      </div>

      <!-- Paso 3: Información Médica -->
      <div class="step" data-step="3">
        <fieldset>
            
          <legend>Información Médica</legend>
          <label>Género:<span class="required">*</span>
            <select name="gender" required>
              <option value="">Selecciona...</option>
              <option value="M" <?= (isset($data['gender']) && $data['gender'] === 'M') ? 'selected' : '' ?>>Masculino</option>
              <option value="F" <?= (isset($data['gender']) && $data['gender'] === 'F') ? 'selected' : '' ?>>Femenino</option>
            </select>
          </label>
          <label>Tipo de sangre:<span class="required">*</span>
            <select name="blood_type">
              <option value="">Selecciona...</option>
              <?php
              $tipos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
              foreach ($tipos as $tipo) {
                  $sel = ($data['blood_type'] ?? '') === $tipo ? 'selected' : '';
                  echo "<option value=\"$tipo\" $sel>$tipo</option>";
              }
              ?>
            </select>
          </label>

          <label>Alergias:
            <textarea name="allergies"><?= htmlspecialchars($data['allergies'] ?? '') ?></textarea>
          </label>

          <label>Enfermedades crónicas:
            <textarea name="chronic_diseases"><?= htmlspecialchars($data['chronic_diseases'] ?? '') ?></textarea>
          </label>

          <label>Medicamentos actuales:
            <textarea name="current_medications"><?= htmlspecialchars($data['current_medications'] ?? '') ?></textarea>
          </label>
        </fieldset>

        <div class="step-buttons">
          <button type="button" class="prev-btn" onclick="prevStep()">Anterior</button>
          <button type="button" class="next-btn" onclick="nextStep()">Siguiente</button>
        </div>
      </div>

      <!-- Paso 4: Preferencias de Notificación -->
      <div class="step" data-step="4">
        <fieldset>
          <legend>Preferencias</legend>

          <label><input type="checkbox" name="notify_email" <?= !empty($data['notify_email']) ? 'checked' : '' ?>> Recibir recordatorios por email</label>
          <label><input type="checkbox" name="notify_sms" <?= !empty($data['notify_sms']) ? 'checked' : '' ?>> Recibir recordatorios por SMS</label>
          <label><input type="checkbox" name="notify_whatsapp" <?= !empty($data['notify_whatsapp']) ? 'checked' : '' ?>> Recibir recordatorios por WhatsApp</label>

          <label>Días antes para enviar recordatorio:
            <select name="reminder_days">
              <option value="1" <?= ($data['reminder_days'] ?? '') === '1' ? 'selected' : '' ?>>1 día</option>
              <option value="2" <?= ($data['reminder_days'] ?? '') === '2' ? 'selected' : '' ?>>2 días</option>
              <option value="7" <?= ($data['reminder_days'] ?? '') === '7' ? 'selected' : '' ?>>7 días</option>
            </select>
          </label>
        </fieldset>

        <div class="step-buttons">
          <button type="button" class="prev-btn" onclick="prevStep()">Anterior</button>
          <button type="submit" class="next-btn">Registrar</button>
        </div>
      </div>
    </form>
      <p class="mt-3">
        ¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/login">Iniciar Sesión</a>
      </p>
    </main>

    <script>
      const steps = document.querySelectorAll('.step');
      let currentStep = 0;

      function showStep(index) {
        steps.forEach((el, i) => el.classList.toggle('active', i === index));
        currentStep = index;
      }

      function nextStep() {
        // Validar campos requeridos del paso actual
        const currentFields = steps[currentStep].querySelectorAll('input[required], select[required], textarea[required]');
        for (const field of currentFields) {
          if (!field.value.trim()) {
            alert("Por favor completa todos los campos obligatorios (*) antes de continuar.");
            field.focus();
            return;
          }
        }

        if (currentStep < steps.length - 1) {
          showStep(currentStep + 1);
          document.querySelector('.registro-container').scrollIntoView({ behavior: 'smooth' });
        }
      }

      function prevStep() {
        if (currentStep > 0) {
          showStep(currentStep - 1);
          document.querySelector('.registro-container').scrollIntoView({ behavior: 'smooth' });
        }
      }

      // Prevenir envío si no estás en el último paso
      document.addEventListener('DOMContentLoaded', () => {
        showStep(0);
        document.getElementById('registroForm').addEventListener('submit', function (e) {
          if (currentStep < steps.length - 1) {
            e.preventDefault();
            nextStep();
          }
        });
      });
    </script>

  </body>
</html>


  <style>
    /* Estilos para los pasos y botones */
    .step { display: none; }
    .step.active { display: block; }

    .step-buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 1rem;
    }
    .step-buttons button {
      padding: 0.6rem 1.2rem;
      font-size: 1rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .step-buttons .next-btn {
      background-color: #1977cc;
      color: #fff;
    }
    .step-buttons .next-btn:hover {
      background-color: #166ab5;
    }
    .step-buttons .prev-btn {
      background-color: #6c757d;
      color: #fff;
    }
    .step-buttons .prev-btn:hover {
      background-color: #5a6268;
    }

    /* Estilos específicos del formulario de registro */
    *, *::before, *::after {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background-color: #bdd7e5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding: 20px;
    }
    .registro-container {
      background-color: white;
      padding: 40px 30px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      text-align: center;
    }
    .registro-container h2 {
      margin-bottom: 20px;
      color: #2c4964;
    }
    .registro-container input,
    .registro-container select,
    .registro-container textarea {
      width: 100%;
      padding: 8px;
      margin: 6px 0;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }
    .registro-container label {
      display: block;
      text-align: left;
      margin-top: 10px;
      color: #2c4964;
      font-weight: 500;
    }
    .next-btn, .prev-btn {
      min-width: 120px;
    }
    .alert-danger {
      background-color: #f8d7da;
      color: #842029;
      padding: 10px;
      border-radius: 4px;
      margin-bottom: 15px;
      text-align: center;
    }
    .mt-3 {
      margin-top: 1rem;
    }
  </style>
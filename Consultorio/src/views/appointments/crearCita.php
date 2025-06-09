<?php

$departamentos = obtenerDepartamentos();
$doctores = obtenerDoctoresConDepartamento();
$usuario = $_SESSION['user'];
$hoy = date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css" />
</head>
<body>
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
        <main class="main">
             <!-- Sección de agendar cita -->
    <section id="appointment" class="appointment section">
  
    <div class="container section-title" data-aos="fade-up">
        <h2>Agendar cita</h2>
        <p>Formulario para agendar cita</p>
    </div>

<div class="container">
<div class="appointment-form-container" data-aos="fade-up" data-aos-delay="200">

    <?php if (isset($_GET['error']) && $_GET['msg']): ?>
    <div class="alert alert-danger" style="text-align:center; margin-bottom: 20px; background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px;">
        ⚠️ <?= htmlspecialchars($_GET['msg']) ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
    <div class="alert alert-success" style="text-align:center; margin-bottom: 20px; background-color: #d4edda; color: #155724; padding: 12px; border-radius: 5px;">
        ✅ ¡Tu cita ha sido agendada con éxito!
    </div>

    <div class="text-center" style="margin-top: 20px;">
           <a href="<?= BASE_URL ?>/appointments/mine" class="appointment-btn">Agendar cita</button>
    </div>

    <?php else: ?>
    <form action="<?= BASE_URL ?>/appointments/create" method="post" role="form" class="appointment-form">
        <div class="form-row">
            <div class="form-group">
                <input type="text" name="first_name" class="form-control" id="first_name" placeholder="Nombre"
                value="<?= htmlspecialchars($usuario['first_name']) ?>" readonly>
            </div>

            <div class="form-group">
                <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Apellidos"
                value="<?= htmlspecialchars($usuario['last_name']) ?>" readonly>
            </div>

            <div class="form-group">
                <input type="tel" class="form-control" name="phone" id="phone" placeholder="Número de teléfono"
                value="<?= htmlspecialchars($usuario['phone']) ?>" readonly>
            </div>

            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="Correo"
                value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group date-group">
                <input type="date" name="date" class="form-control" id="date" min="<?= $hoy ?>" required>
            </div>

            <div class="form-group time-group">
                <input type="time" name="time" class="form-control" id="time" min="08:00" max="18:00" required>
            </div>

            <div class="form-group">
                <select id="department" class="form-control">
                    <option value="">Todos los departamentos</option>
                    <?php foreach ($departamentos as $dep): ?>
                        <option value="<?= htmlspecialchars($dep['name']) ?>"><?= htmlspecialchars($dep['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <select name="doctor_id" id="doctor" class="form-control" required>
                    <option value="">Selecciona un doctor</option>
                    <?php foreach ($doctores as $doc): ?>
                        <option value="<?= $doc['user_id'] ?>" data-departamento="<?= $doc['departamento'] ?>">
                            <?= htmlspecialchars($doc['first_name'] . ' ' . $doc['last_name']) ?> (<?= $doc['departamento'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <textarea class="form-control" name="reason" rows="5" placeholder="Motivo de la cita" required></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <input type="number" class="form-control" name="amount" step="0.01" min="100.00" placeholder="Monto mínimo: $100.00" required>
            </div>

            <div class="form-group">
                <select name="method" class="form-control" required>
                    <option value="">Método de pago</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                </select>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="appointment-btn">Agendar cita</button>
        </div>
    </form>
    <?php endif; ?>

</div>
</div>


</div>
  </div>
</section>
        </main>
</body>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const selectDepartamento = document.getElementById('department');
    const selectDoctor = document.getElementById('doctor');

    // Guardamos todos los doctores originales en memoria
    const allDoctorOptions = Array.from(selectDoctor.querySelectorAll('option[data-departamento]')).map(opt => ({
      value: opt.value,
      text: opt.text,
      departamento: opt.dataset.departamento
    }));

    selectDepartamento.addEventListener('change', function () {
      const departamento = this.value;
      selectDoctor.innerHTML = '<option value="">Selecciona un doctor</option>';

      allDoctorOptions.forEach(opt => {
        if (!departamento || opt.departamento === departamento) {
          const option = document.createElement('option');
          option.value = opt.value;
          option.textContent = opt.text;
          option.dataset.departamento = opt.departamento;
          selectDoctor.appendChild(option);
        }
      });
    });
  });
</script>



</html>
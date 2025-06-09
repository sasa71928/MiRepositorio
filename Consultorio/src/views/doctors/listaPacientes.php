<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Mis Pacientes</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/doctor.css" />
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include_once __DIR__.'/../layouts/header.php'; ?>

    <main class="main">
        <!-- Sección de lista de pacientes -->
        <section id="patients-list" class="patients-list section">
            <div class="container">
                <div class="page-header">
                    <h2>Mis Pacientes</h2>
                </div>

                <!-- Filtros y búsqueda -->
                <div class="filters-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar paciente...">
                    </div>
                </div>

                <table class="patients-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Género</th>
                            <th>Teléfono</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pacientes as $paciente): ?>
                            <tr>
                                <td><?= $paciente['id'] ?></td>
                                <td><?= htmlspecialchars($paciente['first_name'] . ' ' . $paciente['last_name']) ?></td>
                                <td>
                                    <?php
                                        $nacimiento = new DateTime($paciente['birthdate']);
                                        $hoy = new DateTime();
                                        $edad = $hoy->diff($nacimiento)->y;
                                        echo $edad ;
                                    ?>
                                </td>
                                <td><?= $paciente['gender'] === 'F' ? 'Femenino' : 'Masculino' ?></td>
                                <td><?= $paciente['phone'] ?></td>
                                <td class="actions-cell">
                                <a href="#" class="action-icon ver-historial" 
                                data-id="<?= $paciente['id'] ?>" 
                                data-nombre="<?= htmlspecialchars($paciente['first_name'] . ' ' . $paciente['last_name']) ?>"
                                data-birthdate="<?= $paciente['birthdate'] ?>"
                                data-genero="<?= $paciente['gender'] ?>"
                                data-telefono="<?= $paciente['phone'] ?>"
                                data-email="<?= $paciente['email'] ?? '-' ?>"
                                data-direccion="<?= $paciente['address'] ?? '-' ?>"
                                data-grupo="<?= $paciente['blood_type'] ?? '-' ?>"
                                data-alergias="<?= $paciente['allergies'] ?? '-' ?>"
                                data-enfermedades="<?= $paciente['chronic_diseases'] ?? '-' ?>"
                                data-medicamentos="<?= $paciente['current_medications'] ?? '-' ?>"
                                data-historial="<?= $paciente['historial'] ?? '-' ?>"
                                data-updated="<?= $paciente['updated_at'] ?? '-' ?>"
                                title="Ver historial médico">
                                <i class="fas fa-eye"></i>
                                </a>


                            </tr>
                        </td>

                        <?php endforeach; ?>
                    </tbody>
                </table>


                <!-- Paginación -->
                <div class="pagination">
                    <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="page-btn active">1</button>
                    <button class="page-btn">2</button>
                    <button class="page-btn">3</button>
                    <span class="page-ellipsis">...</span>
                    <button class="page-btn">10</button>
                    <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </section>

        <!-- Sección de vista detallada de paciente (oculta por defecto) -->
<section id="patient-detail" class="patient-detail section" style="display: none;">
    <div class="container">
        <div class="page-header">
            <div class="back-button">
                <button class="btn-outline"><i class="fas fa-arrow-left"></i> Volver a la lista</button>
            </div>
            <h2>Detalles del Paciente</h2>
        </div>

        <div class="patient-profile">
            <div class="profile-header">
            <div class="profile-info">
                <h3 id="detalle-nombre">Cargando...</h3>
                <p id="detalle-edad-genero"></p>
                <p id="detalle-id"></p>
                <div class="profile-contact">
                    <span id="detalle-telefono"><i class="fas fa-phone"></i> </span>
                    <span id="detalle-email"><i class="fas fa-envelope"></i> </span>
                </div>
            </div>

            </div>

            <div class="profile-tabs">
            <button class="tab-btn active" data-target="tab-info">Información</button>
            <button class="tab-btn" data-target="tab-historial">Historial Médico</button>
            </div>

            <div class="profile-content">
            <!-- Información personal, contacto, médica -->
            <div class="tab-content active" id="tab-info">
                <div id="contenidoInfoPaciente">Cargando datos del paciente...</div>
            </div>

            <!-- Contenido del historial médico -->
            <div class="tab-content" id="tab-historial">
                <div id="contenidoHistorialPaciente">Cargando historial médico...</div>
            </div>
            </div>
        </div>
    </div>
</section>
    </main>



<script>
  document.addEventListener('DOMContentLoaded', () => {
    const patientsList = document.getElementById('patients-list');
    const patientDetail = document.getElementById('patient-detail');
    const backButton = document.querySelector('.back-button .btn-outline');

    document.querySelectorAll('.ver-historial').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();

        patientsList.style.display = 'none';
        patientDetail.style.display = 'block';

        const contenido = document.getElementById('contenidoInfoPaciente');

        const p = {
        first_name: btn.dataset.nombre.split(' ')[0],
        last_name: btn.dataset.nombre.split(' ').slice(1).join(' '),
        birthdate: btn.dataset.birthdate,
        gender: btn.dataset.genero,
        phone: btn.dataset.telefono,
        email: btn.dataset.email,
        address: btn.dataset.direccion,
        blood_type: btn.dataset.grupo,
        allergies: btn.dataset.alergias,
        chronic_diseases: btn.dataset.enfermedades,
        current_medications: btn.dataset.medicamentos,
        historial: btn.dataset.historial || '-',
        updated_at: btn.dataset.updated
        };



                // Calcular edad
        const nacimiento = new Date(p.birthdate);
        const hoy = new Date();
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        const edadTexto = `${edad} años`;

        // Actualizar encabezado del detalle
        document.getElementById('detalle-nombre').textContent = `${p.first_name} ${p.last_name}`;
        document.getElementById('detalle-edad-genero').textContent = `${edadTexto} - ${p.gender === 'F' ? 'Femenino' : 'Masculino'}`;
        document.getElementById('detalle-id').textContent = `ID: ${btn.dataset.id}`;
        document.getElementById('detalle-telefono').innerHTML = `<i class="fas fa-phone"></i> ${p.phone}`;
        document.getElementById('detalle-email').innerHTML = `<i class="fas fa-envelope"></i> ${p.email}`;

        contenido.innerHTML = `
        <div class="info-section">
            <h4>Información Personal</h4>
            <div class="info-grid">
            <div class="info-item"><span class="info-label">Nombre Completo</span><span class="info-value">${p.first_name} ${p.last_name}</span></div>
            <div class="info-item"><span class="info-label">Fecha de Nacimiento</span><span class="info-value">${p.birthdate}</span></div>
            <div class="info-item"><span class="info-label">Género</span><span class="info-value">${p.gender === 'F' ? 'Femenino' : 'Masculino'}</span></div>
            </div>
        </div>

        <div class="info-section">
            <h4>Información de Contacto</h4>
            <div class="info-grid">
            <div class="info-item"><span class="info-label">Teléfono</span><span class="info-value">${p.phone}</span></div>
            <div class="info-item"><span class="info-label">Email</span><span class="info-value">${p.email}</span></div>
            <div class="info-item"><span class="info-label">Dirección</span><span class="info-value">${p.address}</span></div>
            </div>
        </div>

        <div class="info-section">
        <h4>Información Médica</h4>
        <div class="info-grid">
            <div class="info-item">
            <span class="info-label">Alergias</span>
            <span class="info-value">${p.allergies || '-'}</span>
            </div>
            <div class="info-item">
            <span class="info-label">Enfermedades Crónicas</span>
            <span class="info-value">${p.chronic_diseases || '-'}</span>
            </div>
            <div class="info-item">
            <span class="info-label">Medicación Actual</span>
            <span class="info-value">${p.current_medications || '-'}</span>
            </div>
            <div class="info-item">
            <span class="info-label">Grupo Sanguíneo</span>
            <span class="info-value">${p.blood_type || '-'}</span>
            </div>
        </div>
        </div>

        `;

        document.getElementById('contenidoHistorialPaciente').innerHTML = `
        <div class="info-section">
            <h4>Historial Médico</h4>
            <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Alergias</span>
                <span class="info-value">${p.allergies || '-'}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Enfermedades Crónicas</span>
                <span class="info-value">${p.chronic_diseases || '-'}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Medicación Actual</span>
                <span class="info-value">${p.current_medications || '-'}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Última Actualización</span>
                <span class="info-value">${p.updated_at || '-'}</span>
            </div>
            </div>
        </div>
        `;

    });
    });


    backButton.addEventListener('click', () => {
      patientDetail.style.display = 'none';
      patientsList.style.display = 'block';
    });
  });


  // Cambiar pestañas
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const target = btn.getAttribute('data-target');

    // Activar botón
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Mostrar contenido correspondiente
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
    });

    const selectedTab = document.getElementById(target);
    if (selectedTab) selectedTab.classList.add('active');
  });
});


</script>


</body>
</html>

<style>
    .tab-content { display: none; }
.tab-content.active { display: block; }

</style>
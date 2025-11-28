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
                        <input type="text" id="busquedaPaciente" placeholder="Buscar paciente...">
                    </div>
                </div>

                <div class="table-responsive">
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
                            <?php if (empty($pacientes)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                                        No tienes pacientes asignados.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pacientes as $paciente): ?>
                                    <tr>
                                        <td><?= $paciente['id'] ?></td>
                                        <td><?= htmlspecialchars($paciente['first_name'] . ' ' . $paciente['last_name']) ?></td>
                                        <td>
                                            <?php
                                                if (!empty($paciente['birthdate']) && $paciente['birthdate'] != '0000-00-00') {
                                                    try {
                                                        $nac = new DateTime($paciente['birthdate']);
                                                        $hoy = new DateTime();
                                                        echo $hoy->diff($nac)->y . ' años';
                                                    } catch (Exception $e) { echo "-"; }
                                                } else { echo "-"; }
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
                                            data-email="<?= htmlspecialchars($paciente['email'] ?? '-') ?>"
                                            data-direccion="<?= htmlspecialchars($paciente['address'] ?? '-') ?>"
                                            data-grupo="<?= htmlspecialchars($paciente['blood_type'] ?? '-') ?>"
                                            data-alergias="<?= htmlspecialchars($paciente['allergies'] ?? '-') ?>"
                                            data-enfermedades="<?= htmlspecialchars($paciente['chronic_diseases'] ?? '-') ?>"
                                            data-medicamentos="<?= htmlspecialchars($paciente['current_medications'] ?? '-') ?>"
                                            data-updated="<?= $paciente['updated_at'] ?? '-' ?>"
                                            data-citas='<?= htmlspecialchars(json_encode($paciente['citas'] ?? []), ENT_QUOTES, 'UTF-8') ?>'
                                            title="Ver historial médico">
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
                    <!-- Encabezado del Perfil -->
                    <div class="profile-header">
                        <div class="profile-info">
                            <h3 id="detalle-nombre">Cargando...</h3>
                            <p id="detalle-edad-genero"></p>
                            <p id="detalle-id" style="font-size: 0.9em; color: #888;"></p>
                            <div class="profile-contact">
                                <span id="detalle-telefono"><i class="fas fa-phone"></i> </span>
                                <span id="detalle-email"><i class="fas fa-envelope"></i> </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pestañas -->
                    <div class="profile-tabs">
                        <button class="tab-btn active" data-target="tab-info">Información</button>
                        <button class="tab-btn" data-target="tab-historial">Historial Médico</button>
                        <button class="tab-btn" data-target="tab-citas">Citas</button>
                    </div>

                    <!-- Contenido de Pestañas -->
                    <div class="profile-content">
                        <div class="tab-content active" id="tab-info">
                            <div id="contenidoInfoPaciente">Cargando...</div>
                        </div>
                        <div class="tab-content" id="tab-historial">
                            <div id="contenidoHistorialPaciente">Cargando...</div>
                        </div>
                        <div class="tab-content" id="tab-citas">
                            <div id="contenidoCitasPaciente">Cargando...</div>
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

        // Evento para ver detalles
        document.querySelectorAll('.ver-historial').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                // Cambiar vista
                patientsList.style.display = 'none';
                patientDetail.style.display = 'block';

                // Parsear datos con seguridad
                let citasData = [];
                try {
                    citasData = JSON.parse(btn.dataset.citas || '[]');
                } catch (err) { console.error("Error JSON Citas", err); }

                const p = {
                    id: btn.dataset.id,
                    nombreComp: btn.dataset.nombre,
                    birthdate: btn.dataset.birthdate,
                    gender: btn.dataset.genero,
                    phone: btn.dataset.telefono,
                    email: btn.dataset.email,
                    address: btn.dataset.direccion,
                    blood_type: btn.dataset.grupo,
                    allergies: btn.dataset.alergias,
                    chronic_diseases: btn.dataset.enfermedades,
                    current_medications: btn.dataset.medicamentos,
                    updated_at: btn.dataset.updated,
                    citas: citasData
                };

                // Calcular edad
                let edadTexto = 'N/A';
                if (p.birthdate && p.birthdate !== '0000-00-00') {
                    const nac = new Date(p.birthdate);
                    const hoy = new Date();
                    let edad = hoy.getFullYear() - nac.getFullYear();
                    const m = hoy.getMonth() - nac.getMonth();
                    if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) edad--;
                    edadTexto = `${edad} años`;
                }

                // Rellenar Encabezado
                document.getElementById('detalle-nombre').textContent = p.nombreComp;
                document.getElementById('detalle-edad-genero').textContent = `${edadTexto} - ${p.gender === 'F' ? 'Femenino' : 'Masculino'}`;
                document.getElementById('detalle-id').textContent = `ID Paciente: ${p.id}`;
                document.getElementById('detalle-telefono').innerHTML = `<i class="fas fa-phone"></i> ${p.phone}`;
                document.getElementById('detalle-email').innerHTML = `<i class="fas fa-envelope"></i> ${p.email}`;

                // Tab 1: Información
                document.getElementById('contenidoInfoPaciente').innerHTML = `
                    <div class="info-section">
                        <h4>Datos Personales</h4>
                        <div class="info-grid">
                            <div class="info-item"><span class="info-label">Dirección</span><span class="info-value">${p.address}</span></div>
                            <div class="info-item"><span class="info-label">Email</span><span class="info-value">${p.email}</span></div>
                            <div class="info-item"><span class="info-label">Teléfono</span><span class="info-value">${p.phone}</span></div>
                        </div>
                    </div>`;

                // Tab 2: Historial Médico
                document.getElementById('contenidoHistorialPaciente').innerHTML = `
                    <div class="info-section">
                        <h4>Ficha Médica</h4>
                        <div class="info-grid">
                            <div class="info-item"><span class="info-label">Grupo Sanguíneo</span><span class="info-value">${p.blood_type || '-'}</span></div>
                            <div class="info-item"><span class="info-label">Alergias</span><span class="info-value">${p.allergies || '-'}</span></div>
                            <div class="info-item"><span class="info-label">Enfermedades Crónicas</span><span class="info-value">${p.chronic_diseases || '-'}</span></div>
                            <div class="info-item"><span class="info-label">Medicación Actual</span><span class="info-value">${p.current_medications || '-'}</span></div>
                            <div class="info-item"><span class="info-label">Última Actualización</span><span class="info-value">${p.updated_at || '-'}</span></div>
                        </div>
                    </div>`;

                // Tab 3: Citas
                let citasHTML = '<p style="text-align:center; color:#666; padding:20px;">No hay historial de citas.</p>';
                if (p.citas.length > 0) {
                    const filas = p.citas.map(c => `
                        <tr>
                            <td>${c.scheduled_at || '-'}</td>
                            <td>${c.reason || '-'}</td>
                            <td><span class="status ${c.status}">${c.status}</span></td>
                        </tr>
                    `).join('');
                    
                    citasHTML = `
                        <div class="info-section">
                            <table class="patients-table" style="width:100%">
                                <thead><tr><th>Fecha</th><th>Motivo</th><th>Estado</th></tr></thead>
                                <tbody>${filas}</tbody>
                            </table>
                        </div>`;
                }
                document.getElementById('contenidoCitasPaciente').innerHTML = citasHTML;
            });
        });

        // Volver
        backButton.addEventListener('click', () => {
            patientDetail.style.display = 'none';
            patientsList.style.display = 'block';
        });

        // Pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.getAttribute('data-target');
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
                document.getElementById(target).classList.add('active');
            });
        });

        // Buscador
        const inputBusqueda = document.getElementById('busquedaPaciente');
        inputBusqueda.addEventListener('input', () => {
            const filtro = inputBusqueda.value.toLowerCase();
            const filas = document.querySelectorAll('.patients-table tbody tr');
            filas.forEach(fila => {
                // Ignoramos la fila de "no hay pacientes" si existe
                if(fila.cells.length < 2) return;
                const nombre = fila.cells[1]?.textContent.toLowerCase();
                const telefono = fila.cells[4]?.textContent.toLowerCase();
                const coincide = nombre.includes(filtro) || telefono.includes(filtro);
                fila.style.display = coincide ? '' : 'none';
            });
        });
    });
    </script>

    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 10px; }
        .info-item { display: flex; flex-direction: column; }
        .info-label { font-size: 0.85em; color: #666; }
        .info-value { font-weight: 600; color: #333; }
        .status { padding: 3px 8px; border-radius: 10px; font-size: 0.8em; text-transform: uppercase; }
        .status.pendiente { background: #fff3cd; color: #856404; }
        .status.completada { background: #d4edda; color: #155724; }
        .status.cancelada { background: #f8d7da; color: #721c24; }
    </style>
</body>
</html>
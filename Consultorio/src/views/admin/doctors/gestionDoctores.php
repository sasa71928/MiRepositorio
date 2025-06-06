<?php
require_once __DIR__ . '/../../../controllers/AdminController.php';

$departamentos = obtenerDepartamentos();

$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$doctoresPorPagina = 5;
$offset = ($paginaActual - 1) * $doctoresPorPagina;

$filtroDepartamento = $_GET['departamento'] ?? '';
$nombreBuscado = $_GET['nombre'] ?? '';

$doctores = obtenerDoctores($doctoresPorPagina, $offset, $filtroDepartamento, $nombreBuscado);
$totalDoctores = contarDoctores($filtroDepartamento, $nombreBuscado);
$totalPaginas = ceil($totalDoctores / $doctoresPorPagina);

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Gestión de Doctores</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<style>
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 15px;
    }

    .form-row .form-group {
        flex: 1 1 45%;
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }
    }

</style>

<body>
    <?php include_once __DIR__ . '/../../layouts/header.php'; ?>
    <main class="main">
        <div class="dashboard-container">
    
            <!-- Contenido Principal -->
            <div class="dashboard-content">
                <!-- Seccion de arriba -->
                <section class="content-header">
                    <div class="header-title">
                        <h1>Gestión de Doctores</h1>
                        <p>Administra los doctores de la clínica</p>
                    </div>
                    <div class="header-actions">
                        <button id="btnAgregarDoctor" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Agregar Doctor
                        </button>
                    </div>
                </section>
                
                <!-- Filtros y Búsqueda -->
                <form method="GET" action="<?= BASE_URL ?>/adminDoctors/gestionar">
                    <section class="filters-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input
                                type="text"
                                name="nombre"
                                placeholder="Buscar doctor..."
                                value="<?= htmlspecialchars($nombreBuscado) ?>"
                            >
                        </div>

                        <div class="filter-options">
                            <select name="departamento">
                                <option value="">Todos los departamentos</option>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?= htmlspecialchars($dep['name']) ?>"
                                        <?= ($filtroDepartamento === $dep['name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dep['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                <div id="filter-actions" class="btn btn-primary">
                    <a href="<?= BASE_URL ?>/adminDoctors/gestionar" class="btn-limpiar">Limpiar filtros</a>
                    <style>
                            .btn-limpiar {
                            text-decoration: none !important;  /* elimina subrayado */
                        }
                        .btn-limpiar:visited {
                            color: white;
                            text-decoration: none;
                        }
                     </style>

                </div>

                    </section>
                </form>

                
                <!-- Lista de Doctores -->
                <section class="doctors-list-section">
                    <div class="doctors-table-container">
                        <table class="doctors-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Departamento</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($doctores) === 0): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                                            No se encontraron doctores que coincidan con la búsqueda o filtro seleccionado.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($doctores as $doctor): ?>
                                        <tr data-id="<?= $doctor['id'] ?>">
                                            <td><?= htmlspecialchars($doctor['id']) ?></td>
                                            <td><?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?></td>
                                            <td><?= htmlspecialchars($doctor['departamento']) ?></td>
                                            <td><?= htmlspecialchars($doctor['email']) ?></td>
                                            <td><?= htmlspecialchars($doctor['phone']) ?></td>
                                            <td class="actions-cell">
                                                <button class="btn-icon btn-edit"
                                                    data-id="<?= $doctor['id'] ?>"
                                                    data-username="<?= htmlspecialchars($doctor['username']) ?>"
                                                    data-first_name="<?= htmlspecialchars($doctor['first_name']) ?>"
                                                    data-last_name="<?= htmlspecialchars($doctor['last_name']) ?>"
                                                    data-birthdate="<?= $doctor['birthdate'] ?>"
                                                    data-gender="<?= $doctor['gender'] ?>"
                                                    data-address="<?= htmlspecialchars($doctor['address']) ?>"
                                                    data-city="<?= htmlspecialchars($doctor['city']) ?>"
                                                    data-departamento_id="<?= $doctor['department_id'] ?>"
                                                    data-email="<?= htmlspecialchars($doctor['email']) ?>"
                                                    data-phone="<?= htmlspecialchars($doctor['phone']) ?>"
                                                    data-cedula="<?= htmlspecialchars($doctor['cedula_profesional']) ?>"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            <button class="btn-icon btn-delete" title="Eliminar"
                                                    data-id="<?= $doctor['id'] ?>"
                                                    data-name="<?= htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>


                        </table>
                    </div>
                    
                    <!-- Paginación -->
                        <div class="pagination">
                            <?php if ($paginaActual > 1): ?>
                                <a href="?pagina=<?= $paginaActual - 1 ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <a href="?pagina=<?= $i ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>"
                                class="pagination-btn <?= $i == $paginaActual ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($paginaActual < $totalPaginas): ?>
                                <a href="?pagina=<?= $paginaActual + 1 ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>" class="pagination-btn">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                </section>
            </div>
        </div>
    </main>
    
    <!-- Modal para Agregar Doctor -->
    <div id="modalAgregarDoctor" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Doctor</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formAgregarDoctor" action="<?= BASE_URL ?>/adminDoctors/crearDoctor" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Nombre de Usuario</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name">Nombre(s)</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="last_name">Apellidos</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Fecha de Nacimiento</label>
                            <input type="date" id="birthdate" name="birthdate" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">Género</label>
                            <select id="gender" name="gender" required>
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="address">Dirección</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ciudad</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="departamento">Departamento</label>
                            <select id="departamento" name="department_id" required>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="phone" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cedula">Cédula Profesional</label>
                            <input type="text" id="cedula" name="cedula" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña Temporal</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" id="btnCancelarAgregar">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnConfirmarAgregar">Guardar Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Modal de Confirmación para Eliminar Doctor -->
    <div id="modalConfirmarEliminar" class="modal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h2>Confirmar Eliminación</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirmation-message">
                    <i class="fas fa-exclamation-triangle warning-icon"></i>
                    <p>¿Está seguro que desea eliminar al doctor <span id="doctorName"></span>?</p>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="btnCancelarEliminar">Cancelar</button>
                <button class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal de Confirmación Exitosa -->
    <div id="modalExito" class="modal">
        <div class="modal-content modal-sm">
            <div class="modal-header">
                <h2>Operación Exitosa</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="success-message">
                    <i class="fas fa-check-circle success-icon"></i>
                    <p id="mensajeExito">La operación se ha completado con éxito.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="btnAceptarExito">Aceptar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Doctor -->
<div id="modalEditarDoctor" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar Doctor</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="formEditarDoctor" action="<?= BASE_URL ?>/adminDoctors/editarDoctor" method="POST">
                <input type="hidden" id="edit_id" name="edit_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_username">Nombre de Usuario</label>
                        <input type="text" id="edit_username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_first_name">Nombre(s)</label>
                        <input type="text" id="edit_first_name" name="first_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_last_name">Apellidos</label>
                    <input type="text" id="edit_last_name" name="last_name" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_birthdate">Fecha de Nacimiento</label>
                        <input type="date" id="edit_birthdate" name="birthdate" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_gender">Género</label>
                        <select id="edit_gender" name="gender" required>
                            <option value="">Seleccione...</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_address">Dirección</label>
                        <input type="text" id="edit_address" name="address" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_city">Ciudad</label>
                        <input type="text" id="edit_city" name="city" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_especialidad">Departamento</label>
                    <select id="edit_especialidad" name="departamento_id" required>
                        <?php foreach ($departamentos as $dep): ?>
                            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_email">Correo Electrónico</label>
                        <input type="email" id="edit_email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_telefono">Teléfono</label>
                        <input type="tel" id="edit_telefono" name="phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_cedula">Cédula Profesional</label>
                    <input type="text" id="edit_cedula" name="cedula" required>
                </div>

                <div class="form-group">
                    <label for="edit_password">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" id="edit_password" name="password">
                </div>
            </form>

        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="btnCancelarEditar">Cancelar</button>
            <button class="btn btn-primary" id="btnConfirmarEditar">Guardar Cambios</button>
        </div>
    </div>
</div>
    
    <script>
        // Funcionalidad para los modales
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a los modales
            const modalAgregarDoctor = document.getElementById('modalAgregarDoctor');
            const modalConfirmarEliminar = document.getElementById('modalConfirmarEliminar');
            const modalExito = document.getElementById('modalExito');
            
            // Botones para abrir modales
            const btnAgregarDoctor = document.getElementById('btnAgregarDoctor');
            const btnsEliminar = document.querySelectorAll('.btn-delete');
            
            // Botones para cerrar modales
            const closeButtons = document.querySelectorAll('.close-modal');
            
            // Botones de acción en modales
            const btnCancelarAgregar = document.getElementById('btnCancelarAgregar');
            const btnConfirmarAgregar = document.getElementById('btnConfirmarAgregar');
            const btnCancelarEliminar = document.getElementById('btnCancelarEliminar');
            const btnConfirmarEliminar = document.getElementById('btnConfirmarEliminar');
            const btnAceptarExito = document.getElementById('btnAceptarExito');
            
            // Abrir modal de agregar doctor
            btnAgregarDoctor.addEventListener('click', function() {
                modalAgregarDoctor.style.display = 'flex';
            });
            
            // Configurar botones de eliminar
            btnsEliminar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const doctorId = this.getAttribute('data-id');
                    const doctorName = this.getAttribute('data-name');
                    document.getElementById('doctorName').textContent = doctorName;
                    modalConfirmarEliminar.setAttribute('data-id', doctorId);
                    modalConfirmarEliminar.style.display = 'flex';
                });
            });
            
            // Cerrar modales con botón X
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.modal').style.display = 'none';
                });
            });
            
            // Cerrar modal al hacer clic fuera del contenido
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal')) {
                    event.target.style.display = 'none';
                }
            });
            
            // Cancelar agregar doctor
            btnCancelarAgregar.addEventListener('click', function() {
                modalAgregarDoctor.style.display = 'none';
                document.getElementById('formAgregarDoctor').reset();
            });
            
            // Confirmar agregar doctor
            document.getElementById('btnConfirmarAgregar').addEventListener('click', async function () {
                const form = document.getElementById('formAgregarDoctor');
                
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const formData = new FormData(form);
                
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        // Ocultar modal de agregar
                        document.getElementById('modalAgregarDoctor').style.display = 'none';
                        
                        // Mostrar modal de éxito
                        document.getElementById('mensajeExito').textContent = 'El doctor ha sido agregado exitosamente.';
                        document.getElementById('modalExito').style.display = 'flex';

                        // Resetear formulario
                        form.reset();
                    } else {
                        alert('❌ Error al guardar el doctor.');
                    }
                } catch (err) {
                    console.error('Error de red o JS:', err);
                    alert('❌ Ocurrió un error inesperado.');
                }
            });

            
            // Cancelar eliminar doctor
            btnCancelarEliminar.addEventListener('click', function() {
                modalConfirmarEliminar.style.display = 'none';
            });
            
            // Confirmar eliminar doctor
            btnConfirmarEliminar.addEventListener('click', function() {
                const doctorId = modalConfirmarEliminar.getAttribute('data-id');
                // Aquí iría la lógica para eliminar el doctor del servidor
                modalConfirmarEliminar.style.display = 'none';
                document.getElementById('mensajeExito').textContent = 'El doctor ha sido eliminado exitosamente.';
                modalExito.style.display = 'flex';
            });
            
            // Aceptar mensaje de éxito
            btnAceptarExito.addEventListener('click', function() {
                modalExito.style.display = 'none';
                // Opcionalmente, recargar la página para mostrar los cambios
                // window.location.reload();
            });

            // Referencias para el modal de editar
        const modalEditarDoctor = document.getElementById('modalEditarDoctor');
        const btnsEditar = document.querySelectorAll('.btn-edit');
        const btnCancelarEditar = document.getElementById('btnCancelarEditar');
        const btnConfirmarEditar = document.getElementById('btnConfirmarEditar');

        // Configurar botones de editar
        btnsEditar.forEach(btn => {
            btn.addEventListener('click', function () {
                const d = this.dataset;

                document.getElementById('edit_id').value = d.id;
                document.getElementById('edit_username').value = d.username;
                document.getElementById('edit_first_name').value = d.first_name;
                document.getElementById('edit_last_name').value = d.last_name;
                document.getElementById('edit_birthdate').value = d.birthdate;
                document.getElementById('edit_gender').value = d.gender;
                document.getElementById('edit_address').value = d.address;
                document.getElementById('edit_city').value = d.city;
                document.getElementById('edit_email').value = d.email;
                document.getElementById('edit_telefono').value = d.phone;
                document.getElementById('edit_cedula').value = d.cedula;

                // Seleccionar el departamento
                const depSelect = document.getElementById('edit_especialidad');
                if (d.departamento_id) {
                    depSelect.value = d.departamento_id;
                }

                modalEditarDoctor.style.display = 'flex';
            });
        });



        // Cancelar editar doctor
        btnCancelarEditar.addEventListener('click', function() {
            modalEditarDoctor.style.display = 'none';
            document.getElementById('formEditarDoctor').reset();
        });

            // Confirmar editar doctor
btnConfirmarEditar.addEventListener('click', async function () {
    const form = document.getElementById('formEditarDoctor');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const data = {
        id: document.getElementById('edit_id').value,
        username: document.getElementById('edit_username').value,
        first_name: document.getElementById('edit_first_name').value,
        last_name: document.getElementById('edit_last_name').value,
        birthdate: document.getElementById('edit_birthdate').value,
        gender: document.getElementById('edit_gender').value,
        address: document.getElementById('edit_address').value,
        city: document.getElementById('edit_city').value,
        departamento_id: document.getElementById('edit_especialidad').value,
        email: document.getElementById('edit_email').value,
        phone: document.getElementById('edit_telefono').value,
        cedula: document.getElementById('edit_cedula').value,
        password: document.getElementById('edit_password').value
    };

    console.log("🟢 Datos capturados para actualizar:", data);

    try {
        const response = await fetch('<?= BASE_URL ?>/adminDoctors/editarDoctor', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

            if (response.ok) {
                modalEditarDoctor.style.display = 'none';
                document.getElementById('mensajeExito').textContent = 'El doctor ha sido actualizado exitosamente.';
                modalExito.style.display = 'flex';

                // Actualizar la fila en la tabla (sin recargar la página)
                const fila = document.querySelector(`tr[data-id="${data.id}"]`);
                if (fila) {
                    fila.children[1].textContent = data.first_name + ' ' + data.last_name;
                    fila.children[2].textContent = document.querySelector(`#edit_especialidad option[value="${data.departamento_id}"]`)?.textContent || '';
                    fila.children[3].textContent = data.email;
                    fila.children[4].textContent = data.phone;

                    // Actualizar atributos del botón de editar
                    const btnEditar = fila.querySelector('.btn-edit');
                    if (btnEditar) {
                        btnEditar.dataset.username = data.username;
                        btnEditar.dataset.first_name = data.first_name;
                        btnEditar.dataset.last_name = data.last_name;
                        btnEditar.dataset.birthdate = data.birthdate;
                        btnEditar.dataset.gender = data.gender;
                        btnEditar.dataset.address = data.address;
                        btnEditar.dataset.city = data.city;
                        btnEditar.dataset.departamento_id = data.departamento_id;
                        btnEditar.dataset.email = data.email;
                        btnEditar.dataset.phone = data.phone;
                        btnEditar.dataset.cedula = data.cedula;
                    }
                }
                    form.reset();
                    } else {
                    alert('❌ Error al editar el doctor.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Ocurrió un error inesperado.');
            }
        });


            });

        document.addEventListener('DOMContentLoaded', function () {
    const filtroDepartamento = document.getElementById('filtroDepartamento');
    const buscarDoctor = document.getElementById('buscarDoctor');

    // Evento para filtro de departamento
    filtroDepartamento.addEventListener('change', () => {
        actualizarURL();
    });

    // Evento para enter en búsqueda
    buscarDoctor.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            actualizarURL();
        }
    });

    function actualizarURL() {
        const departamento = filtroDepartamento.value;
        const buscar = buscarDoctor.value.trim();
        const parametros = new URLSearchParams();

        if (departamento) parametros.set('departamento', departamento);
        if (buscar) parametros.set('buscar', buscar);

        window.location.href = '?' + parametros.toString();
    }
});

    document.addEventListener('DOMContentLoaded', function () {
        const selectDepartamento = document.querySelector('select[name="departamento"]');
        if (selectDepartamento) {
            selectDepartamento.addEventListener('change', function () {
                this.form.submit(); // envía el formulario automáticamente
            });
        }
    });
    </script>
</body>
</html>
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
    .btn-limpiar {
        text-decoration: none !important;
        color: white;
    }
    .btn-limpiar:visited {
        color: white;
    }
</style>

<body>
    <?php include_once __DIR__ . '/../../layouts/header.php'; ?>
    <main class="main">
        <div class="dashboard-container">
            <div class="dashboard-content">
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
                
                <form method="GET" action="<?= BASE_URL ?>/adminDoctors/gestionar">
                    <section class="filters-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="nombre" placeholder="Buscar doctor..." value="<?= htmlspecialchars($nombreBuscado) ?>">
                        </div>

                        <div class="filter-options">
                            <select name="departamento" id="filtroDepartamento">
                                <option value="">Todos los departamentos</option>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?= htmlspecialchars($dep['name']) ?>" <?= ($filtroDepartamento === $dep['name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dep['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="filter-actions" class="btn btn-primary">
                            <a href="<?= BASE_URL ?>/adminDoctors/gestionar" class="btn-limpiar">Limpiar filtros</a>
                        </div>
                    </section>
                </form>

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
                                        <td colspan="6" style="text-align: center; padding: 20px; color: #999;">No se encontraron doctores.</td>
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
                    
                    <div class="pagination">
                        <?php if ($paginaActual > 1): ?>
                            <a href="?pagina=<?= $paginaActual - 1 ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>" class="pagination-btn"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                            <a href="?pagina=<?= $i ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>" class="pagination-btn <?= $i == $paginaActual ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($paginaActual < $totalPaginas): ?>
                            <a href="?pagina=<?= $paginaActual + 1 ?>&departamento=<?= urlencode($filtroDepartamento) ?>&nombre=<?= urlencode($nombreBuscado) ?>" class="pagination-btn"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
    <!-- Modal Agregar -->
    <div id="modalAgregarDoctor" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Doctor</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formAgregarDoctor" action="<?= BASE_URL ?>/adminDoctors/crearDoctor" method="POST">
                    <div class="form-row">
                        <div class="form-group"><label>Usuario</label><input type="text" name="username" required></div>
                        <div class="form-group"><label>Nombre(s)</label><input type="text" name="first_name" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Apellidos</label><input type="text" name="last_name" required></div>
                        <div class="form-group"><label>Fecha Nacimiento</label><input type="date" name="birthdate" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Género</label>
                            <select name="gender" required>
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Dirección</label><input type="text" name="address" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Ciudad</label><input type="text" name="city" required></div>
                        <div class="form-group">
                            <label>Departamento</label>
                            <select name="department_id" required>
                                <?php foreach ($departamentos as $dep): ?>
                                    <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                        <div class="form-group"><label>Teléfono</label><input type="tel" name="phone" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Cédula</label><input type="text" name="cedula" required></div>
                        <div class="form-group"><label>Contraseña</label><input type="password" name="password" required></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline close-modal-btn">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar -->
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
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline close-modal-btn">Cancelar</button>
                <button class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Exito -->
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

    <!-- Modal Editar -->
    <div id="modalEditarDoctor" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Doctor</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formEditarDoctor" action="<?= BASE_URL ?>/adminDoctors/editarDoctor" method="POST">
                    <!-- El ID del doctor es crucial -->
                    <input type="hidden" id="edit_id" name="id">

                    <div class="form-row">
                        <div class="form-group"><label>Usuario</label><input type="text" id="edit_username" name="username" required></div>
                        <div class="form-group"><label>Nombre(s)</label><input type="text" id="edit_first_name" name="first_name" required></div>
                    </div>
                    <div class="form-group"><label>Apellidos</label><input type="text" id="edit_last_name" name="last_name" required></div>
                    <div class="form-row">
                        <div class="form-group"><label>Fecha Nacimiento</label><input type="date" id="edit_birthdate" name="birthdate" required></div>
                        <div class="form-group">
                            <label>Género</label>
                            <select id="edit_gender" name="gender" required>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Dirección</label><input type="text" id="edit_address" name="address" required></div>
                        <div class="form-group"><label>Ciudad</label><input type="text" id="edit_city" name="city" required></div>
                    </div>
                    <div class="form-group">
                        <label>Departamento</label>
                        <select id="edit_especialidad" name="departamento_id" required>
                            <?php foreach ($departamentos as $dep): ?>
                                <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Email</label><input type="email" id="edit_email" name="email" required></div>
                        <div class="form-group"><label>Teléfono</label><input type="tel" id="edit_telefono" name="phone" required></div>
                    </div>
                    <div class="form-group"><label>Cédula</label><input type="text" id="edit_cedula" name="cedula" required></div>
                    <div class="form-group"><label>Nueva Contraseña (opcional)</label><input type="password" id="edit_password" name="password"></div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline close-modal-btn">Cancelar</button>
                        <!-- Importante: type="button" para manejarlo con JS y FormData -->
                        <button type="button" class="btn btn-primary" id="btnConfirmarEditar">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos
        const modals = document.querySelectorAll('.modal');
        const closeBtns = document.querySelectorAll('.close-modal, .close-modal-btn');
        
        // Cerrar modales
        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => modals.forEach(m => m.style.display = 'none'));
        });
        
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) e.target.style.display = 'none';
        });

        // --- AGREGAR DOCTOR ---
        document.getElementById('btnAgregarDoctor').addEventListener('click', () => {
            document.getElementById('modalAgregarDoctor').style.display = 'flex';
        });

        // --- EDITAR DOCTOR ---
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                const d = this.dataset;
                // Llenar formulario
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
                document.getElementById('edit_especialidad').value = d.departamento_id;
                
                document.getElementById('modalEditarDoctor').style.display = 'flex';
            });
        });

        // --- GUARDAR EDICIÓN (CORRECCIÓN CLAVE) ---
        document.getElementById('btnConfirmarEditar').addEventListener('click', async function() {
            const form = document.getElementById('formEditarDoctor');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // [CORRECCIÓN] Usar FormData para enviar como formulario tradicional (POST)
            // Esto asegura que $_POST se llene correctamente en el servidor.
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                // Como el controlador hace un redirect, fetch seguirá la redirección.
                // Si la respuesta es ok (200), asumimos éxito.
                if (response.ok) {
                    document.getElementById('modalEditarDoctor').style.display = 'none';
                    document.getElementById('mensajeExito').textContent = 'Doctor actualizado correctamente.';
                    document.getElementById('modalExito').style.display = 'flex';
                } else {
                    alert('Hubo un error al guardar los cambios.');
                }
            } catch (err) {
                console.error(err);
                alert('Error de conexión.');
            }
        });

        // --- ELIMINAR ---
        // (Lógica de UI para mostrar modal, la eliminación real dependerá de tu implementación en backend)
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                document.getElementById('doctorName').textContent = name;
                document.getElementById('modalConfirmarEliminar').style.display = 'flex';
                // Aquí podrías setear el ID en el botón de confirmar para hacer el fetch de borrado
            });
        });

        // Recargar al aceptar éxito
        document.getElementById('btnAceptarExito').addEventListener('click', () => {
            window.location.reload();
        });
        
        // Filtro automático
        const selectDep = document.getElementById('filtroDepartamento');
        if(selectDep) {
            selectDep.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CliniGest - Gestión de Doctores</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
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
                <section class="filters-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Buscar doctor...">
                    </div>
                    <div class="filter-options">
                        <select>
                            <option value="">Todas los departamentos</option>
                            <option value="cardiologia">Pediatría</option>
                            <option value="pediatria">Medicina General</option>
                            <option value="general">Imagenología</option>
                            <option value="dermatologia">Laboratorio Clínico</option>
                            <option value="dermatologia">Enfermería</option>
                        </select>
                    </div>
                </section>
                
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
                                <tr>
                                    <td>001</td>
                                    <td>Dr. Carlos Rodríguez</td>
                                    <td>Medicina General</td>
                                    <td>carlos.rodriguez@clinigest.com</td>
                                    <td>555-123-4567</td>
                                    <td class="actions-cell">
                                        <button class="btn-icon btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" title="Eliminar" data-id="001" data-name="Dr. Carlos Rodríguez">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>002</td>
                                    <td>Dra. Ana Martínez</td>
                                    <td>Pediatría</td>
                                    <td>ana.martinez@clinigest.com</td>
                                    <td>555-234-5678</td>
                                    <td class="actions-cell">
                                        <button class="btn-icon btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" title="Eliminar" data-id="002" data-name="Dra. Ana Martínez">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>003</td>
                                    <td>Dr. Miguel Sánchez</td>
                                    <td>Cardiología</td>
                                    <td>miguel.sanchez@clinigest.com</td>
                                    <td>555-345-6789</td>
                                    <td class="actions-cell">
                                        <button class="btn-icon btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" title="Eliminar" data-id="003" data-name="Dr. Miguel Sánchez">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>004</td>
                                    <td>Dra. Laura Gómez</td>
                                    <td>Dermatología</td>
                                    <td>laura.gomez@clinigest.com</td>
                                    <td>555-456-7890</td>
                                    <td class="actions-cell">
                                        <button class="btn-icon btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-delete" title="Eliminar" data-id="004" data-name="Dra. Laura Gómez">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="pagination">
                        <button class="pagination-btn" disabled><i class="fas fa-chevron-left"></i></button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn">2</button>
                        <button class="pagination-btn">3</button>
                        <button class="pagination-btn"><i class="fas fa-chevron-right"></i></button>
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
                <form id="formAgregarDoctor">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="especialidad">Departamento</label>
                        <select id="especialidad" name="especialidad" required>
                            <option value="medicina_general">Medicina General</option>
                            <option value="pediatria">Pediatría</option>
                            <option value="cardiologia">Enfermería</option>
                            <option value="dermatologia">Imagenología</option>
                            <option value="neurologia">Laboratorio Clínico</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="cedula">Cédula Profesional</label>
                            <input type="text" id="cedula" name="cedula" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña Temporal</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" id="btnCancelarAgregar">Cancelar</button>
                <button class="btn btn-primary" id="btnConfirmarAgregar">Guardar Doctor</button>
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
            <form id="formEditarDoctor">
                <input type="hidden" id="edit_id" name="edit_id">
                <div class="form-group">
                    <label for="edit_nombre">Nombre Completo</label>
                    <input type="text" id="edit_nombre" name="edit_nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit_especialidad">Departamento</label>
                    <select id="edit_especialidad" name="edit_especialidad" required>
                        <option value="medicina_general">Medicina General</option>
                        <option value="pediatria">Pediatría</option>
                        <option value="cardiologia">Enfermería</option>
                        <option value="dermatologia">Imagenología</option>
                        <option value="neurologia">Laboratorio Clínico</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" id="edit_email" name="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_telefono">Teléfono</label>
                        <input type="tel" id="edit_telefono" name="edit_telefono" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_cedula">Cédula Profesional</label>
                        <input type="text" id="edit_cedula" name="edit_cedula" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_password">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                    <input type="password" id="edit_password" name="edit_password">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="btnCancelarEditar">Cancelar</button>
            <button class="btn btn-primary" id="btnConfirmarEditar">Guardar Cambios</button>
        </div>
    </div>
</div>
    
    <?php include 'footerAdmin.php'; ?>
    
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
            btnConfirmarAgregar.addEventListener('click', function() {
                const form = document.getElementById('formAgregarDoctor');
                if (form.checkValidity()) {
                    // Aquí iría la lógica para enviar los datos al servidor
                    modalAgregarDoctor.style.display = 'none';
                    document.getElementById('mensajeExito').textContent = 'El doctor ha sido agregado exitosamente.';
                    modalExito.style.display = 'flex';
                    form.reset();
                } else {
                    form.reportValidity();
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
    btn.addEventListener('click', function() {
        // Obtener datos del doctor desde la fila de la tabla
        const row = this.closest('tr');
        const id = row.cells[0].textContent;
        const nombre = row.cells[1].textContent;
        const departamento = row.cells[2].textContent;
        const email = row.cells[3].textContent;
        const telefono = row.cells[4].textContent;
        
        // Rellenar el formulario con los datos actuales
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;
        
        // Seleccionar el departamento correcto en el dropdown
        const departamentoSelect = document.getElementById('edit_especialidad');
        for (let i = 0; i < departamentoSelect.options.length; i++) {
            if (departamentoSelect.options[i].text === departamento) {
                departamentoSelect.selectedIndex = i;
                break;
            }
        }
        
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_telefono').value = telefono;
        
        // Para la cédula profesional, podríamos tener un valor por defecto o dejarlo vacío
        // En un sistema real, estos datos vendrían de la base de datos
        document.getElementById('edit_cedula').value = "PROF" + id + "XYZ";
        
        // Mostrar el modal
        modalEditarDoctor.style.display = 'flex';
    });
});

// Cancelar editar doctor
btnCancelarEditar.addEventListener('click', function() {
    modalEditarDoctor.style.display = 'none';
    document.getElementById('formEditarDoctor').reset();
});

// Confirmar editar doctor
btnConfirmarEditar.addEventListener('click', function() {
    const form = document.getElementById('formEditarDoctor');
    if (form.checkValidity()) {
        // Aquí iría la lógica para enviar los datos actualizados al servidor
        modalEditarDoctor.style.display = 'none';
        document.getElementById('mensajeExito').textContent = 'El doctor ha sido actualizado exitosamente.';
        modalExito.style.display = 'flex';
        
        // En un sistema real, aquí actualizaríamos la fila en la tabla con los nuevos datos
        // Por ahora, solo simulamos que se ha actualizado
        const doctorId = document.getElementById('edit_id').value;
        const doctorName = document.getElementById('edit_nombre').value;
        
        // Opcional: actualizar la fila en la tabla sin recargar la página
        const rows = document.querySelectorAll('.doctors-table tbody tr');
        rows.forEach(row => {
            if (row.cells[0].textContent === doctorId) {
                row.cells[1].textContent = doctorName;
                row.cells[2].textContent = document.getElementById('edit_especialidad').options[document.getElementById('edit_especialidad').selectedIndex].text;
                row.cells[3].textContent = document.getElementById('edit_email').value;
                row.cells[4].textContent = document.getElementById('edit_telefono').value;
            }
        });
        
        form.reset();
    } else {
        form.reportValidity();
    }
});
        });
    </script>
</body>
</html>
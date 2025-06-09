<?php
require_once __DIR__ . '/../../controllers/AdminController.php';

$departamentos = obtenerDepartamentosConDoctores();

?>
<link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css" />
<main class="main">
    <?php include_once __DIR__.'/../layouts/header.php'; ?>
    <div class="dashboard-container">
        <div class="dashboard-content">
            <section class="content-header">
                <div class="header-title">
                    <h1>Administrar Departamentos</h1>
                    <p>Gestiona los diferentes departamentos disponibles en la clínica.</p>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="abrirModalAgregar()">Agregar Departamento</button>
                </div>
            </section>

            <section class="doctors-list-section">
                <table class="doctors-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctores</th>
                            <th>Acciones</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($departamentos as $dep): ?>
                            <tr>
                                <td><?= $dep['id'] ?></td>
                                <td><?= htmlspecialchars($dep['name']) ?></td>
                                <td><?= $dep['total_doctores'] ?? 0 ?> doctores</td>
                                <td>
                                    <button class="btn btn-edit" onclick="abrirModalEditar(<?= $dep['id'] ?>, '<?= htmlspecialchars($dep['name']) ?>')">Editar</button>
                                    <form action="<?= BASE_URL ?>/departamento/eliminar" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?= $dep['id'] ?>">
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar este departamento?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>


                </table>
            </section>
        </div>
    </div>
</main>

<!-- Modal Agregar -->
<div id="modalAgregar" class="modal" style="display: none;">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2>Agregar Departamento</h2>
            <button class="close-modal" onclick="cerrarModal('modalAgregar')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?= BASE_URL ?>/departamento/crear" method="POST">
                <div class="form-group">
                    <label for="nuevo_departamento">Nombre del Departamento</label>
                    <input type="text" name="nombre" id="nuevo_departamento" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="cerrarModal('modalAgregar')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div id="modalEditar" class="modal" style="display: none;">
    <div class="modal-content modal-sm">
        <div class="modal-header">
            <h2>Editar Departamento</h2>
            <button class="close-modal" onclick="cerrarModal('modalEditar')">&times;</button>
        </div>
        <div class="modal-body">
            <form action="<?= BASE_URL ?>/departamento/editar" method="POST">
                <input type="hidden" name="id" id="editar_id">
                <div class="form-group">
                    <label for="editar_nombre">Nombre del Departamento</label>
                    <input type="text" name="nombre" id="editar_nombre" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="cerrarModal('modalEditar')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalAgregar() {
    document.getElementById('modalAgregar').style.display = 'flex';
}

function abrirModalEditar(id, nombre) {
    document.getElementById('editar_id').value = id;
    document.getElementById('editar_nombre').value = nombre;
    document.getElementById('modalEditar').style.display = 'flex';
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

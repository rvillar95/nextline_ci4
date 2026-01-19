<?= $this->extend('layout/dashboard') ?>

<?= $this->section('empresa/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Gestión de Empresas
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/empresa/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Empresa
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="estado_filter" class="form-label">Estado</label>
                            <select id="estado_filter" class="form-select">
                                <option value="">Todos</option>
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="paquete_filter" class="form-label">Paquete</label>
                            <select id="paquete_filter" class="form-select">
                                <option value="">Todos</option>
                                <?php
                                $db = \Config\Database::connect();
                                $paquetes = $db->table('paquetes')
                                    ->where('activo', 'A')
                                    ->orderBy('nombre', 'ASC')
                                    ->get()
                                    ->getResult();
                                foreach ($paquetes as $paquete) {
                                    echo '<option value="' . $paquete->id . '">' . esc($paquete->nombre) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="busqueda_filter" class="form-label">Búsqueda</label>
                            <input type="text" id="busqueda_filter" class="form-control" placeholder="Nombre, email, RUT...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="aplicar_filtros" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <table id="tablaEmpresas" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Nombre Comercial</th>
                                <th>RUT</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Paquete</th>
                                <th>Usuarios</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTable carga aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas desactivar esta empresa?</p>
                <p class="text-muted small">La empresa será desactivada (no eliminada) y no podrá ser utilizada hasta que se reactive.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacionEmpresa()">Desactivar</button>
            </div>
        </div>
    </div>
</div>

<style>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#tablaEmpresas').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/empresa/getEmpresas') ?>",
            "type": "GET",
            "data": function(d) {
                d.estado = $('#estado_filter').val();
                d.paquete_id = $('#paquete_filter').val();
                d.busqueda = $('#busqueda_filter').val();
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7 },
            { "data": 8, "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25
    });

    $('#aplicar_filtros').click(function() {
        table.ajax.reload();
    });

    $('#busqueda_filter').on('keyup', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 500);
    });

    window.editarEmpresa = function(id) {
        window.location.href = '<?= base_url('dashboard/empresa/editar') ?>/' + id;
    };

    window.verDetalleEmpresa = function(id) {
        window.location.href = '<?= base_url('dashboard/empresa/detalle') ?>/' + id;
    };

    window.eliminarEmpresa = function(id) {
        window.empresaIdAEliminar = id;
        $('#modalConfirmarEliminacion').modal('show');
    };

    window.confirmarEliminacionEmpresa = function() {
        if (!window.empresaIdAEliminar) return;

        $.ajax({
            url: '<?= base_url('dashboard/empresa/eliminar') ?>/' + window.empresaIdAEliminar,
            type: 'POST',
            data: {
                <?= csrf_field() ?>
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Empresa desactivada con éxito');
                    $('#modalConfirmarEliminacion').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error(response.error || 'Error al desactivar la empresa');
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.error || 'Error al desactivar la empresa';
                toastr.error(error);
            }
        });
    };

    window.activarEmpresa = function(id) {
        $.ajax({
            url: '<?= base_url('dashboard/empresa/activar') ?>/' + id,
            type: 'POST',
            data: {
                <?= csrf_field() ?>
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Empresa activada con éxito');
                    table.ajax.reload();
                } else {
                    toastr.error(response.error || 'Error al activar la empresa');
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.error || 'Error al activar la empresa';
                toastr.error(error);
            }
        });
    };
});
</script>

<?= $this->endSection() ?>

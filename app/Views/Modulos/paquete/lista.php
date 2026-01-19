<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paquete/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cubes"></i> Gestión de Paquetes
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/paquete/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Paquete
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
                            <label for="activo_filter" class="form-label">Estado</label>
                            <select id="activo_filter" class="form-select">
                                <option value="">Todos</option>
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label for="busqueda_filter" class="form-label">Búsqueda</label>
                            <input type="text" id="busqueda_filter" class="form-control" placeholder="Nombre, slug, descripción...">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="aplicar_filtros" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <table id="tablaPaquetes" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Slug</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Módulos</th>
                                <th>Empresas</th>
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
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">Confirmar Desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas desactivar este paquete?</p>
                <p class="text-muted small">El paquete será desactivado (no eliminado) y no podrá ser asignado a nuevas empresas hasta que se reactive.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacionPaquete()">Desactivar</button>
            </div>
        </div>
    </div>
</div>

<script>
console.log('Script de paquetes cargado');

// Usar jQuery directamente ya que está cargado en el header
jQuery(document).ready(function($) {
    console.log('jQuery ready, verificando DataTables...');
    
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables no está disponible');
        alert('Error: DataTables no está cargado. Recarga la página.');
        return;
    }
    
    console.log('DataTables disponible, inicializando tabla...');
    
    var table = $('#tablaPaquetes').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/paquete/getPaquetes') ?>",
            "type": "GET",
            "data": function(d) {
                d.activo = $('#activo_filter').val() || '';
                d.busqueda = $('#busqueda_filter').val() || '';
                console.log('Enviando datos:', { activo: d.activo, busqueda: d.busqueda });
            },
            "error": function(xhr, error, thrown) {
                console.error('Error AJAX:', error);
                console.error('Thrown:', thrown);
                console.error('Response:', xhr.responseText);
                alert('Error al cargar datos. Revisa la consola (F12) para más detalles.');
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
            { "data": 7, "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25
    });

    console.log('DataTable inicializado:', table);

    // Función para recargar con filtros
    function recargarConFiltros() {
        console.log('Recargando tabla...');
        table.ajax.reload(null, false);
    }

    // Event listeners para filtros
    $('#aplicar_filtros').on('click', function(e) {
        e.preventDefault();
        console.log('Botón filtrar clickeado');
        recargarConFiltros();
    });

    $('#busqueda_filter').on('keyup', function() {
        clearTimeout(this.searchTimeout);
        var self = this;
        this.searchTimeout = setTimeout(function() {
            console.log('Búsqueda cambiada');
            recargarConFiltros();
        }, 500);
    });

    $('#activo_filter').on('change', function() {
        console.log('Estado cambiado');
        recargarConFiltros();
    });

        // Funciones globales para los botones
        window.editarPaquete = function(id) {
            window.location.href = '<?= base_url('dashboard/paquete/editar') ?>/' + id;
        };

        window.verDetallePaquete = function(id) {
            window.location.href = '<?= base_url('dashboard/paquete/detalle') ?>/' + id;
        };

        window.gestionarModulos = function(id) {
            window.location.href = '<?= base_url('dashboard/paquete/gestionar-modulos') ?>/' + id;
        };

        window.eliminarPaquete = function(id) {
            window.paqueteIdAEliminar = id;
            jQuery('#modalConfirmarEliminacion').modal('show');
        };

        window.confirmarEliminacionPaquete = function() {
            if (!window.paqueteIdAEliminar) return;

            jQuery.ajax({
                url: '<?= base_url('dashboard/paquete/eliminar') ?>/' + window.paqueteIdAEliminar,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Paquete desactivado con éxito');
                        }
                        jQuery('#modalConfirmarEliminacion').modal('hide');
                        table.ajax.reload();
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.error || 'Error al desactivar el paquete');
                        }
                    }
                },
                error: function(xhr) {
                    var error = xhr.responseJSON?.error || 'Error al desactivar el paquete';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(error);
                    } else {
                        alert(error);
                    }
                }
            });
        };

        window.activarPaquete = function(id) {
            jQuery.ajax({
                url: '<?= base_url('dashboard/paquete/activar') ?>/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                },
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || 'Paquete activado con éxito');
                        }
                        table.ajax.reload();
                    } else {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(response.error || 'Error al activar el paquete');
                        }
                    }
                },
                error: function(xhr) {
                    var error = xhr.responseJSON?.error || 'Error al activar el paquete';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(error);
                    } else {
                        alert(error);
                    }
                }
            });
        };
});
</script>

<?= $this->endSection() ?>

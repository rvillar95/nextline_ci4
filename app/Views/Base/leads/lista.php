<?php $this->extend('layout/dashboard') ?>

<?= $this->section("leads/lista") ?>
<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf-token">
<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 col-12">
        <div class="widget-content widget-content-area br-8">
            <!-- Filtros -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="filtro-estado" class="form-label">Filtrar por Estado:</label>
                    <select id="filtro-estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1">Nuevo</option>
                        <option value="2">En gestión</option>
                        <option value="3">Contactado</option>
                        <option value="4">Cerrado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-servicio" class="form-label">Filtrar por Servicio:</label>
                    <select id="filtro-servicio" class="form-select">
                        <option value="">Todos los servicios</option>
                        <!-- Se llenará dinámicamente -->
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" id="aplicar-filtros">Aplicar Filtros</button>
                    <button type="button" class="btn btn-secondary" id="limpiar-filtros">Limpiar</button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="leads-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Mensaje</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este lead?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#leads-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url('dashboard/leads/getLeads') ?>",
            "type": "GET",
            "data": function(d) {
                d.estado_id = $('#filtro-estado').val();
                d.servicio_id = $('#filtro-servicio').val();
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
        "order": [[6, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    // Cargar servicios para el filtro
    $.get('<?= base_url('dashboard/leads/getServicios') ?>', function(data) {
        console.log('Servicios recibidos:', data);
        var select = $('#filtro-servicio');
        if (Array.isArray(data)) {
            $.each(data, function(index, servicio) {
                select.append('<option value="' + servicio.id + '">' + servicio.nombre + '</option>');
            });
        } else {
            console.error('Error: Los datos no son un array:', data);
        }
    }).fail(function(xhr, status, error) {
        console.error('Error al cargar servicios:', status, error);
        console.error('Respuesta del servidor:', xhr.responseText);
    });

    // Aplicar filtros
    $('#aplicar-filtros').click(function() {
        table.ajax.reload();
    });

    // Limpiar filtros
    $('#limpiar-filtros').click(function() {
        $('#filtro-estado').val('');
        $('#filtro-servicio').val('');
        table.ajax.reload();
    });

    // Cambiar estado
    $(document).on('click', '.btn-cambiar-estado', function() {
        var id = $(this).data('id');
        var estado = $(this).data('estado');
        var csrfToken = $('#csrf-token').val();
        
        $.post('<?= base_url('dashboard/leads/cambiarEstado') ?>', {
            id: id,
            estado_id: estado,
            <?= csrf_token() ?>: csrfToken
        }, function(response) {
            if (response.ok) {
                // Actualizar el token CSRF para futuras peticiones
                if (response.csrf_token) {
                    $('#csrf-token').val(response.csrf_token);
                }
                table.ajax.reload();
                toastr.success('Estado actualizado correctamente');
            } else {
                toastr.error('Error al actualizar el estado');
            }
        }).fail(function(xhr) {
            if (xhr.status === 403) {
                toastr.error('Sesión expirada. Por favor, recarga la página.');
            } else {
                toastr.error('Error de conexión');
            }
        });
    });

    // Eliminar
    var leadIdToDelete = null;
    $(document).on('click', '.btn-eliminar', function() {
        leadIdToDelete = $(this).data('id');
        $('#modalEliminar').modal('show');
    });

    $('#confirmarEliminar').click(function() {
        if (leadIdToDelete) {
            var csrfToken = $('#csrf-token').val();
            $.post('<?= base_url('dashboard/leads/eliminar') ?>', {
                id: leadIdToDelete,
                <?= csrf_token() ?>: csrfToken
            }, function(response) {
                $('#modalEliminar').modal('hide');
                table.ajax.reload();
                toastr.success('Lead eliminado correctamente');
            }).fail(function(xhr) {
                if (xhr.status === 403) {
                    toastr.error('Sesión expirada. Por favor, recarga la página.');
                } else {
                    toastr.error('Error al eliminar el lead');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>

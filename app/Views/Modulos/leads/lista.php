<?php $this->extend('layout/dashboard') ?>

<?= $this->section("leads/lista") ?>
<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf-token">


<div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-book"></i> Gestión de Contactos
                    </h3>
                    <div class="card-tools">
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
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
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
    </div>
</div>


<!-- Modal para ver detalle -->
<div class="modal fade" id="modalDetalle" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detalle del Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detalleContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este lead? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarEliminar">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
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

    // Ver detalle
    $(document).on('click', '.btn-ver-detalle', function() {
        var id = $(this).data('id');
        $('#modalDetalle').modal('show');
        
        // Mostrar spinner
        $('#detalleContent').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-info" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `);
        
        // Cargar datos
        $.get('<?= base_url('dashboard/leads/getDetalle') ?>', { id: id }, function(response) {
            if (response.ok && response.lead) {
                var lead = response.lead;
                var estadoBadge = '';
                
                switch(parseInt(lead.estado_id)) {
                    case 1:
                        estadoBadge = '<span class="badge bg-info">Nuevo</span>';
                        break;
                    case 2:
                        estadoBadge = '<span class="badge bg-warning">En gestión</span>';
                        break;
                    case 3:
                        estadoBadge = '<span class="badge bg-primary">Contactado</span>';
                        break;
                    case 4:
                        estadoBadge = '<span class="badge bg-success">Cerrado</span>';
                        break;
                    default:
                        estadoBadge = '<span class="badge bg-secondary">N/A</span>';
                }
                
                $('#detalleContent').html(`
                    <div class="row g-4">
                        <!-- Información Personal -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary mb-3">
                                        <i class="fas fa-user me-2"></i>Información Personal
                                    </h6>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Nombre Completo</label>
                                        <div class="fw-bold">${lead.nombre || 'N/A'}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Correo Electrónico</label>
                                        <div class="fw-bold">
                                            <a href="mailto:${lead.correo}" class="text-decoration-none">
                                                <i class="fas fa-envelope me-2"></i>${lead.correo || 'N/A'}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="text-muted small d-block mb-1">Teléfono</label>
                                        <div class="fw-bold">
                                            ${lead.telefono ? `<a href="tel:${lead.telefono}" class="text-decoration-none"><i class="fas fa-phone me-2"></i>${lead.telefono}</a>` : 'N/A'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estado y Servicio -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-success mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Estado y Servicio
                                    </h6>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Estado Actual</label>
                                        <div>${estadoBadge}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Plan de interés</label>
                                        <div class="fw-bold">${lead.plan_nombre || (lead.plan_interes === 'otro' ? 'Otro / Consulta general' : (lead.plan_interes || lead.servicio_nombre || 'Sin especificar'))}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="text-muted small d-block mb-1">Fecha de Registro</label>
                                        <div class="fw-bold">
                                            <i class="fas fa-calendar-alt me-2"></i>${lead.fcreacion_formatted || 'N/A'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mensaje Completo -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-warning mb-3">
                                        <i class="fas fa-comment-alt me-2"></i>Mensaje del Cliente
                                    </h6>
                                    <div class="bg-light p-3 rounded" style="white-space: pre-wrap; line-height: 1.6;">
                                        ${lead.mensaje || 'Sin mensaje'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información UTM (si existe) -->
                        ${lead.utm_source || lead.utm_medium || lead.utm_campaign ? `
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title text-secondary mb-3">
                                        <i class="fas fa-chart-line me-2"></i>Información de Campaña
                                    </h6>
                                    <div class="row">
                                        ${lead.utm_source ? `
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small d-block mb-1">Fuente (Source)</label>
                                            <div class="fw-bold">${lead.utm_source}</div>
                                        </div>
                                        ` : ''}
                                        ${lead.utm_medium ? `
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small d-block mb-1">Medio (Medium)</label>
                                            <div class="fw-bold">${lead.utm_medium}</div>
                                        </div>
                                        ` : ''}
                                        ${lead.utm_campaign ? `
                                        <div class="col-md-4 mb-2">
                                            <label class="text-muted small d-block mb-1">Campaña</label>
                                            <div class="fw-bold">${lead.utm_campaign}</div>
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                `);
            } else {
                $('#detalleContent').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Error al cargar el detalle del lead
                    </div>
                `);
            }
        }).fail(function() {
            $('#detalleContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Error de conexión al cargar el detalle
                </div>
            `);
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

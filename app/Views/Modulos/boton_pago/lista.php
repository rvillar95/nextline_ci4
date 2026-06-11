<?= $this->extend('layout/dashboard') ?>

<?= $this->section('boton_pago/lista') ?>

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
    }
    
    .badge-estado {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-pendiente { background-color: #ffc107; color: #000; }
    .badge-procesando { background-color: #17a2b8; color: #fff; }
    .badge-completado { background-color: #28a745; color: #fff; }
    .badge-fallido { background-color: #dc3545; color: #fff; }
    .badge-reembolsado { background-color: #6c757d; color: #fff; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-tags me-2"></i> Tarifas de consulta</h2>
                        <p style="color: white;">Define los montos que puedes cobrar al agendar citas (vía Mercado Pago)</p>
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/crear') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nueva tarifa
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="section-card">
                <?php if (empty($plantillas)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aún no has definido tarifas. Créalas aquí y elígelas al agendar una cita.</p>
                        <a href="<?= base_url('dashboard/boton-pago/crear') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Crear primera tarifa
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="tablaBotonesPago" class="table table-bordered table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Moneda</th>
                                    <th>Estado</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plantillas as $plantilla): ?>
                                    <tr>
                                        <td><?= $plantilla['id'] ?? $plantilla->id ?? '' ?></td>
                                        <td><?= esc($plantilla['titulo'] ?? $plantilla->titulo ?? 'Sin título') ?></td>
                                        <td><?= esc(substr($plantilla['descripcion'] ?? $plantilla->descripcion ?? '', 0, 50)) ?><?= strlen($plantilla['descripcion'] ?? $plantilla->descripcion ?? '') > 50 ? '...' : '' ?></td>
                                        <td>$<?= number_format($plantilla['monto'] ?? $plantilla->monto ?? 0, 0, ',', '.') ?></td>
                                        <td><?= esc($plantilla['moneda'] ?? $plantilla->moneda ?? 'CLP') ?></td>
                                        <td>
                                            <?php
                                            $activo = $plantilla['activo'] ?? $plantilla->activo ?? 'A';
                                            $badgeClass = ($activo === 'A') ? 'badge-completado' : 'badge-pendiente';
                                            $estadoTexto = ($activo === 'A') ? 'Activa' : 'Inactiva';
                                            ?>
                                            <span class="badge badge-estado <?= $badgeClass ?>">
                                                <?= $estadoTexto ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($plantilla['fcreacion'] ?? $plantilla->fcreacion ?? 'now')) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('dashboard/boton-pago/crear?plantilla_id=' . (int)($plantilla['id'] ?? $plantilla->id ?? 0)) ?>" 
                                                   class="btn btn-sm btn-info" title="Editar tarifa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('dashboard/boton-pago/crear') ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Duplicar como nueva tarifa">
                                                    <i class="fas fa-copy"></i>
                                                </a>
                                                <?php if ($activo === 'A' && !empty($puede_eliminar)): ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger btn-eliminar-tarifa"
                                                        title="Eliminar tarifa"
                                                        data-id="<?= (int)($plantilla['id'] ?? $plantilla->id ?? 0) ?>"
                                                        data-titulo="<?= esc($plantilla['titulo'] ?? $plantilla->titulo ?? '', 'attr') ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarEliminacionTarifa" tabindex="-1" aria-labelledby="modalConfirmarEliminacionTarifaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionTarifaLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar la tarifa <strong id="tarifaTituloEliminar"></strong>?</p>
                <p class="text-muted small mb-0">
                    La tarifa quedará inactiva y dejará de aparecer al agendar citas. No se borrará del historial.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacionTarifa()">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
window.tarifaIdAEliminar = null;

window.eliminarTarifa = function(id, titulo) {
    window.tarifaIdAEliminar = id;
    $('#tarifaTituloEliminar').text(titulo || 'seleccionada');
    var modalEl = document.getElementById('modalConfirmarEliminacionTarifa');
    if (modalEl && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else {
        $('#modalConfirmarEliminacionTarifa').modal('show');
    }
};

window.confirmarEliminacionTarifa = function() {
    if (!window.tarifaIdAEliminar) {
        return;
    }

    $.ajax({
        url: '<?= base_url('dashboard/boton-pago/eliminar') ?>/' + window.tarifaIdAEliminar,
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
                    toastr.success(response.message || 'Tarifa eliminada con éxito');
                }
                var modalEl = document.getElementById('modalConfirmarEliminacionTarifa');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var inst = bootstrap.Modal.getInstance(modalEl);
                    if (inst) inst.hide();
                } else {
                    $('#modalConfirmarEliminacionTarifa').modal('hide');
                }
                window.location.reload();
            } else if (typeof toastr !== 'undefined') {
                toastr.error(response.error || 'Error al eliminar la tarifa');
            } else {
                alert(response.error || 'Error al eliminar la tarifa');
            }
        },
        error: function(xhr) {
            var error = xhr.responseJSON?.error || 'Error al eliminar la tarifa';
            if (typeof toastr !== 'undefined') {
                toastr.error(error);
            } else {
                alert(error);
            }
        }
    });
};

$(document).ready(function() {
    $(document).on('click', '.btn-eliminar-tarifa', function() {
        var id = parseInt($(this).data('id'), 10);
        var titulo = $(this).attr('data-titulo') || '';
        if (id > 0) {
            window.eliminarTarifa(id, titulo);
        }
    });

    // Solo inicializar DataTables si la tabla existe y tiene datos
    if ($('#tablaBotonesPago').length && $('#tablaBotonesPago tbody tr').length > 0) {
        var table = $('#tablaBotonesPago').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "responsive": true,
            "pageLength": 25,
            "order": [[6, "desc"]], // Ordenar por fecha de creación descendente
            "columnDefs": [
                { "orderable": false, "targets": 7 } // Columna de acciones no ordenable
            ],
            "autoWidth": false
        });
    }
});
</script>

<?= $this->endSection() ?>

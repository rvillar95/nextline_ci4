<?= $this->extend('layout/dashboard') ?>

<?= $this->section('boton_pago/lista') ?>

<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
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
                        <h2 style="color: white;"><i class="fas fa-credit-card me-2"></i> Botones de Pago</h2>
                        <p style="color: white;">Gestiona los botones de pago creados con Mercado Pago</p>
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/crear') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Crear Botón de Pago
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
                        <p class="text-muted">No hay plantillas de botones de pago creadas aún</p>
                        <a href="<?= base_url('dashboard/boton-pago/crear') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Crear Primera Plantilla
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
                                                <a href="<?= base_url('dashboard/boton-pago/editar/' . ($plantilla['id'] ?? $plantilla->id ?? '')) ?>" 
                                                   class="btn btn-sm btn-info" title="Editar plantilla">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('dashboard/boton-pago/crear') ?>" 
                                                   class="btn btn-sm btn-primary" title="Crear nueva plantilla">
                                                    <i class="fas fa-plus"></i>
                                                </a>
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

<script>
$(document).ready(function() {
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

<!-- Vista: Listado de Materiales - Detalle -->
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('listado_material/detalle') ?>
<div class="layout-px-spacing">
    <div class="middle-content container-xxl p-0">
        
        <!-- Breadcrumb -->
        <div class="page-meta">
            <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/inicio') ?>">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard/listado-material/lista') ?>">Listado de Materiales</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                </ol>
            </nav>
        </div>

        <!-- Mensajes Flash -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Información General -->
            <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                <?= esc($listado->titulo) ?>
                            </h5>
                            <span class="badge" style="background: rgba(255,255,255,0.3);">
                                <?= esc($listado->numero_listado) ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Cliente:</label>
                                <p class="mb-0 fw-bold"><?= esc($listado->cliente_nombre ?? 'No especificado') ?></p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Proyecto:</label>
                                <p class="mb-0 fw-bold"><?= esc($listado->proyecto_nombre ?? 'No especificado') ?></p>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Fecha:</label>
                                <p class="mb-0 fw-bold"><?= date('d/m/Y', strtotime($listado->fecha_listado ?? $listado->created_at)) ?></p>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Estado:</label>
                                <p class="mb-0">
                                    <?php
                                    $estadoBadge = match ($listado->estado ?? 'borrador') {
                                        'borrador' => '<span class="badge badge-secondary">Borrador</span>',
                                        'finalizado' => '<span class="badge badge-success">Finalizado</span>',
                                        'enviado' => '<span class="badge badge-info">Enviado</span>',
                                        'archivado' => '<span class="badge badge-warning">Archivado</span>',
                                        default => '<span class="badge badge-secondary">N/A</span>',
                                    };
                                    echo $estadoBadge;
                                    ?>
                                </p>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="text-muted small">Total Materiales:</label>
                                <p class="mb-0 fw-bold"><?= count($items) ?> items</p>
                            </div>

                            <?php if (!empty($listado->observaciones)): ?>
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">Observaciones:</label>
                                    <div class="alert alert-warning mb-0">
                                        <?= nl2br(esc($listado->observaciones)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Acciones
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('dashboard/listado-material/editar/' . $listado->id) ?>" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar Listado
                            </a>

                            <a href="<?= base_url('dashboard/listado-material/generarPDF/' . $listado->id) ?>" class="btn btn-danger">
                                <i class="fas fa-file-pdf me-2"></i>Generar PDF
                            </a>

                            <a href="<?= base_url('dashboard/listado-material/lista') ?>" class="btn btn-light">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Materiales -->
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-header" style="background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%); color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            Materiales (<?= count($items) ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($items)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No hay materiales registrados en este listado.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="45%">Material</th>
                                            <th width="20%">Unidad de Medida</th>
                                            <th width="15%" class="text-end">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $contador = 1; ?>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td class="text-center"><?= $contador++ ?></td>
                                                <td>
                                                    <div class="fw-bold"><?= esc($item->nombre_material) ?></div>
                                                    <?php if (!empty($item->descripcion)): ?>
                                                        <small class="text-muted fst-italic"><?= esc($item->descripcion) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($item->unidad_medida ?? '-') ?></td>
                                                <td class="text-end">
                                                    <?= $item->cantidad !== null ? number_format($item->cantidad, 2, ',', '.') : '-' ?>
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

    </div>
</div>

<style>
    .d-grid.gap-2 {
        display: grid;
        gap: 0.5rem;
    }
</style>

<?= $this->endSection() ?>


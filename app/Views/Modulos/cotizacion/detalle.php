<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> Cotización: <?= esc($cotizacion->numero_cotizacion ?? 'Sin número') ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cotizacion/generarPDF/' . ($cotizacion->id ?? 0)) ?>" class="btn btn-success">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </a>
                        <a href="<?= base_url('dashboard/cotizacion/editar/' . ($cotizacion->id ?? 0)) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="<?= base_url('dashboard/cotizacion/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                    <?php if (session()->getFlashdata('success') !== null) : ?>
                                <div class="alert alert-success my-3" role="alert">
                                    <?= session()->getFlashdata('success'); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('errors') !== null) : ?>
                                <p style="color:red; font-weight:bold;">
                                    <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                        <?= session()->getFlashdata('errors'); ?>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        <!-- Información Principal -->
                        <div class="col-md-8">
                            <!-- Información de la Cotización -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i> Información de la Cotización
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Número:</strong><br>
                                            <?= esc($cotizacion->numero_cotizacion ?? 'Sin número') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Título:</strong><br>
                                            <?= esc($cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? 'Sin título') ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Estado:</strong><br>
                                            <?php
                                            $estadoBadge = match ($cotizacion->estado ?? 'borrador') {
                                                'borrador' => '<span class="badge badge-secondary">Borrador</span>',
                                                'enviada' => '<span class="badge badge-info">Enviada</span>',
                                                'revisada' => '<span class="badge badge-warning">Revisada</span>',
                                                'aprobada' => '<span class="badge badge-success">Aprobada</span>',
                                                'rechazada' => '<span class="badge badge-danger">Rechazada</span>',
                                                'expirada' => '<span class="badge badge-warning">Expirada</span>',
                                                default => '<span class="badge badge-secondary">N/A</span>',
                                            };
                                            echo $estadoBadge;
                                            ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Prioridad:</strong><br>
                                            <?php
                                            $prioridadBadge = match ($cotizacion->prioridad ?? 'media') {
                                                'baja' => '<span class="badge badge-success">Baja</span>',
                                                'media' => '<span class="badge badge-warning">Media</span>',
                                                'alta' => '<span class="badge badge-danger">Alta</span>',
                                                default => '<span class="badge badge-secondary">N/A</span>',
                                            };
                                            echo $prioridadBadge;
                                            ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Fecha:</strong><br>
                                            <?= date('d/m/Y', strtotime($cotizacion->fecha_cotizacion ?? date('Y-m-d'))) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($cotizacion->fecha_validez)): ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Válida hasta:</strong><br>
                                            <?= date('d/m/Y', strtotime($cotizacion->fecha_validez ?? date('Y-m-d'))) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Días restantes:</strong><br>
                                            <?php
                                            $diasRestantes = (strtotime($cotizacion->fecha_validez ?? date('Y-m-d')) - time()) / (60 * 60 * 24);
                                            if ($diasRestantes > 0) {
                                                echo '<span class="badge badge-success">' . ceil($diasRestantes) . ' días</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Expirada</span>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Información del Cliente -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-user"></i> Información del Cliente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Cliente:</strong><br>
                                            <?= esc($cotizacion->cliente_nombre ?? 'Sin nombre') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Tipo:</strong><br>
                                            <?php
                                            $tipoBadge = match ($cotizacion->tipo_cliente ?? 'particular') {
                                                'particular' => '<span class="badge badge-info">Particular</span>',
                                                'empresa' => '<span class="badge badge-success">Empresa</span>',
                                                'organizacion' => '<span class="badge badge-warning">Organización</span>',
                                                default => '<span class="badge badge-secondary">N/A</span>',
                                            };
                                            echo $tipoBadge;
                                            ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($cotizacion->contacto_nombre)): ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Contacto:</strong><br>
                                            <?= esc($cotizacion->contacto_nombre ?? '') ?>
                                        </div>
                                        <?php if (!empty($cotizacion->telefono)): ?>
                                        <div class="col-md-6">
                                            <strong>Teléfono:</strong><br>
                                            <a href="tel:<?= esc($cotizacion->telefono ?? '') ?>"><?= esc($cotizacion->telefono ?? '') ?></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($cotizacion->email)): ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:<?= esc($cotizacion->email ?? '') ?>"><?= esc($cotizacion->email ?? '') ?></a>
                                        </div>
                                        <?php if (!empty($cotizacion->direccion)): ?>
                                        <div class="col-md-6">
                                            <strong>Dirección:</strong><br>
                                            <?= esc($cotizacion->direccion ?? '') ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Items de la Cotización -->
                            <?php if (!empty($cotizacion->items)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-list"></i> Items de la Cotización
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Descripción</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th>Precio Unit.</th>
                                                    <th>Subtotal</th>
                                                    <th>Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cotizacion->items as $index => $item): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= esc($item->descripcion ?? '') ?></td>
                                                    <td><?= number_format($item->cantidad ?? 0, 2, ',', '.') ?></td>
                                                    <td><?= esc($item->unidad ?? '') ?></td>
                                                    <td>$<?= number_format($item->precio_unitario ?? 0, 0, ',', '.') ?></td>
                                                    <td>$<?= number_format($item->subtotal ?? 0, 0, ',', '.') ?></td>
                                                    <td>
                                                        <span class="badge badge-info"><?= ucfirst($item->categoria ?? '') ?></span>
                                                        <?php if (!empty($item->es_opcional)): ?>
                                                            <span class="badge badge-warning">Opcional</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Descripción del Proyecto -->
                            <?php if (!empty($cotizacion->proyecto_descripcion)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-clipboard-list"></i> Descripción del Proyecto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(esc($cotizacion->proyecto_descripcion ?? '')) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Condiciones de Pago -->
                            <?php if (!empty($cotizacion->condiciones_generales)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-credit-card"></i> Condiciones de Pago
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(esc($cotizacion->condiciones_generales ?? '')) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Observaciones -->
                            <?php if (!empty($cotizacion->observaciones_especiales)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-sticky-note"></i> Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(esc($cotizacion->observaciones_especiales ?? '')) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Panel Lateral -->
                        <div class="col-md-4">
                            <!-- Resumen de Totales -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-calculator"></i> Resumen de Totales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6"><strong>Subtotal:</strong></div>
                                        <div class="col-6 text-right">$<?= number_format($cotizacion->subtotal ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                    <?php if (($cotizacion->descuento_monto ?? 0) > 0): ?>
                                    <div class="row">
                                        <div class="col-6"><strong>Descuento:</strong></div>
                                        <div class="col-6 text-right">-$<?= number_format($cotizacion->descuento_monto ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-6"><strong>IVA (<?= $cotizacion->iva_porcentaje ?? 19 ?>%):</strong></div>
                                        <div class="col-6 text-right">$<?= number_format($cotizacion->iva_monto ?? 0, 0, ',', '.') ?></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6"><strong>TOTAL:</strong></div>
                                        <div class="col-6 text-right"><strong>$<?= number_format($cotizacion->total_general ?? 0, 0, ',', '.') ?></strong></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones Rápidas -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-bolt"></i> Acciones Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <a href="<?= base_url('dashboard/cotizacion/generarPDF/' . ($cotizacion->id ?? 0)) ?>" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-file-pdf"></i> Generar PDF
                                    </a>
                                    <a href="<?= base_url('dashboard/cotizacion/editar/' . ($cotizacion->id ?? 0)) ?>" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Editar Cotización
                                    </a>
                                    <?php if (($cotizacion->estado ?? 'borrador') === 'aprobada'): ?>
                                    <button class="btn btn-success btn-block mb-2" onclick="convertirCotizacion(<?= $cotizacion->id ?? 0 ?>)">
                                        <i class="fas fa-project-diagram"></i> Convertir en Proyecto
                                    </button>
                                    <?php endif; ?>
                                    <a href="<?= base_url('dashboard/cliente/detalle/' . ($cotizacion->cliente_id ?? 0)) ?>" class="btn btn-info btn-block mb-2">
                                        <i class="fas fa-user"></i> Ver Cliente
                                    </a>
                                    <button class="btn btn-danger btn-block" onclick="eliminarCotizacion(<?= $cotizacion->id ?? 0 ?>)">
                                        <i class="fas fa-trash"></i> Eliminar Cotización
                                    </button>
                                </div>
                            </div>

                            <!-- Archivos Adjuntos -->
                            <?php if (!empty($cotizacion->archivos)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-paperclip"></i> Archivos Adjuntos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($cotizacion->archivos as $archivo): ?>
                                    <div class="mb-2">
                                        <a href="<?= base_url($archivo->ruta_archivo) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> <?= esc($archivo->nombre_archivo) ?>
                                        </a>
                                        <?php if ($archivo->es_principal): ?>
                                            <span class="badge badge-warning">Principal</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('components/modals') ?>

<script src="<?= base_url('lib/js/modals.js') ?>"></script>
<script>
function eliminarCotizacion(id) {
    eliminarConConfirmacion(
        '<?= base_url('dashboard/cotizacion/eliminar') ?>/' + id,
        '¿Estás seguro de que deseas eliminar esta cotización?'
    );
}

function convertirCotizacion(id) {
    convertirCotizacionConConfirmacion(
        '<?= base_url('dashboard/cotizacion/convertir-proyecto') ?>/' + id,
        '¿Estás seguro de convertir esta cotización en proyecto? Se creará un nuevo proyecto basado en los datos de esta cotización.'
    );
}
</script>

<?= $this->endSection() ?>

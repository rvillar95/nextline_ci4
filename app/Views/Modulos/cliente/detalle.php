<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cliente/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Detalle del Cliente: <?= esc($cliente->nombre_razon_social) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cliente/editar/' . $cliente->id) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información Principal -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-info-circle"></i> Información General
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Tipo de Cliente:</strong><br>
                                            <?php
                                            $tipoBadge = match ($cliente->tipo_cliente) {
                                                'particular' => '<span class="badge badge-info">Particular</span>',
                                                'empresa' => '<span class="badge badge-success">Empresa</span>',
                                                'organizacion' => '<span class="badge badge-warning">Organización</span>',
                                                default => '<span class="badge badge-secondary">N/A</span>',
                                            };
                                            echo $tipoBadge;
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Estado:</strong><br>
                                            <span class="badge badge-success">Activo</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nombre / Razón Social:</strong><br>
                                            <?= esc($cliente->nombre_razon_social) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>RUT / DNI:</strong><br>
                                            <?= esc($cliente->rut_dni ?? 'No especificado') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Contacto -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-address-book"></i> Información de Contacto
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php if ($cliente->contacto_nombre): ?>
                                        <div class="col-md-6">
                                            <strong>Contacto:</strong><br>
                                            <?= esc($cliente->contacto_nombre) ?>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($cliente->contacto_cargo): ?>
                                        <div class="col-md-6">
                                            <strong>Cargo:</strong><br>
                                            <?= esc($cliente->contacto_cargo) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <?php if ($cliente->telefono): ?>
                                        <div class="col-md-6">
                                            <strong>Teléfono:</strong><br>
                                            <a href="tel:<?= esc($cliente->telefono) ?>"><?= esc($cliente->telefono) ?></a>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($cliente->email): ?>
                                        <div class="col-md-6">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:<?= esc($cliente->email) ?>"><?= esc($cliente->email) ?></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($cliente->sitio_web): ?>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Sitio Web:</strong><br>
                                            <a href="<?= esc($cliente->sitio_web) ?>" target="_blank"><?= esc($cliente->sitio_web) ?></a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Dirección -->
                            <?php if ($cliente->direccion || $cliente->comuna || $cliente->region): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-map-marker-alt"></i> Ubicación
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($cliente->direccion): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <strong>Dirección:</strong><br>
                                            <?= esc($cliente->direccion) ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($cliente->comuna || $cliente->region): ?>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <strong>Comuna:</strong><br>
                                            <?= esc($cliente->comuna ?? 'No especificada') ?>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Región:</strong><br>
                                            <?= esc($cliente->region ?? 'No especificada') ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Observaciones -->
                            <?php if ($cliente->observaciones): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-sticky-note"></i> Observaciones
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(esc($cliente->observaciones)) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Panel Lateral -->
                        <div class="col-md-4">
                            <!-- Estadísticas -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-bar"></i> Estadísticas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            <h3 class="text-primary"><?= count($cotizaciones) ?></h3>
                                            <p class="text-muted">Cotizaciones</p>
                                        </div>
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
                                    <a href="<?= base_url('dashboard/cotizacion/registro?cliente_id=' . $cliente->id) ?>" class="btn btn-success btn-block mb-2">
                                        <i class="fas fa-plus"></i> Nueva Cotización
                                    </a>
                                    <a href="<?= base_url('dashboard/cliente/editar/' . $cliente->id) ?>" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Editar Cliente
                                    </a>
                                    <button class="btn btn-danger btn-block" onclick="eliminarCliente(<?= $cliente->id ?>)">
                                        <i class="fas fa-trash"></i> Eliminar Cliente
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cotizaciones del Cliente -->
                    <?php if (!empty($cotizaciones)): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-file-invoice-dollar"></i> Cotizaciones del Cliente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Número</th>
                                                    <th>Título</th>
                                                    <th>Estado</th>
                                                    <th>Total</th>
                                                    <th>Fecha</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($cotizaciones as $cotizacion): ?>
                                                <tr>
                                                    <td><?= esc($cotizacion->numero_cotizacion) ?></td>
                                                    <td><?= esc($cotizacion->proyecto_nombre ?? $cotizacion->titulo ?? 'Sin título') ?></td>
                                                    <td>
                                                        <?php
                                                        $estadoBadge = match ($cotizacion->estado ?? 'borrador') {
                                                            'borrador' => '<span class="badge badge-secondary">Borrador</span>',
                                                            'enviada' => '<span class="badge badge-info">Enviada</span>',
                                                            'aceptada' => '<span class="badge badge-success">Aceptada</span>',
                                                            'rechazada' => '<span class="badge badge-danger">Rechazada</span>',
                                                            'expirada' => '<span class="badge badge-warning">Expirada</span>',
                                                            default => '<span class="badge badge-secondary">N/A</span>',
                                                        };
                                                        echo $estadoBadge;
                                                        ?>
                                                    </td>
                                                    <td>$<?= number_format($cotizacion->total_general, 0, ',', '.') ?></td>
                                                    <td><?= date('d/m/Y', strtotime($cotizacion->fecha_cotizacion)) ?></td>
                                                    <td>
                                                        <a href="<?= base_url('dashboard/cotizacion/detalle/' . $cotizacion->id) ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?= base_url('dashboard/cotizacion/editar/' . $cotizacion->id) ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('components/modals') ?>

<script src="<?= base_url('lib/js/modals.js') ?>"></script>
<script>
function eliminarCliente(id) {
    eliminarConConfirmacion(
        '<?= base_url('dashboard/cliente/eliminar') ?>/' + id,
        '¿Estás seguro de que deseas eliminar este cliente?'
    );
}
</script>

<?= $this->endSection() ?>


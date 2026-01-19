<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paquete/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cube"></i> Detalle de Paquete: <?= esc($paquete->nombre) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                        <a href="<?= base_url('dashboard/paquete/editar/' . $paquete->id) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="<?= base_url('dashboard/paquete/gestionar-modulos/' . $paquete->id) ?>" class="btn btn-warning">
                            <i class="fas fa-cogs"></i> Gestionar Módulos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nombre:</th>
                                            <td><?= esc($paquete->nombre) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Slug:</th>
                                            <td><code><?= esc($paquete->slug) ?></code></td>
                                        </tr>
                                        <?php if ($paquete->descripcion): ?>
                                        <tr>
                                            <th>Descripción:</th>
                                            <td><?= nl2br(esc($paquete->descripcion)) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                <?php if ($paquete->activo == 'A'): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Orden:</th>
                                            <td><?= $paquete->orden ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Precios -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Precios</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Precio Setup:</th>
                                            <td>
                                                <?php if ($paquete->precio_setup > 0): ?>
                                                    <strong>$<?= number_format($paquete->precio_setup, 0, ',', '.') ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">Gratis</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Precio Mensual:</th>
                                            <td>
                                                <?php if ($paquete->precio_mensual > 0): ?>
                                                    <strong>$<?= number_format($paquete->precio_mensual, 0, ',', '.') ?>/mes</strong>
                                                <?php else: ?>
                                                    <span class="text-muted">Gratis</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Módulos del Paquete -->
                    <?php if (!empty($modulos)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-cubes"></i> Módulos Incluidos (<?= count($modulos) ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($modulos as $modulo): ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="card border-primary">
                                                <div class="card-body p-2">
                                                    <strong><i class="fas fa-check-circle text-success"></i> <?= esc($modulo['nombre']) ?></strong>
                                                    <?php if ($modulo['descripcion']): ?>
                                                        <br><small class="text-muted"><?= esc($modulo['descripcion']) ?></small>
                                                    <?php endif; ?>
                                                    <br><small class="text-info"><i class="fas fa-route"></i> <?= esc($modulo['ruta']) ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Este paquete no tiene módulos asignados. 
                        <a href="<?= base_url('dashboard/paquete/gestionar-modulos/' . $paquete->id) ?>" class="alert-link">Asignar módulos ahora</a>
                    </div>
                    <?php endif; ?>

                    <!-- Empresas con este Paquete -->
                    <?php if (!empty($empresas)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-building"></i> Empresas con este Paquete (<?= count($empresas) ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Empresa</th>
                                                    <th>Email</th>
                                                    <th>Usuarios</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($empresas as $empresa): ?>
                                                <tr>
                                                    <td><?= esc($empresa['nombre']) ?></td>
                                                    <td><?= esc($empresa['email'] ?? '') ?></td>
                                                    <td><span class="badge bg-secondary"><?= $empresa['cantidad_usuarios'] ?></span></td>
                                                    <td>
                                                        <?php if ($empresa['estado'] == 'A'): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('dashboard/empresa/detalle/' . $empresa['id']) ?>" class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i> Ver
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
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay empresas asignadas a este paquete aún.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

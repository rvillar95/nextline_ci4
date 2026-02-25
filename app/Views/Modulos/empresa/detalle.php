<?= $this->extend('layout/dashboard') ?>

<?= $this->section('empresa/detalle') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Detalle de Empresa: <?= esc($empresa->nombre) ?>
                    </h3>
                    <div class="card-tools">
                        <?php if ($es_super_admin): ?>
                            <a href="<?= base_url('dashboard/empresa/lista') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver a Lista
                            </a>
                            <a href="<?= base_url('dashboard/empresa/editar/' . $empresa->id) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('dashboard/empresa/editar/' . $empresa->id) ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        <?php endif; ?>
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
                                            <td><?= esc($empresa->nombre) ?></td>
                                        </tr>
                                        <?php if ($empresa->nombre_comercial): ?>
                                        <tr>
                                            <th>Nombre Comercial:</th>
                                            <td><?= esc($empresa->nombre_comercial) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($empresa->rut): ?>
                                        <tr>
                                            <th>RUT:</th>
                                            <td><?= esc($empresa->rut) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                <?php if ($empresa->estado == 'A'): ?>
                                                    <span class="badge bg-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if ($paquete): ?>
                                        <tr>
                                            <th>Paquete:</th>
                                            <td>
                                                <span class="badge bg-info"><?= esc($paquete->nombre) ?></span>
                                                <?php if ($paquete->precio_mensual > 0): ?>
                                                    <small class="text-muted">($<?= number_format($paquete->precio_mensual, 0, ',', '.') ?>/mes)</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <tr>
                                            <th>Paquete:</th>
                                            <td><span class="badge bg-secondary">Sin Paquete</span></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Contacto -->
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fas fa-address-book"></i> Información de Contacto</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <?php if ($empresa->email): ?>
                                        <tr>
                                            <th width="40%">Email:</th>
                                            <td><a href="mailto:<?= esc($empresa->email) ?>"><?= esc($empresa->email) ?></a></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($empresa->telefono): ?>
                                        <tr>
                                            <th>Teléfono:</th>
                                            <td><a href="tel:<?= esc($empresa->telefono) ?>"><?= esc($empresa->telefono) ?></a></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($empresa->direccion): ?>
                                        <tr>
                                            <th>Direcci?n:</th>
                                            <td><?= esc($empresa->direccion) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if (!empty($empresa->url_google_maps)): ?>
                                        <tr>
                                            <th>Google Maps:</th>
                                            <td><a href="<?= esc($empresa->url_google_maps) ?>" target="_blank" rel="noopener">Ver en Google Maps</a></td>
                                        </tr>
                                        <?php endif; ?>
                                        <?php if ($empresa->sitio_web): ?>
                                        <tr>
                                            <th>Sitio Web:</th>
                                            <td><a href="<?= esc($empresa->sitio_web) ?>" target="_blank"><?= esc($empresa->sitio_web) ?></a></td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción, Misión, Visión, Valores -->
                    <?php if ($empresa->descripcion || $empresa->mision || $empresa->vision || $empresa->valores): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-file-alt"></i> Información Adicional</h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($empresa->descripcion): ?>
                                    <div class="mb-3">
                                        <h6><strong>Descripción:</strong></h6>
                                        <p><?= nl2br(esc($empresa->descripcion)) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($empresa->mision): ?>
                                    <div class="mb-3">
                                        <h6><strong>Misión:</strong></h6>
                                        <p><?= nl2br(esc($empresa->mision)) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($empresa->vision): ?>
                                    <div class="mb-3">
                                        <h6><strong>Visión:</strong></h6>
                                        <p><?= nl2br(esc($empresa->vision)) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($empresa->valores): ?>
                                    <div class="mb-3">
                                        <h6><strong>Valores:</strong></h6>
                                        <p><?= nl2br(esc($empresa->valores)) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Módulos del Paquete -->
                    <?php if ($paquete && !empty($modulos)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fas fa-cubes"></i> Módulos del Paquete (<?= count($modulos) ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($modulos as $modulo): ?>
                                        <div class="col-md-4 mb-2">
                                            <span class="badge bg-primary">
                                                <i class="fas fa-check-circle"></i> <?= esc($modulo['nombre']) ?>
                                            </span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Usuarios de la Empresa -->
                    <?php if (!empty($usuarios)): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="fas fa-users"></i> Usuarios (<?= count($usuarios) ?>)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Perfil</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                <tr>
                                                    <td><?= esc(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')) ?></td>
                                                    <td><?= esc($usuario['correo'] ?? '') ?></td>
                                                    <td><?= esc($usuario['perfil_nombre'] ?? 'N/A') ?></td>
                                                    <td>
                                                        <?php if ($usuario['estado'] == 'A'): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
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

                    <!-- Fechas -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Información del Sistema</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Fecha de Creación:</th>
                                            <td><?= $empresa->fcreacion ? date('d/m/Y H:i', strtotime($empresa->fcreacion)) : 'N/A' ?></td>
                                        </tr>
                                        <tr>
                                            <th>��ltima Actualización:</th>
                                            <td><?= $empresa->fmodificacion ? date('d/m/Y H:i', strtotime($empresa->fmodificacion)) : 'N/A' ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('boton_pago/ver') ?>

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
    
    .info-row {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #667eea;
        min-width: 150px;
        display: inline-block;
    }
    
    .badge-estado {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    .badge-pendiente { background-color: #ffc107; color: #000; }
    .badge-procesando { background-color: #17a2b8; color: #fff; }
    .badge-completado { background-color: #28a745; color: #fff; }
    .badge-fallido { background-color: #dc3545; color: #fff; }
    .badge-reembolsado { background-color: #6c757d; color: #fff; }
    
    .code-block {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 0.9em;
        word-break: break-all;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-eye me-2"></i> Detalles del Botón de Pago</h2>
                        <p style="color: white;">Información completa del botón de pago y su estado</p>
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
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

            <?php if (isset($pago) && $pago): ?>
                <!-- Información General -->
                <div class="section-card">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i> Información General</h4>
                    
                    <div class="info-row">
                        <span class="info-label">ID del Pago:</span>
                        <span><?= $pago['id'] ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Título:</span>
                        <span><?= esc($pago['observaciones'] ?? 'Sin título') ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Monto:</span>
                        <span><strong>$<?= number_format($pago['monto'], 2) ?> <?= esc($pago['moneda'] ?? 'CLP') ?></strong></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <?php
                        $estado = $pago['estado_pago'] ?? 'pendiente';
                        $badgeClass = 'badge-pendiente';
                        if ($estado === 'completado') $badgeClass = 'badge-completado';
                        elseif ($estado === 'procesando') $badgeClass = 'badge-procesando';
                        elseif ($estado === 'fallido') $badgeClass = 'badge-fallido';
                        elseif ($estado === 'reembolsado') $badgeClass = 'badge-reembolsado';
                        ?>
                        <span class="badge badge-estado <?= $badgeClass ?>">
                            <?= ucfirst($estado) ?>
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Fecha de Creación:</span>
                        <span><?= date('d/m/Y H:i:s', strtotime($pago['fcreacion'])) ?></span>
                    </div>
                    
                    <?php if (!empty($pago['fecha_pago'])): ?>
                        <div class="info-row">
                            <span class="info-label">Fecha de Pago:</span>
                            <span><?= date('d/m/Y H:i:s', strtotime($pago['fecha_pago'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Información de Mercado Pago -->
                <?php if (!empty($pago['mp_preference_id'])): ?>
                    <div class="section-card">
                        <h4 class="mb-4"><i class="fas fa-credit-card me-2"></i> Información de Mercado Pago</h4>
                        
                        <div class="info-row">
                            <span class="info-label">Preference ID:</span>
                            <div class="code-block"><?= esc($pago['mp_preference_id']) ?></div>
                        </div>
                        
                        <?php if (!empty($pago['mp_payment_id'])): ?>
                            <div class="info-row">
                                <span class="info-label">Payment ID:</span>
                                <div class="code-block"><?= esc($pago['mp_payment_id']) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($pago['mp_status'])): ?>
                            <div class="info-row">
                                <span class="info-label">Estado MP:</span>
                                <span><?= esc($pago['mp_status']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Información de la Cita -->
                <?php if (isset($pago->cita) && $pago->cita): ?>
                    <div class="section-card">
                        <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i> Información de la Cita</h4>
                        
                        <?php if (isset($pago->cita->fecha)): ?>
                            <div class="info-row">
                                <span class="info-label">Fecha:</span>
                                <span><?= date('d/m/Y H:i', strtotime($pago->cita->fecha)) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($pago->cita->paciente_id)): ?>
                            <div class="info-row">
                                <span class="info-label">Paciente ID:</span>
                                <span><?= $pago->cita->paciente_id ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Acciones -->
                <div class="section-card">
                    <h4 class="mb-4"><i class="fas fa-cog me-2"></i> Acciones</h4>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i> Ver Todos
                        </a>
                        <a href="<?= base_url('dashboard/boton-pago/crear') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Crear Nuevo
                        </a>
                        <?php if (!empty($pago['mp_preference_id'])): ?>
                            <a href="https://www.mercadopago.com.mx/activities/payments/<?= esc($pago['mp_preference_id']) ?>" 
                               target="_blank" class="btn btn-info">
                                <i class="fas fa-external-link-alt me-2"></i> Ver en Mercado Pago
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="section-card">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se encontró información del pago.
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Volver a la Lista
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

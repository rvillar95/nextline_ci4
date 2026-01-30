<?= $this->extend('layout/dashboard') ?>

<?= $this->section('pago/editar') ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #30cfd0;
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
        color: #30cfd0;
        min-width: 180px;
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
                        <h2 style="color: white;"><i class="fas fa-eye me-2"></i> Detalles del Pago</h2>
                        <p style="color: white;">Información completa del pago y su estado</p>
                    </div>
                    <a href="<?= base_url('dashboard/pago/lista') ?>" class="btn btn-light">
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
                <?php 
                // Convertir objeto a array si es necesario
                $pagoData = is_object($pago) ? (array)$pago : $pago;
                ?>
                
                <!-- Información General -->
                <div class="section-card">
                    <h4 class="mb-4"><i class="fas fa-info-circle me-2"></i> Información General</h4>
                    
                    <div class="info-row">
                        <span class="info-label">ID del Pago:</span>
                        <span><?= esc($pagoData['id'] ?? $pago->id ?? 'N/A') ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Empresa:</span>
                        <span><?= esc($pago->empresa->nombre ?? 'N/A') ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Monto:</span>
                        <span><strong>$<?= number_format($pagoData['monto'] ?? $pago->monto ?? 0, 0, ',', '.') ?> <?= esc($pagoData['moneda'] ?? $pago->moneda ?? 'CLP') ?></strong></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Tipo de Pago:</span>
                        <span><?= esc(ucfirst($pagoData['tipo_pago'] ?? $pago->tipo_pago ?? 'N/A')) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <?php
                        $estado = $pagoData['estado_pago'] ?? $pago->estado_pago ?? 'pendiente';
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
                        <span class="info-label">Método de Pago:</span>
                        <span><?= esc(ucfirst($pagoData['metodo_pago'] ?? $pago->metodo_pago ?? 'N/A')) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Fecha de Creación:</span>
                        <span><?= date('d/m/Y H:i:s', strtotime($pagoData['fcreacion'] ?? $pago->fcreacion ?? 'now')) ?></span>
                    </div>
                    
                    <?php if (!empty($pagoData['fecha_pago'] ?? $pago->fecha_pago ?? null)): ?>
                        <div class="info-row">
                            <span class="info-label">Fecha de Pago:</span>
                            <span><?= date('d/m/Y', strtotime($pagoData['fecha_pago'] ?? $pago->fecha_pago)) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($pagoData['referencia'] ?? $pago->referencia ?? null)): ?>
                        <div class="info-row">
                            <span class="info-label">Referencia:</span>
                            <span><?= esc($pagoData['referencia'] ?? $pago->referencia) ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($pagoData['observaciones'] ?? $pago->observaciones ?? null)): ?>
                        <div class="info-row">
                            <span class="info-label">Observaciones:</span>
                            <span><?= esc($pagoData['observaciones'] ?? $pago->observaciones) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Información de Mercado Pago -->
                <?php if (!empty($pagoData['mp_preference_id'] ?? $pago->mp_preference_id ?? null)): ?>
                    <div class="section-card">
                        <h4 class="mb-4"><i class="fas fa-credit-card me-2"></i> Información de Mercado Pago</h4>
                        
                        <div class="info-row">
                            <span class="info-label">Preference ID:</span>
                            <div class="code-block"><?= esc($pagoData['mp_preference_id'] ?? $pago->mp_preference_id) ?></div>
                        </div>
                        
                        <?php if (!empty($pagoData['mp_payment_id'] ?? $pago->mp_payment_id ?? null)): ?>
                            <div class="info-row">
                                <span class="info-label">Payment ID:</span>
                                <div class="code-block"><?= esc($pagoData['mp_payment_id'] ?? $pago->mp_payment_id) ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($pagoData['mp_status'] ?? $pago->mp_status ?? null)): ?>
                            <div class="info-row">
                                <span class="info-label">Estado MP:</span>
                                <span><?= esc($pagoData['mp_status'] ?? $pago->mp_status) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($pagoData['mp_preference_id'] ?? $pago->mp_preference_id ?? null)): ?>
                            <div class="info-row">
                                <span class="info-label">Ver en Mercado Pago:</span>
                                <a href="https://www.mercadopago.cl/developers/panel/app/<?= esc($pagoData['mp_preference_id'] ?? $pago->mp_preference_id) ?>" 
                                   target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-external-link-alt me-2"></i> Abrir en Panel MP
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Información de la Cita (si está vinculada) -->
                <?php if (!empty($pagoData['detalle_agenda_id'] ?? $pago->detalle_agenda_id ?? null)): ?>
                    <div class="section-card">
                        <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i> Información de la Cita</h4>
                        
                        <div class="info-row">
                            <span class="info-label">ID de Cita:</span>
                            <span><?= esc($pagoData['detalle_agenda_id'] ?? $pago->detalle_agenda_id) ?></span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Ver Cita:</span>
                            <a href="<?= base_url('dashboard/agenda/consulta/' . ($pagoData['detalle_agenda_id'] ?? $pago->detalle_agenda_id)) ?>" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-eye me-2"></i> Ver Detalles de la Cita
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Acciones -->
                <div class="section-card">
                    <h4 class="mb-4"><i class="fas fa-cog me-2"></i> Acciones</h4>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('dashboard/pago/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i> Ver Todos
                        </a>
                        <a href="<?= base_url('dashboard/pago/editar/' . ($pagoData['id'] ?? $pago->id)) ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <?php if (($pagoData['estado_pago'] ?? $pago->estado_pago ?? '') === 'pendiente'): ?>
                            <button onclick="procesarPago(<?= $pagoData['id'] ?? $pago->id ?>)" class="btn btn-success">
                                <i class="fas fa-check me-2"></i> Procesar Pago
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="section-card">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se encontró información del pago.
                    </div>
                    <a href="<?= base_url('dashboard/pago/lista') ?>" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i> Volver a la Lista
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Función para procesar un pago pendiente
function procesarPago(pagoId) {
    if (!pagoId) {
        alert('Error: ID de pago no válido');
        return;
    }
    
    if (!confirm('¿Está seguro de que desea procesar este pago?')) {
        return;
    }
    
    var referencia = prompt('Ingrese la referencia del pago (opcional):', '');
    
    $.ajax({
        url: '<?= base_url('dashboard/pago/procesar') ?>',
        type: 'POST',
        data: {
            id: pagoId,
            referencia: referencia || '',
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Pago procesado exitosamente');
                window.location.reload();
            } else {
                alert('Error: ' + (response.error || 'No se pudo procesar el pago'));
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            alert('Error al procesar el pago. Por favor, intente nuevamente.');
        }
    });
}
</script>

<?= $this->endSection() ?>

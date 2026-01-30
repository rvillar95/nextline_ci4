<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paciente/detalle') ?>

<style>
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
    }
    
    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #495057;
        min-width: 180px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-value {
        color: #212529;
        flex: 1;
    }
    
    .icon-label {
        color: #667eea;
        font-size: 1rem;
    }
    
    .badge-custom {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn-action {
        padding: 4px 12px;
        font-size: 0.85rem;
        border-radius: 6px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-circle me-2"></i> Detalle del Paciente</h2>
                        <p style="color: white; margin-bottom: 0;">
                            <?= esc(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')) ?>
                            <?php if ($paciente->rut_dni): ?>
                                · <?= esc($paciente->rut_dni) ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/paciente/editar/' . $paciente->id) ?>" class="btn btn-light me-2">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <a href="<?= base_url('dashboard/paciente/lista') ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Información Básica -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-id-card icon-label"></i>
                    <span>Información Básica</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user icon-label"></i> Nombre Completo
                            </div>
                            <div class="info-value">
                                <?= esc(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? '')) ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-id-badge icon-label"></i> RUT/DNI
                            </div>
                            <div class="info-value">
                                <?= esc($paciente->rut_dni ?? 'No registrado') ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar icon-label"></i> Fecha de Nacimiento
                            </div>
                            <div class="info-value">
                                <?php if ($paciente->fecha_nacimiento): ?>
                                    <?= date('d/m/Y', strtotime($paciente->fecha_nacimiento)) ?>
                                    <?php
                                    $fechaNac = new \DateTime($paciente->fecha_nacimiento);
                                    $hoy = new \DateTime();
                                    $edad = $hoy->diff($fechaNac)->y;
                                    echo " (" . $edad . " años)";
                                    ?>
                                <?php else: ?>
                                    No registrado
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-venus-mars icon-label"></i> Género
                            </div>
                            <div class="info-value">
                                <?php
                                $genero = $paciente->genero ?? '';
                                echo match($genero) {
                                    'M' => 'Masculino',
                                    'F' => 'Femenino',
                                    'O' => 'Otro',
                                    default => 'No registrado'
                                };
                                ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-tag icon-label"></i> Tipo de Paciente
                            </div>
                            <div class="info-value">
                                <?php
                                $tipo = $paciente->tipo_paciente ?? '';
                                $badgeClass = match($tipo) {
                                    'particular' => 'bg-info',
                                    'convenio' => 'bg-success',
                                    'seguro' => 'bg-warning',
                                    default => 'bg-secondary'
                                };
                                $tipoText = match($tipo) {
                                    'particular' => 'Particular',
                                    'convenio' => 'Convenio',
                                    'seguro' => 'Seguro',
                                    default => 'No definido'
                                };
                                ?>
                                <span class="badge <?= $badgeClass ?> badge-custom"><?= $tipoText ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone icon-label"></i> Teléfono
                            </div>
                            <div class="info-value">
                                <?= esc($paciente->telefono ?? 'No registrado') ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-envelope icon-label"></i> Email
                            </div>
                            <div class="info-value">
                                <?php if ($paciente->email): ?>
                                    <a href="mailto:<?= esc($paciente->email) ?>"><?= esc($paciente->email) ?></a>
                                <?php else: ?>
                                    No registrado
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-map-marker-alt icon-label"></i> Dirección
                            </div>
                            <div class="info-value">
                                <?= esc($paciente->direccion ?? 'No registrado') ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-map icon-label"></i> Región / Comuna
                            </div>
                            <div class="info-value">
                                <?php
                                $region = $paciente->region_data->nombre ?? $paciente->region ?? '';
                                $comuna = $paciente->comuna_data->nombre ?? $paciente->comuna ?? '';
                                if ($region || $comuna) {
                                    echo esc($region . ($region && $comuna ? ' / ' : '') . $comuna);
                                } else {
                                    echo 'No registrado';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user-md icon-label"></i> Nutricionista
                            </div>
                            <div class="info-value">
                                <?php if (isset($paciente->nutricionista)): ?>
                                    <?= esc(($paciente->nutricionista->nombre ?? '') . ' ' . ($paciente->nutricionista->apellido ?? '')) ?>
                                <?php else: ?>
                                    No asignado
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Clínica -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-heartbeat icon-label"></i>
                    <span>Información Clínica</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-weight icon-label"></i> Peso Inicial
                            </div>
                            <div class="info-value">
                                <?= $paciente->peso_inicial ? number_format($paciente->peso_inicial, 2) . ' kg' : 'No registrado' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-ruler-vertical icon-label"></i> Altura
                            </div>
                            <div class="info-value">
                                <?= $paciente->altura ? number_format($paciente->altura, 2) . ' cm' : 'No registrado' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calculator icon-label"></i> IMC Inicial
                            </div>
                            <div class="info-value">
                                <?php if ($paciente->imc_inicial): ?>
                                    <strong><?= number_format($paciente->imc_inicial, 2) ?></strong>
                                    <?php
                                    $imc = $paciente->imc_inicial;
                                    $clasificacion = '';
                                    if ($imc < 18.5) $clasificacion = 'Bajo peso';
                                    elseif ($imc < 25) $clasificacion = 'Normal';
                                    elseif ($imc < 30) $clasificacion = 'Sobrepeso';
                                    else $clasificacion = 'Obesidad';
                                    echo '<span class="badge bg-info ms-2">' . $clasificacion . '</span>';
                                    ?>
                                <?php else: ?>
                                    No calculado
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-bullseye icon-label"></i> Objetivo
                            </div>
                            <div class="info-value">
                                <?= $paciente->objetivo ? nl2br(esc($paciente->objetivo)) : 'No definido' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-exclamation-triangle icon-label"></i> Alergias
                            </div>
                            <div class="info-value">
                                <?= $paciente->alergias ? nl2br(esc($paciente->alergias)) : 'No registradas' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-pills icon-label"></i> Medicamentos
                            </div>
                            <div class="info-value">
                                <?= $paciente->medicamentos ? nl2br(esc($paciente->medicamentos)) : 'No registrados' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-file-medical icon-label"></i> Condiciones Médicas
                            </div>
                            <div class="info-value">
                                <?= $paciente->condiciones_medicas ? nl2br(esc($paciente->condiciones_medicas)) : 'No registradas' ?>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-sticky-note icon-label"></i> Observaciones
                            </div>
                            <div class="info-value">
                                <?= $paciente->observaciones ? nl2br(esc($paciente->observaciones)) : 'Sin observaciones' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial Clínico -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-history icon-label"></i>
                    <span>Historial Clínico</span>
                    <span class="badge bg-secondary ms-2"><?= count($historial ?? []) ?></span>
                </div>
                
                <?php if (!empty($historial)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Peso</th>
                                    <th>IMC</th>
                                    <th>Motivo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial as $h): ?>
                                    <tr>
                                        <td>
                                            <?php if ($h->fecha_consulta): ?>
                                                <?= date('d/m/Y', strtotime($h->fecha_consulta)) ?>
                                                <?php if ($h->hora_consulta): ?>
                                                    <br><small class="text-muted"><?= date('H:i', strtotime($h->hora_consulta)) ?></small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $tipo = $h->tipo_registro ?? 'consulta';
                                            $badgeClass = match($tipo) {
                                                'consulta' => 'bg-primary',
                                                'seguimiento' => 'bg-info',
                                                'control' => 'bg-success',
                                                'emergencia' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($tipo) ?></span>
                                        </td>
                                        <td>
                                            <?= $h->peso_actual ? number_format($h->peso_actual, 2) . ' kg' : '—' ?>
                                        </td>
                                        <td>
                                            <?= $h->imc_actual ? number_format($h->imc_actual, 2) : '—' ?>
                                        </td>
                                        <td>
                                            <?= $h->motivo_consulta ? esc(substr($h->motivo_consulta, 0, 50)) . (strlen($h->motivo_consulta) > 50 ? '...' : '') : '—' ?>
                                        </td>
                                        <td>
                                            <?php if ($h->detalle_agenda_id): ?>
                                                <a href="<?= base_url('dashboard/agenda/consulta?id=' . $h->detalle_agenda_id) ?>" 
                                                   class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="fas fa-eye"></i> Ver
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No hay registros de historial clínico para este paciente.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Planes Alimentarios -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-utensils icon-label"></i>
                    <span>Planes Alimentarios</span>
                    <span class="badge bg-secondary ms-2"><?= count($planes ?? []) ?></span>
                </div>
                
                <?php if (!empty($planes)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha Creación</th>
                                    <th>Requerimiento (kcal)</th>
                                    <th>Distribución</th>
                                    <th>Adecuación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planes as $plan): ?>
                                    <tr>
                                        <td>
                                            <?php if ($plan->fcreacion): ?>
                                                <?= date('d/m/Y H:i', strtotime($plan->fcreacion)) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $plan->requerimiento_kcal ? number_format($plan->requerimiento_kcal, 0) . ' kcal' : '—' ?>
                                        </td>
                                        <td>
                                            <?php if ($plan->prot_porcentaje && $plan->grasa_porcentaje && $plan->cho_porcentaje): ?>
                                                P: <?= number_format($plan->prot_porcentaje, 1) ?>% · 
                                                G: <?= number_format($plan->grasa_porcentaje, 1) ?>% · 
                                                CHO: <?= number_format($plan->cho_porcentaje, 1) ?>%
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($plan->adecuacion_kcal_porc): ?>
                                                <span class="badge bg-<?= $plan->adecuacion_kcal_porc >= 90 && $plan->adecuacion_kcal_porc <= 110 ? 'success' : 'warning' ?>">
                                                    <?= number_format($plan->adecuacion_kcal_porc, 1) ?>%
                                                </span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($plan->detalle_agenda_id): ?>
                                                <a href="<?= base_url('dashboard/plan-alimentario?detalle_agenda_id=' . $plan->detalle_agenda_id) ?>" 
                                                   class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="fas fa-eye"></i> Ver Plan
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No hay planes alimentarios registrados para este paciente.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Citas / Detalle Agenda -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-calendar-alt icon-label"></i>
                    <span>Citas y Consultas</span>
                    <span class="badge bg-secondary ms-2"><?= count($citas ?? []) ?></span>
                </div>
                
                <?php if (!empty($citas)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                    <th>Tipo Consulta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citas as $cita): ?>
                                    <tr>
                                        <td>
                                            <?php 
                                            $fecha = $cita->fecha ?? $cita->fecha_agenda ?? '';
                                            if ($fecha) {
                                                // Intentar parsear fecha en formato DD-MM-YYYY
                                                if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fecha, $matches)) {
                                                    echo $fecha; // Ya está en formato correcto
                                                } else {
                                                    echo date('d/m/Y', strtotime($fecha));
                                                }
                                            } else {
                                                echo '—';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($cita->hora_inicio): ?>
                                                <?= date('H:i', strtotime($cita->hora_inicio)) ?>
                                                <?php if ($cita->hora_fin): ?>
                                                    - <?= date('H:i', strtotime($cita->hora_fin)) ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= esc($cita->modalidad_nombre ?? '—') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $estado = $cita->estado_cita ?? 'pendiente';
                                            $badgeClass = match($estado) {
                                                'confirmada' => 'bg-success',
                                                'en_proceso' => 'bg-info',
                                                'completada' => 'bg-primary',
                                                'cancelada' => 'bg-danger',
                                                'no_asistio' => 'bg-warning',
                                                default => 'bg-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $estado)) ?></span>
                                        </td>
                                        <td>
                                            <?= esc($cita->tipo_consulta ?? '—') ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('dashboard/agenda/consulta?id=' . $cita->id) ?>" 
                                               class="btn btn-sm btn-outline-primary btn-action">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No hay citas registradas para este paciente.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Documentos -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-file-alt icon-label"></i>
                    <span>Documentos</span>
                    <span class="badge bg-secondary ms-2"><?= count($documentos ?? []) ?></span>
                </div>
                
                <?php if (!empty($documentos)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $tipo = $doc->tipo_documento ?? 'otro';
                                            $tipoText = match($tipo) {
                                                'pauta_nutricional' => 'Pauta Nutricional',
                                                'receta' => 'Receta',
                                                'informe' => 'Informe',
                                                'consentimiento' => 'Consentimiento',
                                                default => 'Otro'
                                            };
                                            ?>
                                            <span class="badge bg-info"><?= $tipoText ?></span>
                                        </td>
                                        <td>
                                            <?= esc($doc->titulo ?? 'Sin título') ?>
                                        </td>
                                        <td>
                                            <?php if ($doc->fecha_documento): ?>
                                                <?= date('d/m/Y', strtotime($doc->fecha_documento)) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($doc->enviado): ?>
                                                <span class="badge bg-success">Enviado</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No enviado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($doc->archivo_ruta): ?>
                                                <a href="<?= base_url($doc->archivo_ruta) ?>" 
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="fas fa-download"></i> Descargar
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No hay documentos registrados para este paciente.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

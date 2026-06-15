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

    #tablaPlanesPaciente th.text-num,
    #tablaPlanesPaciente td.text-num {
        text-align: right;
        white-space: nowrap;
    }

    #tablaPlanesPaciente .macro-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 8px;
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
                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                        <?php if (($total_historiales ?? 0) >= 2): ?>
                        <a href="<?= base_url('dashboard/historial/comparar?paciente_id=' . (int) $paciente->id) ?>"
                           class="btn btn-light"
                           title="Comparar evolución entre consultas de este paciente">
                            <i class="fas fa-chart-line me-2"></i> Comparar historial
                        </a>
                        <?php endif; ?>
                        <a href="<?= base_url('dashboard/paciente/editar/' . $paciente->id) ?>" class="btn btn-light">
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
                                    'fonasa' => 'bg-primary',
                                    'isapre' => 'bg-primary',
                                    'otro' => 'bg-secondary',
                                    default => 'bg-secondary'
                                };
                                $tipoText = match($tipo) {
                                    'particular' => 'Particular',
                                    'convenio' => 'Convenio',
                                    'seguro' => 'Seguro',
                                    'fonasa' => 'Fonasa',
                                    'isapre' => 'Isapre',
                                    'otro' => 'Otro',
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

            <!-- Consultas (citas + historial clínico unificado) -->
            <div class="section-card">
                <div class="section-title">
                    <i class="fas fa-stethoscope icon-label"></i>
                    <span>Consultas</span>
                    <span class="badge bg-secondary ms-2"><?= count($consultas ?? []) ?></span>
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <p class="text-muted small mb-0">Citas del paciente con datos clínicos cuando existen. Todas abren la misma ficha de consulta.</p>
                    <?php if (($total_historiales ?? 0) >= 2): ?>
                    <a href="<?= base_url('dashboard/historial/comparar?paciente_id=' . (int) $paciente->id) ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i> Comparar historial clínico
                    </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($consultas)): ?>
                    <div class="table-responsive">
                        <table id="tablaConsultasPaciente" class="table table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Modalidad</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                    <th>Peso</th>
                                    <th>IMC</th>
                                    <th>Resumen</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consultas as $c): ?>
                                    <?php
                                    $fechaRaw = $c->fecha ?? $c->fecha_agenda ?? '';
                                    $fechaOrder = '';
                                    $fechaTxt = '—';
                                    if ($fechaRaw) {
                                        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $fechaRaw, $m)) {
                                            $fechaOrder = $m[3] . '-' . $m[2] . '-' . $m[1];
                                            $fechaTxt = $fechaRaw;
                                        } else {
                                            $ts = strtotime($fechaRaw);
                                            $fechaOrder = $ts ? date('Y-m-d', $ts) : '';
                                            $fechaTxt = $ts ? date('d/m/Y', $ts) : $fechaRaw;
                                        }
                                    }
                                    if ($c->hora_inicio) {
                                        $fechaOrder .= ' ' . date('H:i:s', strtotime($c->hora_inicio));
                                    }
                                    $estado = $c->estado_cita ?? 'pendiente';
                                    $estadoBadge = match ($estado) {
                                        'confirmada' => 'bg-success',
                                        'en_proceso' => 'bg-info',
                                        'completada' => 'bg-primary',
                                        'cancelada' => 'bg-danger',
                                        'no_asistio' => 'bg-warning',
                                        default => 'bg-secondary',
                                    };
                                    $tipo = $c->tipo_consulta ?? $c->historial_tipo ?? '—';
                                    $motivoRaw = $c->motivo_consulta ?: ($c->motivo ?? '');
                                    $motivoTxt = \App\Models\HistorialClinico::richTextToPlain($motivoRaw);
                                    if (strlen($motivoTxt) > 60) {
                                        $motivoTxt = substr($motivoTxt, 0, 60) . '…';
                                    }
                                    ?>
                                    <tr>
                                        <td data-order="<?= esc($fechaOrder) ?>"><?= esc($fechaTxt) ?></td>
                                        <td data-order="<?= $c->hora_inicio ? esc(date('H:i', strtotime($c->hora_inicio))) : '' ?>">
                                            <?php if ($c->hora_inicio): ?>
                                                <?= date('H:i', strtotime($c->hora_inicio)) ?>
                                                <?php if ($c->hora_fin): ?>– <?= date('H:i', strtotime($c->hora_fin)) ?><?php endif; ?>
                                            <?php else: ?>—<?php endif; ?>
                                        </td>
                                        <td><?= esc($c->modalidad_nombre ?? '—') ?></td>
                                        <td><span class="badge <?= $estadoBadge ?>"><?= ucfirst(str_replace('_', ' ', $estado)) ?></span></td>
                                        <td><?= esc(str_replace('_', ' ', $tipo)) ?></td>
                                        <td><?= $c->peso_actual ? number_format((float) $c->peso_actual, 2) . ' kg' : '—' ?></td>
                                        <td><?= $c->imc_actual ? number_format((float) $c->imc_actual, 2) : '—' ?></td>
                                        <td class="small text-muted"><?= $motivoTxt !== '' ? esc($motivoTxt) : '—' ?></td>
                                        <td>
                                            <a href="<?= base_url('dashboard/agenda/consulta?id=' . (int) $c->id) ?>"
                                               class="btn btn-sm btn-outline-primary btn-action"
                                               title="Abrir consulta completa">
                                                <i class="fas fa-eye"></i> Ver consulta
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> No hay consultas registradas para este paciente.
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
                        <table id="tablaPlanesPaciente" class="table table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Fecha creación</th>
                                    <th>Consulta</th>
                                    <th class="text-num">Requerimiento</th>
                                    <th>Distribución</th>
                                    <th class="text-center">Adecuación</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($planes as $plan): ?>
                                    <?php
                                    $fechaCreacionOrder = '';
                                    $fechaCreacionTxt = '—';
                                    if (!empty($plan->fcreacion)) {
                                        $tsCreacion = strtotime($plan->fcreacion);
                                        if ($tsCreacion) {
                                            $fechaCreacionOrder = date('Y-m-d H:i:s', $tsCreacion);
                                            $fechaCreacionTxt = date('d/m/Y H:i', $tsCreacion);
                                        }
                                    }

                                    $fechaConsultaOrder = '';
                                    $fechaConsultaTxt = '—';
                                    if (!empty($plan->fecha_consulta)) {
                                        $rawFecha = (string) $plan->fecha_consulta;
                                        if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $rawFecha, $m)) {
                                            $fechaConsultaOrder = $m[3] . '-' . $m[2] . '-' . $m[1];
                                            $fechaConsultaTxt = $rawFecha;
                                        } else {
                                            $tsConsulta = strtotime($rawFecha);
                                            if ($tsConsulta) {
                                                $fechaConsultaOrder = date('Y-m-d', $tsConsulta);
                                                $fechaConsultaTxt = date('d/m/Y', $tsConsulta);
                                            }
                                        }
                                        if (!empty($plan->hora_consulta)) {
                                            $horaTxt = date('H:i', strtotime($plan->hora_consulta));
                                            $fechaConsultaTxt .= ' ' . $horaTxt;
                                            $fechaConsultaOrder .= ' ' . date('H:i:s', strtotime($plan->hora_consulta));
                                        }
                                    }

                                    $tieneMacros = is_numeric($plan->prot_porcentaje ?? null)
                                        && is_numeric($plan->grasa_porcentaje ?? null)
                                        && is_numeric($plan->cho_porcentaje ?? null);

                                    $adecuacion = is_numeric($plan->adecuacion_kcal_porc ?? null)
                                        ? (float) $plan->adecuacion_kcal_porc
                                        : null;
                                    $adecuacionBadge = 'secondary';
                                    if ($adecuacion !== null) {
                                        if ($adecuacion >= 90 && $adecuacion <= 110) {
                                            $adecuacionBadge = 'success';
                                        } elseif ($adecuacion > 0) {
                                            $adecuacionBadge = 'warning';
                                        }
                                    }

                                    $urlPlan = base_url('dashboard/plan-alimentario')
                                        . '?paciente_id=' . (int) $paciente->id
                                        . '&tab=plan';
                                    if (!empty($plan->detalle_agenda_id)) {
                                        $urlPlan .= '&detalle_agenda_id=' . (int) $plan->detalle_agenda_id;
                                    }
                                    ?>
                                    <tr>
                                        <td data-order="<?= esc($fechaCreacionOrder) ?>"><?= esc($fechaCreacionTxt) ?></td>
                                        <td data-order="<?= esc($fechaConsultaOrder) ?>">
                                            <?php if ($fechaConsultaTxt !== '—'): ?>
                                                <?= esc($fechaConsultaTxt) ?>
                                                <?php if (!empty($plan->estado_consulta)): ?>
                                                    <span class="badge bg-light text-dark border ms-1">
                                                        <?= esc(ucfirst(str_replace('_', ' ', $plan->estado_consulta))) ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin consulta</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-num" data-order="<?= is_numeric($plan->requerimiento_kcal ?? null) ? (float) $plan->requerimiento_kcal : '' ?>">
                                            <?= is_numeric($plan->requerimiento_kcal ?? null)
                                                ? number_format((float) $plan->requerimiento_kcal, 0) . ' kcal'
                                                : '—' ?>
                                        </td>
                                        <td>
                                            <?php if ($tieneMacros): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <span class="badge bg-primary macro-badge">P <?= number_format((float) $plan->prot_porcentaje, 1) ?>%</span>
                                                    <span class="badge bg-warning text-dark macro-badge">G <?= number_format((float) $plan->grasa_porcentaje, 1) ?>%</span>
                                                    <span class="badge bg-success macro-badge">CHO <?= number_format((float) $plan->cho_porcentaje, 1) ?>%</span>
                                                </div>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" data-order="<?= $adecuacion !== null ? $adecuacion : '' ?>">
                                            <?php if ($adecuacion !== null): ?>
                                                <span class="badge bg-<?= $adecuacionBadge ?>">
                                                    <?= number_format($adecuacion, 1) ?>%
                                                </span>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="<?= $urlPlan ?>"
                                               class="btn btn-sm btn-outline-primary btn-action"
                                               title="Abrir plan alimentario">
                                                <i class="fas fa-eye"></i> Ver plan
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> No hay planes alimentarios registrados para este paciente.
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
                        <table id="tablaDocumentosPaciente" class="table table-hover table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Enviado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $doc): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            $tipoText = match ($doc->tipo_documento ?? 'otro') {
                                                'pauta_nutricional' => 'Pauta Nutricional',
                                                'receta' => 'Receta',
                                                'informe' => 'Informe',
                                                'consentimiento' => 'Consentimiento',
                                                default => 'Otro',
                                            };
                                            ?>
                                            <span class="badge bg-info"><?= $tipoText ?></span>
                                        </td>
                                        <td><?= esc($doc->titulo ?? 'Sin título') ?></td>
                                        <td data-order="<?= $doc->fecha_documento ? esc($doc->fecha_documento) : '' ?>">
                                            <?= $doc->fecha_documento ? date('d/m/Y', strtotime($doc->fecha_documento)) : '—' ?>
                                        </td>
                                        <td>
                                            <?= $doc->enviado
                                                ? '<span class="badge bg-success">Enviado</span>'
                                                : '<span class="badge bg-secondary">Pendiente</span>' ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <a href="<?= base_url('dashboard/documento/detalle/' . (int) $doc->id) ?>"
                                               class="btn btn-sm btn-outline-info btn-action">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            <?php if (!empty($doc->archivo_ruta)): ?>
                                                <a href="<?= base_url('dashboard/documento/' . (int) $doc->id . '/descargar') ?>"
                                                   class="btn btn-sm btn-outline-primary btn-action ms-1">
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

<script>
$(document).ready(function() {
    var dtLang = { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' };
    var dtCommon = {
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
        language: dtLang,
        responsive: true,
        autoWidth: false
    };

    if ($('#tablaConsultasPaciente').length && $('#tablaConsultasPaciente tbody tr').length) {
        $('#tablaConsultasPaciente').DataTable($.extend({}, dtCommon, {
            order: [[0, 'desc']],
            columnDefs: [{ orderable: false, targets: 8 }]
        }));
    }

    if ($('#tablaDocumentosPaciente').length && $('#tablaDocumentosPaciente tbody tr').length) {
        $('#tablaDocumentosPaciente').DataTable($.extend({}, dtCommon, {
            order: [[2, 'desc']],
            columnDefs: [{ orderable: false, targets: 4 }]
        }));
    }

    if ($('#tablaPlanesPaciente').length && $('#tablaPlanesPaciente tbody tr').length) {
        $('#tablaPlanesPaciente').DataTable($.extend({}, dtCommon, {
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: 5 },
                { className: 'text-num', targets: 2 },
                { className: 'text-center', targets: [4, 5] }
            ]
        }));
    }
});
</script>

<?= $this->endSection() ?>

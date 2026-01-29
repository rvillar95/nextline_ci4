<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/consulta') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Flatpickr Date Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/k10uo8qhvhuxj1ho5z73jcbhzpwlspewyrz3lkbu5b99faon/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<!-- Tagify para tags -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<!-- Chart.js (para gráficos de composición corporal y somatocarta) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #4A90E2 0%, #6BCB77 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        color: white;
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #4A90E2;
    }
    
    .timer-display {
        font-size: 3rem;
        font-weight: bold;
        color: #4A90E2;
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        margin: 20px 0;
    }
    
    .btn-action-large {
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 10px;
        transition: all 0.3s;
    }
    
    .btn-action-large:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .status-iniciada {
        background: #FFA726;
        color: white;
    }
    
    .status-pendiente {
        background: #4A90E2;
        color: white;
    }
    
    .status-completada {
        background: #6BCB77;
        color: white;
    }
    
    /* Estilos para campos según método de cálculo */
    .metodo-4 {
        border-left: 4px solid #dc3545 !important;
        background-color: #fff5f5 !important;
    }
    
    .metodo-5 {
        border-left: 4px solid #0d6efd !important;
        background-color: #f0f7ff !important;
    }
    
    .metodo-2 {
        border-left: 4px solid #198754 !important;
        background-color: #f0fff4 !important;
    }
    
    /* Campos con múltiples métodos - usar gradiente o color combinado */
    .metodo-4.metodo-5 {
        border-left: 4px solid #6f42c1 !important;
        background: linear-gradient(90deg, #fff5f5 0%, #f0f7ff 100%) !important;
    }
    
    .metodo-4.metodo-2 {
        border-left: 4px solid #fd7e14 !important;
        background: linear-gradient(90deg, #fff5f5 0%, #f0fff4 100%) !important;
    }
    
    .metodo-5.metodo-2 {
        border-left: 4px solid #20c997 !important;
        background: linear-gradient(90deg, #f0f7ff 0%, #f0fff4 100%) !important;
    }
    
    .metodo-4.metodo-5.metodo-2 {
        border-left: 4px solid #6c757d !important;
        background: linear-gradient(90deg, #fff5f5 0%, #f0f7ff 50%, #f0fff4 100%) !important;
    }
    
    .metodo-4:focus,
    .metodo-5:focus,
    .metodo-2:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-md me-2"></i> Consulta en Curso</h2>
                        <p style="color: white;">Seguimiento y registro de la consulta con el paciente</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Calendario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del Paciente y Cita -->
            <div class="section-card">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary"><i class="fas fa-user me-2"></i> Información del Paciente</h5>
                        <hr>
                        <p><strong>Nombre:</strong> <?= esc($cita->nombre . ' ' . $cita->apellido) ?></p>
                        <?php if ($cita->rut_dni): ?>
                        <p><strong>RUT/DNI:</strong> <?= esc($cita->rut_dni) ?></p>
                        <?php endif; ?>
                        <?php if ($cita->telefono): ?>
                        <p><strong>Teléfono:</strong> <a href="tel:<?= esc($cita->telefono) ?>"><?= esc($cita->telefono) ?></a></p>
                        <?php endif; ?>
                        <?php if ($cita->email): ?>
                        <p><strong>Email:</strong> <a href="mailto:<?= esc($cita->email) ?>"><?= esc($cita->email) ?></a></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-info"><i class="fas fa-calendar-alt me-2"></i> Información de la Cita</h5>
                        <hr>
                        <p><strong>Fecha:</strong> <?= esc($fecha) ?></p>
                        <p><strong>Hora Programada:</strong> <?= date('H:i', strtotime($cita->hora_inicio)) ?> - <?= date('H:i', strtotime($cita->hora_fin)) ?></p>
                        <p><strong>Modalidad:</strong> <?= esc($cita->modalidad_nombre ?? 'No definida') ?></p>
                        <p><strong>Tipo de Consulta:</strong> <?= ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control')) ?></p>
                        <?php if ($cita->motivo): ?>
                        <p><strong>Motivo:</strong> <?= esc($cita->motivo) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notas y Recordatorios del Nutricionista (Destacado) -->
            <?php if (!empty($cita->notas_nutricionista)): ?>
            <div class="section-card" style="border-left: 4px solid #FFA726 !important; background: linear-gradient(135deg, #FFF8E1 0%, #FFFFFF 100%);">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-sticky-note fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="text-warning mb-3">
                            <i class="fas fa-lightbulb me-2"></i> Notas y Recordatorios
                        </h5>
                        <div class="alert alert-warning mb-0" style="background-color: rgba(255, 167, 38, 0.1); border: 1px solid rgba(255, 167, 38, 0.3);">
                            <div style="white-space: pre-wrap; font-size: 1.05em; line-height: 1.6;"><?= esc($cita->notas_nutricionista) ?></div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i> Estas notas fueron agregadas durante la planificación de la cita
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Consulta Anterior (Solo Referencia) -->
            <?php if (!empty($consulta_anterior)): ?>
            <div class="section-card" style="border-left: 4px solid #90A4AE !important; background: linear-gradient(135deg, #ECEFF1 0%, #FFFFFF 100%);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-secondary mb-0">
                        <i class="fas fa-history me-2"></i> Consulta Anterior (Referencia)
                    </h5>
                    <span class="badge bg-secondary">
                        <?= $consulta_anterior->fecha ?: $consulta_anterior->fecha_agenda ?> 
                        <?= date('H:i', strtotime($consulta_anterior->hora_inicio)) ?>
                    </span>
                </div>
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle me-1"></i> Información de la última consulta completada. Solo para referencia, no afecta esta consulta.
                </p>
                
                <div class="row">
                    <?php if (!empty($consulta_anterior->objetivos)): ?>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-success"><i class="fas fa-bullseye me-2"></i> Objetivos Establecidos</h6>
                        <div class="alert alert-light border-start border-success border-3" style="max-height: 150px; overflow-y: auto;">
                            <?= $consulta_anterior->objetivos ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consulta_anterior->plan_alimentacion)): ?>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-info"><i class="fas fa-utensils me-2"></i> Plan de Alimentación</h6>
                        <div class="alert alert-light border-start border-info border-3" style="max-height: 150px; overflow-y: auto;">
                            <?= $consulta_anterior->plan_alimentacion ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consulta_anterior->recomendaciones)): ?>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-warning"><i class="fas fa-lightbulb me-2"></i> Recomendaciones</h6>
                        <div class="alert alert-light border-start border-warning border-3" style="max-height: 150px; overflow-y: auto;">
                            <?= $consulta_anterior->recomendaciones ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($consulta_anterior->notas_consulta)): ?>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-primary"><i class="fas fa-sticky-note me-2"></i> Notas de Consulta</h6>
                        <div class="alert alert-light border-start border-primary border-3" style="max-height: 150px; overflow-y: auto;">
                            <?= $consulta_anterior->notas_consulta ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if ($consulta_anterior->peso_anterior || $consulta_anterior->imc_anterior): ?>
                <div class="mt-3 pt-3 border-top">
                    <h6 class="text-secondary"><i class="fas fa-chart-line me-2"></i> Mediciones de Referencia</h6>
                    <div class="row">
                        <?php if ($consulta_anterior->peso_anterior): ?>
                        <div class="col-md-3">
                            <small class="text-muted">Peso</small>
                            <div class="fw-bold"><?= number_format($consulta_anterior->peso_anterior, 1) ?> kg</div>
                        </div>
                        <?php endif; ?>
                        <?php if ($consulta_anterior->imc_anterior): ?>
                        <div class="col-md-3">
                            <small class="text-muted">IMC</small>
                            <div class="fw-bold"><?= number_format($consulta_anterior->imc_anterior, 2) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($consulta_anterior->cintura_anterior): ?>
                        <div class="col-md-3">
                            <small class="text-muted">Cintura</small>
                            <div class="fw-bold"><?= number_format($consulta_anterior->cintura_anterior, 1) ?> cm</div>
                        </div>
                        <?php endif; ?>
                        <?php if ($consulta_anterior->grasa_anterior): ?>
                        <div class="col-md-3">
                            <small class="text-muted">Grasa Corporal</small>
                            <div class="fw-bold"><?= number_format($consulta_anterior->grasa_anterior, 1) ?>%</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Control de Consulta -->
            <div class="section-card">
                <div class="text-center">
                    <h5 class="mb-4"><i class="fas fa-clock me-2"></i> Control de Consulta</h5>
                    
                    <!-- Estado de la consulta -->
                    <div class="mb-4">
                        <?php 
                        $estadoConsulta = 'pendiente';
                        if ($cita->fecha_inicio_real && !$cita->fecha_fin_real) {
                            $estadoConsulta = 'iniciada';
                        } elseif ($cita->fecha_fin_real) {
                            $estadoConsulta = 'completada';
                        }
                        ?>
                        <span class="status-badge status-<?= $estadoConsulta ?>">
                            <?php if ($estadoConsulta === 'pendiente'): ?>
                                <i class="fas fa-hourglass-half me-2"></i> Pendiente de Iniciar
                            <?php elseif ($estadoConsulta === 'iniciada'): ?>
                                <i class="fas fa-play-circle me-2"></i> Consulta en Curso
                            <?php else: ?>
                                <i class="fas fa-check-circle me-2"></i> Consulta Completada
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Timer (si está iniciada) -->
                    <?php if ($estadoConsulta === 'iniciada'): ?>
                    <div class="timer-display" id="timerDisplay">
                        00:00:00
                    </div>
                    <?php endif; ?>

                    <!-- Botones de Control -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <?php if ($estadoConsulta === 'pendiente'): ?>
                            <button class="btn btn-success btn-action-large" onclick="iniciarConsulta(<?= $cita->id ?>)">
                                <i class="fas fa-play-circle me-2"></i> Iniciar Consulta
                            </button>
                        <?php elseif ($estadoConsulta === 'iniciada'): ?>
                            <button class="btn btn-danger btn-action-large" onclick="terminarConsulta(<?= $cita->id ?>)">
                                <i class="fas fa-stop-circle me-2"></i> Terminar Consulta
                            </button>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Consulta Finalizada</strong><br>
                                <?php if ($cita->fecha_inicio_real && $cita->fecha_fin_real): ?>
                                    Duración: <?= $cita->duracion_real ?? 'N/A' ?> minutos
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sección de Mediciones Corporales (Colapsable) - PRIMERO para primera consulta -->
            <div class="section-card" style="border-left: 4px solid #4A90E2 !important;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="text-primary mb-0">
                        <i class="fas fa-ruler-combined me-2"></i> Mediciones Corporales y Registro Clínico
                    </h5>
                    <button 
                        type="button" 
                        class="btn btn-outline-primary btn-sm" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#medicionesCollapse"
                        aria-expanded="false"
                        aria-controls="medicionesCollapse"
                        id="btnToggleMediciones"
                    >
                        <i class="fas fa-chevron-down me-2"></i> Mostrar/Ocultar
                    </button>
                </div>
                <p class="text-muted small mb-3">
                    <i class="fas fa-info-circle me-1"></i> Registra las mediciones corporales, pliegues cutáneos y datos clínicos de esta consulta. Estos datos se guardarán en el historial clínico del paciente.
                </p>
                
                <!-- Leyenda de Métodos de Cálculo -->
                <div class="alert alert-light border mb-3" style="background-color: #f8f9fa;">
                    <div class="d-flex align-items-center mb-2">
                        <strong class="me-2"><i class="fas fa-info-circle me-1"></i> Leyenda de Métodos de Cálculo:</strong>
                    </div>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #dc3545 !important; background-color: #fff5f5;">
                                <span class="fw-bold text-danger">4 Componentes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #0d6efd !important; background-color: #f0f7ff;">
                                <span class="fw-bold text-primary">5 Componentes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #198754 !important; background-color: #f0fff4;">
                                <span class="fw-bold text-success">2 Componentes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #6f42c1 !important; background-color: #f3effd;">
                                <span class="fw-bold" style="color: #6f42c1;">4 y 5 Componentes</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #6c757d !important; background-color: #e9ecef;">
                                <span class="fw-bold text-secondary">4, 5 y 2 Componentes</span>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        El borde de cada campo indica para qué método(s) se usa: <strong>Rojo</strong> solo 4 · <strong>Azul</strong> solo 5 · <strong>Verde</strong> solo 2 · <strong>Morado</strong> 4 y 5 · <strong>Gris</strong> 4, 5 y 2. Los que no llevan color no se usan en estos métodos.
                    </small>
                </div>
                
                <div class="collapse" id="medicionesCollapse">
                    <form id="formMediciones" onsubmit="guardarMediciones(event)">
                        <?= csrf_field() ?>
                        <input type="hidden" name="detalle_agenda_id" value="<?= $cita->id ?>">
                        <input type="hidden" name="paciente_id" value="<?= $cita->paciente_id ?>">
                        <input type="hidden" name="historial_id" id="historial_id" value="<?= !empty($historial['id']) ? esc($historial['id']) : '' ?>">
                        <?php
                        $ac = [];
                        $aa = [];
                        if (!empty($historial['anamnesis_clinica'])) {
                            $dec = is_string($historial['anamnesis_clinica']) ? json_decode($historial['anamnesis_clinica'], true) : $historial['anamnesis_clinica'];
                            if (is_array($dec)) $ac = $dec;
                        }
                        if (!empty($historial['anamnesis_alimentaria'])) {
                            $dec = is_string($historial['anamnesis_alimentaria']) ? json_decode($historial['anamnesis_alimentaria'], true) : $historial['anamnesis_alimentaria'];
                            if (is_array($dec)) $aa = $dec;
                        }
                        ?>
                        <!-- Medidas Básicas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-weight me-2"></i> Medidas Básicas</h6>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Peso (kg) *</label>
                                <input type="number" name="peso_actual" id="peso_actual" class="form-control" step="0.01" min="0" placeholder="Ej: 70.5" value="<?= !empty($historial['peso_actual']) ? esc($historial['peso_actual']) : '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Altura (cm) *</label>
                                <input type="number" name="altura_actual" id="altura_actual" class="form-control" step="0.01" min="0" placeholder="Ej: 170" value="<?= !empty($historial['altura_actual']) ? esc($historial['altura_actual']) : '' ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Altura Sentado (cm)</label>
                                <input type="number" name="altura_sentado" id="altura_sentado" class="form-control" step="0.01" min="0" placeholder="Ej: 90" value="<?= !empty($historial['altura_sentado']) ? esc($historial['altura_sentado']) : '' ?>">
                                <small class="text-muted">Para métodos 4, 5 componentes</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">IMC</label>
                                <input type="number" name="imc_actual" id="imc_actual" class="form-control" step="0.01" readonly value="<?= !empty($historial['imc_actual']) ? esc($historial['imc_actual']) : '' ?>">
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>

                        <!-- Circunferencias -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-circle-notch me-2"></i> Circunferencias (cm)</h6>
                                <p class="text-muted small">Medición con cinta métrica. Se mide en centímetros (cm).</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cintura</label>
                                <input type="number" name="circunferencia_cintura" id="circunferencia_cintura" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 85.5" value="<?= !empty($historial['circunferencia_cintura']) ? esc($historial['circunferencia_cintura']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cadera</label>
                                <input type="number" name="circunferencia_cadera" id="circunferencia_cadera" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 95.0" value="<?= !empty($historial['circunferencia_cadera']) ? esc($historial['circunferencia_cadera']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Brazo Relajado</label>
                                <input type="number" name="circunferencia_brazo_relajado" id="circunferencia_brazo_relajado" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 28.5" value="<?= !empty($historial['circunferencia_brazo_relajado']) ? esc($historial['circunferencia_brazo_relajado']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Brazo Contraído</label>
                                <input type="number" name="circunferencia_brazo_contraido" id="circunferencia_brazo_contraido" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 32.0" value="<?= !empty($historial['circunferencia_brazo_contraido']) ? esc($historial['circunferencia_brazo_contraido']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muslo Medio</label>
                                <input type="number" name="circunferencia_muslo_medio" id="circunferencia_muslo_medio" class="form-control metodo-4" step="0.01" min="0" placeholder="Ej: 55.0" value="<?= !empty($historial['circunferencia_muslo_medio']) ? esc($historial['circunferencia_muslo_medio']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pantorrilla</label>
                                <input type="number" name="circunferencia_pantorrilla" id="circunferencia_pantorrilla" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 36.5" value="<?= !empty($historial['circunferencia_pantorrilla']) ? esc($historial['circunferencia_pantorrilla']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cuello</label>
                                <input type="number" name="circunferencia_cuello" id="circunferencia_cuello" class="form-control" step="0.01" min="0" placeholder="Ej: 38.0" value="<?= !empty($historial['circunferencia_cuello']) ? esc($historial['circunferencia_cuello']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tórax</label>
                                <input type="number" name="circunferencia_torax" id="circunferencia_torax" class="form-control metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 98.0" value="<?= !empty($historial['circunferencia_torax']) ? esc($historial['circunferencia_torax']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cabeza</label>
                                <input type="number" name="circunferencia_cabeza" id="circunferencia_cabeza" class="form-control metodo-5" step="0.01" min="0" placeholder="Ej: 56.0" value="<?= !empty($historial['circunferencia_cabeza']) ? esc($historial['circunferencia_cabeza']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Antebrazo Máximo</label>
                                <input type="number" name="circunferencia_antebrazo_maximo" id="circunferencia_antebrazo_maximo" class="form-control metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 28.0" value="<?= !empty($historial['circunferencia_antebrazo_maximo']) ? esc($historial['circunferencia_antebrazo_maximo']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muslo Máximo</label>
                                <input type="number" name="circunferencia_muslo_maximo" id="circunferencia_muslo_maximo" class="form-control metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 58.0" value="<?= !empty($historial['circunferencia_muslo_maximo']) ? esc($historial['circunferencia_muslo_maximo']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muñeca</label>
                                <input type="number" name="circunferencia_muneca" id="circunferencia_muneca" class="form-control metodo-4" step="0.01" min="0" placeholder="Ej: 16.5" value="<?= !empty($historial['circunferencia_muneca']) ? esc($historial['circunferencia_muneca']) : '' ?>">
                            </div>
                        </div>

                        <!-- Pliegues Cutáneos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-hand-paper me-2"></i> Pliegues Cutáneos (mm)</h6>
                                <p class="text-muted small">Medición con plicómetro. Se mide en milímetros (mm).</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tricipital</label>
                                <input type="number" name="pliegue_tricipital" id="pliegue_tricipital" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 12.5" value="<?= isset($historial['pliegue_tricipital']) && $historial['pliegue_tricipital'] !== '' ? esc($historial['pliegue_tricipital']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bicipital</label>
                                <input type="number" name="pliegue_bicipital" id="pliegue_bicipital" class="form-control metodo-4" step="0.01" min="0" placeholder="Ej: 8.3" value="<?= isset($historial['pliegue_bicipital']) && $historial['pliegue_bicipital'] !== '' ? esc($historial['pliegue_bicipital']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Subescapular</label>
                                <input type="number" name="pliegue_subescapular" id="pliegue_subescapular" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 15.2" value="<?= isset($historial['pliegue_subescapular']) && $historial['pliegue_subescapular'] !== '' ? esc($historial['pliegue_subescapular']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Suprailíaco</label>
                                <input type="number" name="pliegue_suprailíaco" id="pliegue_suprailíaco" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 18.7" value="<?= isset($historial['pliegue_suprailíaco']) && $historial['pliegue_suprailíaco'] !== '' ? esc($historial['pliegue_suprailíaco']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Supraespinal</label>
                                <input type="number" name="pliegue_supraespinal" id="pliegue_supraespinal" class="form-control metodo-2 metodo-5" step="0.01" min="0" placeholder="Ej: 16.5" value="<?= isset($historial['pliegue_supraespinal']) && $historial['pliegue_supraespinal'] !== '' ? esc($historial['pliegue_supraespinal']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Abdominal</label>
                                <input type="number" name="pliegue_abdominal" id="pliegue_abdominal" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 22.1" value="<?= isset($historial['pliegue_abdominal']) && $historial['pliegue_abdominal'] !== '' ? esc($historial['pliegue_abdominal']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muslo Anterior</label>
                                <input type="number" name="pliegue_muslo_anterior" id="pliegue_muslo_anterior" class="form-control metodo-4" step="0.01" min="0" placeholder="Ej: 20.5" value="<?= isset($historial['pliegue_muslo_anterior']) && $historial['pliegue_muslo_anterior'] !== '' ? esc($historial['pliegue_muslo_anterior']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pantorrilla Medial</label>
                                <input type="number" name="pliegue_pantorrilla_medial" id="pliegue_pantorrilla_medial" class="form-control metodo-4 metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 10.8" value="<?= isset($historial['pliegue_pantorrilla_medial']) && $historial['pliegue_pantorrilla_medial'] !== '' ? esc($historial['pliegue_pantorrilla_medial']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pectoral</label>
                                <input type="number" name="pliegue_pectoral" id="pliegue_pectoral" class="form-control" step="0.01" min="0" placeholder="Ej: 12.0" value="<?= isset($historial['pliegue_pectoral']) && $historial['pliegue_pectoral'] !== '' ? esc($historial['pliegue_pectoral']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Axilar Medio</label>
                                <input type="number" name="pliegue_axilar_medio" id="pliegue_axilar_medio" class="form-control" step="0.01" min="0" placeholder="Ej: 14.5" value="<?= isset($historial['pliegue_axilar_medio']) && $historial['pliegue_axilar_medio'] !== '' ? esc($historial['pliegue_axilar_medio']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muslo Medial</label>
                                <input type="number" name="pliegue_muslo_medial" id="pliegue_muslo_medial" class="form-control metodo-5 metodo-2" step="0.01" min="0" placeholder="Ej: 18.0" value="<?= isset($historial['pliegue_muslo_medial']) && $historial['pliegue_muslo_medial'] !== '' ? esc($historial['pliegue_muslo_medial']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suma de Pliegues (mm)</label>
                                <input type="number" name="suma_pliegues" id="suma_pliegues" class="form-control" step="0.01" readonly value="<?= isset($historial['suma_pliegues']) && $historial['suma_pliegues'] !== '' ? esc($historial['suma_pliegues']) : '' ?>">
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>

                        <!-- Diámetros Óseos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-ruler me-2"></i> Diámetros Óseos (cm)</h6>
                                <p class="text-muted small">Medición con antropómetro o caliper. Se mide en centímetros (cm). Necesarios para métodos 4, 5 componentes y somatotipo.</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Biacromial (Hombros)</label>
                                <input type="number" name="diametro_biacromial" id="diametro_biacromial" class="form-control metodo-5" step="0.01" min="0" placeholder="Ej: 38.5" value="<?= isset($historial['diametro_biacromial']) && $historial['diametro_biacromial'] !== '' ? esc($historial['diametro_biacromial']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bi-iliocristal (Cadera)</label>
                                <input type="number" name="diametro_bi_iliocristal" id="diametro_bi_iliocristal" class="form-control metodo-5" step="0.01" min="0" placeholder="Ej: 28.0" value="<?= isset($historial['diametro_bi_iliocristal']) && $historial['diametro_bi_iliocristal'] !== '' ? esc($historial['diametro_bi_iliocristal']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Húmero (Codo)</label>
                                <input type="number" name="diametro_humero" id="diametro_humero" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 6.5" value="<?= isset($historial['diametro_humero']) && $historial['diametro_humero'] !== '' ? esc($historial['diametro_humero']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fémur (Rodilla)</label>
                                <input type="number" name="diametro_femur" id="diametro_femur" class="form-control metodo-4 metodo-5" step="0.01" min="0" placeholder="Ej: 9.0" value="<?= isset($historial['diametro_femur']) && $historial['diametro_femur'] !== '' ? esc($historial['diametro_femur']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muñeca</label>
                                <input type="number" name="diametro_muneca" id="diametro_muneca" class="form-control metodo-4" step="0.01" min="0" placeholder="Ej: 5.5" value="<?= isset($historial['diametro_muneca']) && $historial['diametro_muneca'] !== '' ? esc($historial['diametro_muneca']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tobillo</label>
                                <input type="number" name="diametro_tobillo" id="diametro_tobillo" class="form-control" step="0.01" min="0" placeholder="Ej: 6.8" value="<?= isset($historial['diametro_tobillo']) && $historial['diametro_tobillo'] !== '' ? esc($historial['diametro_tobillo']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tórax Transverso</label>
                                <input type="number" name="diametro_torax_transverso" id="diametro_torax_transverso" class="form-control metodo-5" step="0.01" min="0" placeholder="Ej: 28.5" value="<?= isset($historial['diametro_torax_transverso']) && $historial['diametro_torax_transverso'] !== '' ? esc($historial['diametro_torax_transverso']) : '' ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Tórax Anteroposterior</label>
                                <input type="number" name="diametro_torax_anteroposterior" id="diametro_torax_anteroposterior" class="form-control metodo-5" step="0.01" min="0" placeholder="Ej: 20.0" value="<?= isset($historial['diametro_torax_anteroposterior']) && $historial['diametro_torax_anteroposterior'] !== '' ? esc($historial['diametro_torax_anteroposterior']) : '' ?>">
                            </div>
                        </div>

                        <!-- Composición Corporal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i> Composición Corporal</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grasa Corporal (%)</label>
                                <input type="number" name="grasa_corporal" id="grasa_corporal" class="form-control" step="0.01" min="0" max="100" placeholder="Ej: 25.5" value="<?= isset($historial['grasa_corporal']) && $historial['grasa_corporal'] !== '' ? esc($historial['grasa_corporal']) : '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grasa Corporal Calculada (%)</label>
                                <input type="number" name="grasa_corporal_calculada" id="grasa_corporal_calculada" class="form-control" step="0.01" readonly value="<?= isset($historial['grasa_corporal_calculada']) && $historial['grasa_corporal_calculada'] !== '' ? esc($historial['grasa_corporal_calculada']) : '' ?>">
                                <small class="text-muted">Se calcula a partir de los pliegues</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Masa Muscular (kg)</label>
                                <input type="number" name="masa_muscular" id="masa_muscular" class="form-control" step="0.01" min="0" placeholder="Ej: 45.2" value="<?= isset($historial['masa_muscular']) && $historial['masa_muscular'] !== '' ? esc($historial['masa_muscular']) : '' ?>">
                            </div>
                        </div>

                        <!-- Métodos de Cálculo de Composición Corporal -->
                        <?php if (!empty($metodos_calculo ?? [])): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-calculator me-2"></i> Métodos de Cálculo de Composición Corporal</h6>
                                <p class="text-muted small mb-3">Guardá primero las mediciones para poder calcular. Luego elegí el método según tu plan.</p>
                            </div>
                            <?php foreach ($metodos_calculo as $metodo): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 <?= $metodo['disponible'] ? 'border-success' : 'border-secondary opacity-75' ?>">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="fas fa-<?= $metodo['disponible'] ? 'check-circle text-success' : 'lock text-secondary' ?> me-2"></i>
                                            <?= esc($metodo['nombre']) ?>
                                        </h6>
                                        <p class="card-text text-muted small mb-2"><?= esc($metodo['descripcion'] ?? '') ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-<?= $metodo['disponible'] ? 'success' : 'secondary' ?>">
                                                <?= $metodo['componentes'] ?> Componentes
                                            </span>
                                            <?php if ($metodo['disponible']): ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-primary calcular-metodo"
                                                        data-metodo="<?= esc($metodo['slug']) ?>"
                                                        data-nombre="<?= esc($metodo['nombre']) ?>">
                                                    <i class="fas fa-calculator me-1"></i> Calcular
                                                </button>
                                            <?php else: ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary"
                                                        disabled
                                                        title="No disponible en tu plan actual">
                                                    <i class="fas fa-lock me-1"></i> No Disponible
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Información Clínica -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-stethoscope me-2"></i> Información Clínica</h6>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Anamnesis</label>
                                <textarea name="anamnesis" id="anamnesis" class="form-control" rows="4" placeholder="Historia clínica del paciente, antecedentes, síntomas..."><?= isset($historial['anamnesis']) ? esc($historial['anamnesis']) : '' ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Diagnóstico Nutricional</label>
                                <textarea name="diagnostico" id="diagnostico" class="form-control" rows="3" placeholder="Diagnóstico nutricional establecido..."><?= isset($historial['diagnostico']) ? esc($historial['diagnostico']) : '' ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Plan de Tratamiento</label>
                                <textarea name="plan_tratamiento" id="plan_tratamiento" class="form-control" rows="3" placeholder="Plan de tratamiento propuesto..."><?= isset($historial['plan_tratamiento']) ? esc($historial['plan_tratamiento']) : '' ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Tags <small class="text-muted">(Escriba y presione Enter o coma para agregar)</small></label>
                                <input type="text" name="tags" id="tags" class="form-control" 
                                       placeholder="Ej: diabetes, hipertensión, seguimiento, control"
                                       value="<?= esc($tags_string ?? '') ?>">
                                <small class="form-text text-muted">
                                    Los tags ayudan a categorizar y buscar consultas. Ejemplos: diabetes, hipertensión, seguimiento, control, etc.
                                </small>
                            </div>
                        </div>

                        <!-- Ficha de Ingreso: Anamnesis clínica (estructurada) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="fas fa-notes-medical me-2"></i> Anamnesis Clínica</h6>
                                <p class="text-muted small mb-3">Completar cada ítem según corresponda. Se guarda de forma ordenada por tema.</p>
                                <input type="hidden" name="anamnesis_clinica" id="anamnesis_clinica" value="">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Tabaco</label>
                                <textarea class="form-control form-control-sm" id="ac_tabaco" rows="2" placeholder="Ej: No fumador / 5 cig/día"><?= esc($ac['tabaco'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Alcohol</label>
                                <textarea class="form-control form-control-sm" id="ac_alcohol" rows="2" placeholder="Ej: Ocasional / No"><?= esc($ac['alcohol'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Drogas</label>
                                <textarea class="form-control form-control-sm" id="ac_drogas" rows="2" placeholder="Ej: No / Especificar"><?= esc($ac['drogas'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-0">Enfermedad de base / RCV</label>
                                <textarea class="form-control form-control-sm" id="ac_enfermedad_base" rows="2" placeholder="Ej: HTA, DM2, dislipidemia, ninguno"><?= esc($ac['enfermedad_base'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-0">Signos y síntomas</label>
                                <textarea class="form-control form-control-sm" id="ac_signos_sintomas" rows="2" placeholder="Ej: Cefaleas ocasionales, sin otros"><?= esc($ac['signos_sintomas'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Tránsito intestinal / Bristol / diuresis</label>
                                <textarea class="form-control form-control-sm" id="ac_transito_bristol" rows="2" placeholder="Ej: Regular, Bristol 3-4"><?= esc($ac['transito_bristol'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Medicamentos</label>
                                <textarea class="form-control form-control-sm" id="ac_medicamentos" rows="2" placeholder="Ej: Metformina 850 mg c/12 h"><?= esc($ac['medicamentos'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Suplementos</label>
                                <textarea class="form-control form-control-sm" id="ac_suplementos" rows="2" placeholder="Ej: Vit D, omega 3 / Ninguno"><?= esc($ac['suplementos'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Ingesta hídrica</label>
                                <textarea class="form-control form-control-sm" id="ac_ingesta_hidrica" rows="2" placeholder="Ej: 6-8 vasos/día"><?= esc($ac['ingesta_hidrica'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Actividad física</label>
                                <textarea class="form-control form-control-sm" id="ac_actividad_fisica" rows="2" placeholder="Ej: 3 veces/semana, caminata"><?= esc($ac['actividad_fisica'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Sueño</label>
                                <textarea class="form-control form-control-sm" id="ac_sueno" rows="2" placeholder="Ej: 6-7 h, insomnio ocasional"><?= esc($ac['sueno'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-0">Otros / notas</label>
                                <textarea class="form-control form-control-sm" id="ac_otros" rows="3" placeholder="Cualquier dato adicional de la anamnesis clínica"><?= esc($ac['otros'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Anamnesis alimentaria (estructurada) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Anamnesis Alimentaria</h6>
                                <p class="text-muted small mb-3">Relación familiar, quién cocina, apetito, relación con la comida, historia de peso y dietas.</p>
                                <input type="hidden" name="anamnesis_alimentaria" id="anamnesis_alimentaria" value="">
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Relación familiar / apoyo / recursos</label>
                                <textarea class="form-control form-control-sm" id="aa_relacion_familiar" rows="2" placeholder="Ej: Vive con pareja, apoyo en compras"><?= esc($aa['relacion_familiar'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Quién cocina</label>
                                <textarea class="form-control form-control-sm" id="aa_quien_cocina" rows="2" placeholder="Ej: La paciente / Pareja / Delivery"><?= esc($aa['quien_cocina'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Apetito</label>
                                <textarea class="form-control form-control-sm" id="aa_apetito" rows="2" placeholder="Ej: Bueno / Variable / Bajo"><?= esc($aa['apetito'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-0">Relación con la comida</label>
                                <textarea class="form-control form-control-sm" id="aa_relacion_comida" rows="2" placeholder="Ej: Come por ansiedad, sin restricciones emocionales"><?= esc($aa['relacion_comida'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Historia de dietas restrictivas</label>
                                <textarea class="form-control form-control-sm" id="aa_dieta_restrictiva" rows="2" placeholder="Ej: Múltiples intentos, dieta keto 2024"><?= esc($aa['dieta_restrictiva'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Historia de peso</label>
                                <textarea class="form-control form-control-sm" id="aa_historia_peso" rows="2" placeholder="Ej: Estable 5 años, sube en invierno"><?= esc($aa['historia_peso'] ?? '') ?></textarea>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-2">
                                <label class="form-label small mb-0">Ansiedad con/sin comida</label>
                                <textarea class="form-control form-control-sm" id="aa_ansiedad_comida" rows="2" placeholder="Ej: Ansiedad en las tardes, picoteo nocturno"><?= esc($aa['ansiedad_comida'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12 mb-2">
                                <label class="form-label small mb-0">Otros / notas</label>
                                <textarea class="form-control form-control-sm" id="aa_otros" rows="3" placeholder="Cualquier dato adicional de la anamnesis alimentaria"><?= esc($aa['otros'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <!-- Exámenes bioquímicos -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="fas fa-vial me-2"></i> Exámenes Bioquímicos</h6>
                                <p class="text-muted small">Nombre del examen, valor y fecha o interpretación.</p>
                                <input type="hidden" name="examenes_bioquimicos" id="examenes_bioquimicos_hidden" value="">
                                <div class="table-responsive mb-2">
                                    <table class="table table-sm table-bordered" id="tablaExamenesBioquimicos">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Valor</th>
                                                <th>Fecha / Interpretación</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyExamenesBioquimicos">
                                            <?php
                                            $examenes_bioquimicos = $examenes_bioquimicos ?? [];
                                            if (!empty($examenes_bioquimicos)):
                                                foreach ($examenes_bioquimicos as $ex):
                                                    $exNombre = is_object($ex) ? ($ex->nombre ?? '') : ($ex['nombre'] ?? '');
                                                    $exValor = is_object($ex) ? ($ex->valor ?? '') : ($ex['valor'] ?? '');
                                                    $exFecha = is_object($ex) ? ($ex->fecha_interpretacion ?? '') : ($ex['fecha_interpretacion'] ?? '');
                                            ?>
                                            <tr class="fila-examen">
                                                <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia" value="<?= esc($exNombre) ?>"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95" value="<?= esc($exValor) ?>"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal" value="<?= esc($exFecha) ?>"></td>
                                                <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                            </tr>
                                            <?php endforeach; endif; ?>
                                            <tr class="fila-examen">
                                                <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>
                                                <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>
                                                <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarExamen">
                                    <i class="fas fa-plus me-1"></i> Agregar examen
                                </button>
                            </div>
                        </div>

                        <!-- Tendencia de consumo: tabla por grupo (datos normalizados para procesar después) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="fas fa-apple-alt me-2"></i> Tendencia de Consumo / Preferencia alimentaria y Alergias</h6>
                                <p class="text-muted small mb-3">Completar preferencia y/o alergia/intolerancia por grupo. La información queda en tabla para consultas y reportes posteriores.</p>
                                <input type="hidden" name="tendencia_consumo" id="tendencia_consumo" value="">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="tablaTendenciaConsumo">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="align-middle">Tendencia de consumo</th>
                                                <th colspan="1" class="text-center bg-success bg-opacity-25">Preferencia alimentaria</th>
                                                <th colspan="1" class="text-center bg-danger bg-opacity-25">Alergias / Intolerancias</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyTendenciaConsumo">
                                            <?php
                                            $tendencia_grupos = $tendencia_grupos ?? \App\Models\HistorialTendenciaConsumo::getGrupos();
                                            foreach ($tendencia_grupos as $slug => $etiqueta):
                                                $tc = $tendencia_consumo[$slug] ?? null;
                                                $pref = $tc ? ($tc->preferencia ?? '') : '';
                                                $alerg = $tc ? ($tc->alergia_intolerancia ?? '') : '';
                                            ?>
                                            <tr data-grupo="<?= esc($slug) ?>">
                                                <td class="align-middle fw-medium"><?= esc($etiqueta) ?></td>
                                                <td class="p-1 bg-success bg-opacity-10">
                                                    <textarea class="form-control form-control-sm tc-preferencia" rows="2" placeholder="Ej: Alto / Bajo / 0" data-grupo="<?= esc($slug) ?>"><?= esc($pref) ?></textarea>
                                                </td>
                                                <td class="p-1 bg-danger bg-opacity-10">
                                                    <textarea class="form-control form-control-sm tc-alergia" rows="2" placeholder="Ej: Sí / No / 0" data-grupo="<?= esc($slug) ?>"><?= esc($alerg) ?></textarea>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Recordatorio 24 h -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-info mb-3"><i class="fas fa-clock me-2"></i> Recordatorio 24 h</h6>
                                <p class="text-muted small">Desayuno, colación, almuerzo, once, cena: horarios y contenido.</p>
                            </div>
                            <div class="col-12 mb-3">
                                <textarea name="recordatorio_24h" id="recordatorio_24h" class="form-control" rows="5" placeholder="Ej: Desayuno 08:00: café con leche, pan integral, palta. Colación 11:00: fruta. Almuerzo 14:00: ensalada, pollo, arroz. Once 18:00: té, galleta. Cena 21:00: sopa, huevo..."><?= isset($historial['recordatorio_24h']) ? esc($historial['recordatorio_24h']) : '' ?></textarea>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="limpiarFormularioMediciones()">
                                <i class="fas fa-eraser me-2"></i> Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Guardar Mediciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Formulario de Notas y Seguimiento -->
            <form id="formConsulta">
                <input type="hidden" name="detalle_agenda_id" value="<?= $cita->id ?>">
                
                <div class="section-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i> Notas de la Consulta</h5>
                    <p class="text-muted small">Anota todo lo que se habla durante la consulta. Puedes usar formato de texto (negrita, cursiva, subrayado, listas, etc.). Puedes guardar en cualquier momento.</p>
                    <textarea 
                        name="notas_consulta" 
                        id="notas_consulta" 
                        class="form-control" 
                        rows="8" 
                        placeholder="Ej: Paciente refiere mejoría en síntomas digestivos. Se ajustó plan alimentario eliminando lácteos. Se solicitó análisis de sangre para próxima visita..."
                    ><?= $cita->notas_consulta ?? '' ?></textarea>
                    <div class="mt-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="guardarNotasConsulta()">
                            <i class="fas fa-save me-2"></i> Guardar Notas
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="section-card">
                            <h5 class="text-success mb-3"><i class="fas fa-bullseye me-2"></i> Objetivos Establecidos</h5>
                            <p class="text-muted small mb-2">Define los objetivos que se establecieron durante la consulta. Puedes usar formato de texto.</p>
                            <textarea 
                                name="objetivos" 
                                id="objetivos" 
                                class="form-control" 
                                rows="5" 
                                placeholder="Ej: Reducir 5kg en 3 meses, Mejorar niveles de colesterol, Implementar rutina de ejercicios 3 veces por semana..."
                            ><?= $cita->objetivos ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-card">
                            <h5 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Plan de Alimentación</h5>
                            <p class="text-muted small mb-2">Describe el plan de alimentación acordado. Puedes usar formato de texto.</p>
                            <textarea 
                                name="plan_alimentacion" 
                                id="plan_alimentacion" 
                                class="form-control" 
                                rows="5" 
                                placeholder="Ej: Dieta mediterránea, 5 comidas al día, Eliminar azúcares refinados, Aumentar consumo de vegetales..."
                            ><?= $cita->plan_alimentacion ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Calorimetría y Plan Alimentario Estructurado -->
                <div class="section-card" style="border-left: 4px solid #9C27B0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-purple mb-0" style="color: #9C27B0;">
                            <i class="fas fa-calculator me-2"></i> Calorimetría y Plan Alimentario
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-purple" data-bs-toggle="collapse" data-bs-target="#calorimetriaPlanCollapse" aria-expanded="false" aria-controls="calorimetriaPlanCollapse" style="border-color: #9C27B0; color: #9C27B0;">
                            <i class="fas fa-chevron-down me-2"></i> Expandir
                        </button>
                    </div>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i> Calcula el gasto calórico y crea un plan alimentario estructurado con porciones e intercambios.
                    </p>
                    
                    <div class="collapse" id="calorimetriaPlanCollapse">
                        <!-- Pestañas para Calorimetría, Plan y Distribución -->
                        <ul class="nav nav-tabs mb-3" id="planAlimentarioTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="calorimetria-tab" data-bs-toggle="tab" data-bs-target="#calorimetria" type="button" role="tab" aria-controls="calorimetria" aria-selected="true">
                                    <i class="fas fa-fire me-2"></i> Calorimetría
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="plan-tab" data-bs-toggle="tab" data-bs-target="#plan" type="button" role="tab" aria-controls="plan" aria-selected="false">
                                    <i class="fas fa-clipboard-list me-2"></i> Plan Alimentario
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="distribucion-tab" data-bs-toggle="tab" data-bs-target="#distribucion" type="button" role="tab" aria-controls="distribucion" aria-selected="false">
                                    <i class="fas fa-utensils me-2"></i> Distribución por Comidas
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="planAlimentarioTabContent">
                            <!-- Tab: Calorimetría -->
                            <div class="tab-pane fade show active" id="calorimetria" role="tabpanel" aria-labelledby="calorimetria-tab">
                                <?= $this->include('Modulos/plan_alimentario/calorimetria') ?>
                            </div>

                            <!-- Tab: Plan Alimentario -->
                            <div class="tab-pane fade" id="plan" role="tabpanel" aria-labelledby="plan-tab">
                                <?= $this->include('Modulos/plan_alimentario/plan') ?>
                            </div>

                            <!-- Tab: Distribución por Comidas -->
                            <div class="tab-pane fade" id="distribucion" role="tabpanel" aria-labelledby="distribucion-tab">
                                <?= $this->include('Modulos/plan_alimentario/distribucion_comidas') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <h5 class="text-warning mb-3"><i class="fas fa-lightbulb me-2"></i> Recomendaciones</h5>
                    <p class="text-muted small mb-2">Agrega recomendaciones adicionales para el paciente. Puedes usar formato de texto.</p>
                    <textarea 
                        name="recomendaciones" 
                        id="recomendaciones" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Ej: Realizar ejercicio cardiovascular 30 min 3 veces por semana, Tomar suplemento de vitamina D, Agendar próxima cita en 1 mes..."
                    ><?= $cita->recomendaciones ?? '' ?></textarea>
                </div>

                <div class="section-card" style="border-left-color: #6f42c1 !important;">
                    <h5 class="mb-3" style="color: #6f42c1;"><i class="fas fa-calendar-check me-2"></i> Próxima Cita Recomendada</h5>
                    <p class="small text-muted mb-3">
                        <i class="fas fa-info-circle me-1"></i> Agendá una hora real para <strong><?= esc($cita->nombre . ' ' . $cita->apellido) ?></strong>; se mostrará aquí y se guarda en tu agenda.
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <input 
                            type="text" 
                            name="proxima_cita_recomendada" 
                            id="proxima_cita_recomendada" 
                            class="form-control" 
                            placeholder="Sin agendar — usá el botón para elegir horario"
                            style="max-width: 400px; font-size: 1.1rem; font-weight: 600; padding: 0.5rem 0.75rem;"
                            value="<?= $cita->proxima_cita_recomendada ? (strpos(trim($cita->proxima_cita_recomendada), ' ') !== false ? esc($cita->proxima_cita_recomendada) : date('d-m-Y', strtotime($cita->proxima_cita_recomendada))) : '' ?>"
                            readonly
                        >
                        <button type="button" class="btn btn-success flex-shrink-0" id="btnAgendarHoraReal" onclick="abrirModalAgendarHoraReal()">
                            <i class="fas fa-calendar-plus me-2"></i> Agendar hora real
                        </button>
                    </div>
                </div>

                <div class="section-card">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="guardarTodo()">
                                <i class="fas fa-save me-2"></i> Guardar Todo
                            </button>
                            <button type="button" class="btn btn-primary" onclick="guardarTodoYFinalizar()">
                                <i class="fas fa-check-double me-2"></i> Guardar y Finalizar Consulta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Agendar hora real (elegir slot en agenda) -->
<div class="modal fade" id="modalAgendarHoraReal" tabindex="-1" aria-labelledby="modalAgendarHoraRealLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalAgendarHoraRealLabel">
                    <i class="fas fa-calendar-plus me-2"></i> Agendar hora real en agenda
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Elegí una fecha y buscá horarios disponibles. Luego reservá el slot para <strong id="nombrePacienteAgendar"><?= esc($cita->nombre . ' ' . $cita->apellido) ?></strong>.</p>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label small">Fecha para buscar horarios</label>
                        <input type="text" id="modalAgendarFechaBuscar" class="form-control" placeholder="DD-MM-YYYY" readonly>
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" id="btnBuscarHorariosAgendar">
                            <i class="fas fa-search me-2"></i> Buscar horarios disponibles
                        </button>
                    </div>
                </div>
                <div id="slotsAgendarLoading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted small">Buscando horarios...</p>
                </div>
                <div id="slotsAgendarLista" class="mb-0" style="display: none;"></div>
                <div id="slotsAgendarVacio" class="alert alert-warning mb-0" style="display: none;">
                    <i class="fas fa-info-circle me-2"></i> No hay horarios disponibles en la fecha elegida. Probá otra fecha o creá horarios desde el calendario.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Terminar Consulta -->
<div class="modal fade" id="modalTerminarConsulta" tabindex="-1" aria-labelledby="modalTerminarConsultaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #FFA726; border-bottom: none;">
                <h5 class="modal-title" id="modalTerminarConsultaLabel">
                    <i class="fas fa-check-circle me-2"></i> Confirmar Terminación de Consulta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <div style="width: 80px; height: 80px; margin: 0 auto; background-color: #90A4AE; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                </div>
                <p class="text-center mb-2" style="font-size: 1.1rem; color: #333;">
                    <strong>¿Está seguro de que desea terminar la consulta?</strong>
                </p>
                <p class="text-muted text-center" style="font-size: 0.95rem;">
                    Esta acción no se puede deshacer. Se calculará la duración total de la consulta y se marcará como <span style="color: #90A4AE; font-weight: 600;">completada</span>.
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 1rem 1.5rem;">
                <button type="button" class="btn" style="background-color: #BDBDBD; color: white; border: none;" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" class="btn text-white" style="background-color: #90A4AE; border: none;" id="btnConfirmarTerminar">
                    <i class="fas fa-check-circle me-2"></i> Sí, Terminar Consulta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Configurar toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right"
};

// CSRF y binding de Calcular (JavaScript puro para no depender de jQuery)
var csrfNameCalc = '<?= esc(csrf_token()) ?>';
var csrfHashCalc = '<?= esc(csrf_hash()) ?>';
function setCsrfHashCalc(newHash) {
    if (!newHash) return;
    csrfHashCalc = newHash;
    var inp = document.querySelector('input[name="' + csrfNameCalc.replace(/"/g, '\\"') + '"]');
    if (inp) inp.value = csrfHashCalc;
}
document.addEventListener('click', function(e) {
    var btn = e.target && e.target.closest && e.target.closest('.calcular-metodo');
    if (!btn) return;
    var metodoSlug = btn.getAttribute('data-metodo');
    var metodoNombre = btn.getAttribute('data-nombre') || metodoSlug;
    var hid = document.getElementById('historial_id');
    var historialId = hid && hid.value ? parseInt(hid.value, 10) : 0;
    if (!historialId) {
        if (typeof toastr !== 'undefined') toastr.warning('Guardá primero las mediciones para poder calcular la composición corporal.', 'Guardar mediciones');
        return;
    }
    if (typeof validarUnidadesHolway5C === 'function' && metodoSlug === '5-componentes') {
        var issues = validarUnidadesHolway5C();
        if (issues.length && typeof mostrarModalUnidadesSospechosas === 'function') {
            mostrarModalUnidadesSospechosas(issues);
            return;
        }
    }
    var csrfInput = document.querySelector('input[name="' + csrfNameCalc.replace(/"/g, '\\"') + '"]');
    var csrfTokenActual = (csrfInput && csrfInput.value) || csrfHashCalc;
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Calculando...';
    var url = '<?= esc(base_url('dashboard/historial/calcular-')) ?>' + metodoSlug;
    var body = new FormData();
    body.append('historial_id', historialId);
    body.append(csrfNameCalc, csrfTokenActual);
    fetch(url, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfTokenActual },
        body: body
    }).then(function(r) {
        var headerToken = r.headers.get('X-CSRF-TOKEN');
        if (headerToken) setCsrfHashCalc(headerToken);
        return r.json().catch(function() { return {}; });
    }).then(function(response) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (response && response.csrf_hash) setCsrfHashCalc(response.csrf_hash);
        if (response && response.success && typeof window.mostrarResultadosCalculo === 'function') {
            window.mostrarResultadosCalculo(metodoNombre, response.resultado, response.metodo);
        } else if (response && response.requiere_upgrade && typeof window.mostrarErrorUpgrade === 'function') {
            window.mostrarErrorUpgrade(response);
        } else if (response && !response.success && typeof toastr !== 'undefined') {
            toastr.error(response.error || response.message || 'Error desconocido', 'Error');
        }
    }).catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (typeof window.mostrarModalErrorCalculo === 'function') {
            window.mostrarModalErrorCalculo({ error: 'Error al calcular', message: err && err.message ? err.message : 'Error de conexión', metodo: metodoSlug });
        } else if (typeof toastr !== 'undefined') {
            toastr.error('Error al calcular', 'Error');
        }
    });
});

var consultaIniciada = <?= $cita->fecha_inicio_real ? 'true' : 'false' ?>;
var fechaInicio = <?php 
    if ($cita->fecha_inicio_real) {
        // Asegurar que la fecha esté en formato correcto para JavaScript
        $fecha = $cita->fecha_inicio_real;
        // Si viene como string DATETIME de MySQL, ya está en formato YYYY-MM-DD HH:MM:SS
        // Si viene como objeto DateTime, convertir a string
        if (is_object($fecha)) {
            $fecha = $fecha->format('Y-m-d H:i:s');
        } elseif (is_string($fecha)) {
            // Verificar si ya está en formato correcto
            if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fecha)) {
                // Intentar convertir si está en otro formato
                $timestamp = strtotime($fecha);
                if ($timestamp !== false) {
                    $fecha = date('Y-m-d H:i:s', $timestamp);
                }
            }
        }
        echo "'" . $fecha . "'";
    } else {
        echo 'null';
    }
?>;
var timerInterval = null;

// Función para obtener token CSRF
function obtenerTokenCSRF() {
    var name = 'csrf_cookie_name';
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf(name + '=') === 0) {
            return cookie.substring(name.length + 1);
        }
    }
    return null;
}

// Función para actualizar token CSRF después de peticiones AJAX
function actualizarTokenCSRF(xhr) {
    var newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
    if (newToken) {
        $('meta[name="csrf-token"]').attr('content', newToken);
        $('input[name="csrf_test_name"]').val(newToken);
    } else if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
        $('meta[name="csrf-token"]').attr('content', xhr.responseJSON.csrf_token);
        $('input[name="csrf_test_name"]').val(xhr.responseJSON.csrf_token);
    }
}

// Timer para consulta en curso
function iniciarTimer() {
    if (!fechaInicio) {
        console.error('No se puede iniciar el timer: fechaInicio es null');
        return;
    }
    
    // Limpiar timer anterior si existe
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    // Convertir fecha a formato ISO para JavaScript
    var fechaISO = fechaInicio.replace(' ', 'T');
    var inicio = new Date(fechaISO);
    
    // Verificar que la fecha sea válida
    if (isNaN(inicio.getTime())) {
        console.error('Fecha de inicio inválida:', fechaInicio);
        return;
    }
    
    // Actualizar inmediatamente
    function actualizarTimer() {
        var ahora = new Date();
        var diff = ahora - inicio;
        
        // Si la diferencia es negativa, la fecha de inicio es futura (no debería pasar)
        if (diff < 0) {
            console.warn('La fecha de inicio es futura, usando fecha actual');
            inicio = ahora;
            diff = 0;
        }
        
        var horas = Math.floor(diff / (1000 * 60 * 60));
        var minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var segundos = Math.floor((diff % (1000 * 60)) / 1000);
        
        var tiempo = String(horas).padStart(2, '0') + ':' + 
                     String(minutos).padStart(2, '0') + ':' + 
                     String(segundos).padStart(2, '0');
        
        var $timerDisplay = $('#timerDisplay');
        if ($timerDisplay.length) {
            $timerDisplay.text(tiempo);
        } else if (consultaIniciada) {
            // Solo advertir si hay consulta en curso y el elemento debería existir
            console.warn('Elemento #timerDisplay no encontrado');
        }
    }
    
    // Actualizar inmediatamente
    actualizarTimer();
    
    // Actualizar cada segundo
    timerInterval = setInterval(actualizarTimer, 1000);
}

// Iniciar consulta
function iniciarConsulta(detalleAgendaId) {
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/iniciarConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            detalle_agenda_id: detalleAgendaId,
            [csrfName]: csrfToken
        },
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Consulta iniciada', 'Éxito');
                consultaIniciada = true;
                fechaInicio = response.fecha_inicio;
                location.reload(); // Recargar para mostrar timer
            } else {
                toastr.error(response.error || 'Error al iniciar la consulta', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var errorMsg = 'Error al iniciar la consulta';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Variable global para almacenar el ID de la consulta a terminar
var detalleAgendaIdParaTerminar = null;

// Terminar consulta - Abre el modal de confirmación
function terminarConsulta(detalleAgendaId) {
    detalleAgendaIdParaTerminar = detalleAgendaId;
    var modal = new bootstrap.Modal(document.getElementById('modalTerminarConsulta'));
    modal.show();
}

// Confirmar terminación de consulta (se ejecuta desde el modal)
function confirmarTerminarConsulta() {
    if (!detalleAgendaIdParaTerminar) {
        return;
    }
    
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Deshabilitar botón mientras se procesa
    var $btnConfirmar = $('#btnConfirmarTerminar');
    var textoOriginal = $btnConfirmar.html();
    $btnConfirmar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Procesando...');
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/terminarConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            detalle_agenda_id: detalleAgendaIdParaTerminar,
            [csrfName]: csrfToken
        },
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                // Cerrar modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalTerminarConsulta'));
                modal.hide();
                
                toastr.success('Consulta finalizada. Duración: ' + response.duracion_minutos + ' minutos', 'Éxito', {
                    timeOut: 3000
                });
                
                if (timerInterval) {
                    clearInterval(timerInterval);
                }
                
                // Recargar después de un breve delay
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                $btnConfirmar.prop('disabled', false).html(textoOriginal);
                toastr.error(response.error || 'Error al terminar la consulta', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            $btnConfirmar.prop('disabled', false).html(textoOriginal);
            
            var errorMsg = 'Error al terminar la consulta';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Guardar notas de consulta
function guardarNotasConsulta() {
    // Token desde input del formulario (actualizado tras agendar/guardar mediciones) para evitar 403
    var csrfToken = $('input[name="csrf_test_name"]').val() || obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Obtener el contenido HTML del editor TinyMCE
    var notasHTML = '';
    if (tinymce.get('notas_consulta')) {
        notasHTML = tinymce.get('notas_consulta').getContent();
    } else {
        notasHTML = $('#notas_consulta').val();
    }
    
    // Obtener el contenido HTML de todos los editores TinyMCE
    var objetivosHTML = '';
    var planHTML = '';
    var recomendacionesHTML = '';
    
    if (tinymce.get('objetivos')) {
        objetivosHTML = tinymce.get('objetivos').getContent();
    } else {
        objetivosHTML = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        planHTML = tinymce.get('plan_alimentacion').getContent();
    } else {
        planHTML = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        recomendacionesHTML = tinymce.get('recomendaciones').getContent();
    } else {
        recomendacionesHTML = $('#recomendaciones').val();
    }
    
    // Obtener tags del campo Tagify
    var tagsValue = '';
    try {
        // Usar la variable global tagifyInstance
        if (typeof tagifyInstance !== 'undefined' && tagifyInstance !== null) {
            var tagsData = tagifyInstance.value;
            if (tagsData && Array.isArray(tagsData) && tagsData.length > 0) {
                // Extraer solo los valores como strings simples
                tagsValue = tagsData.map(function(item) {
                    if (typeof item === 'string') {
                        return item;
                    } else if (item && typeof item === 'object') {
                        // Si es un objeto, extraer el valor
                        return item.value || item.tag || String(item);
                    }
                    return String(item);
                }).filter(function(tag) {
                    // Filtrar tags vacíos
                    return tag && tag.trim() !== '';
                }).join(',');
            }
        } else {
            // Fallback: obtener valor directamente del input
            tagsValue = $('#tags').val() || '';
        }
    } catch(e) {
        console.error('Error al obtener tags de Tagify:', e);
        // Fallback si hay algún error
        tagsValue = $('#tags').val() || '';
    }
    
    var formData = {
        detalle_agenda_id: $('input[name="detalle_agenda_id"]').val(),
        notas_consulta: notasHTML,
        objetivos: objetivosHTML,
        plan_alimentacion: planHTML,
        recomendaciones: recomendacionesHTML,
        proxima_cita_recomendada: $('#proxima_cita_recomendada').val(),
        tags: tagsValue,
        [csrfName]: csrfToken
    };
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/guardarNotasConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Información guardada correctamente', 'Éxito', {
                    timeOut: 2000
                });
            } else {
                toastr.error(response.error || 'Error al guardar', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var errorMsg = 'Error al guardar la información';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Guardar todo
function guardarTodo() {
    guardarNotasConsulta();
}

// Guardar y finalizar
function guardarTodoYFinalizar() {
    guardarNotasConsulta();
    setTimeout(function() {
        if (consultaIniciada && !<?= $cita->fecha_fin_real ? 'true' : 'false' ?>) {
            terminarConsulta(<?= $cita->id ?>);
        } else {
            toastr.info('La consulta ya está finalizada o no ha sido iniciada', 'Información');
        }
    }, 500);
}

// ============================================
// FUNCIONES PARA MEDICIONES CORPORALES
// ============================================

<?php if (!empty($historial)): ?>
// Cargar datos existentes del historial
$(document).ready(function() {
    var historial = <?= json_encode($historial) ?>;
    
    if (historial) {
        $('#historial_id').val(historial.id);
        $('#peso_actual').val(historial.peso_actual || '');
        $('#altura_actual').val(historial.altura_actual || '');
        $('#altura_sentado').val(historial.altura_sentado || '');
        $('#imc_actual').val(historial.imc_actual || '');
        $('#circunferencia_cintura').val(historial.circunferencia_cintura || '');
        $('#circunferencia_cadera').val(historial.circunferencia_cadera || '');
        $('#circunferencia_brazo_relajado').val(historial.circunferencia_brazo_relajado || '');
        $('#circunferencia_brazo_contraido').val(historial.circunferencia_brazo_contraido || '');
        $('#circunferencia_muslo_medio').val(historial.circunferencia_muslo_medio || '');
        $('#circunferencia_pantorrilla').val(historial.circunferencia_pantorrilla || '');
        $('#circunferencia_cuello').val(historial.circunferencia_cuello || '');
        $('#circunferencia_torax').val(historial.circunferencia_torax || '');
        $('#circunferencia_cabeza').val(historial.circunferencia_cabeza || '');
        $('#circunferencia_antebrazo_maximo').val(historial.circunferencia_antebrazo_maximo || '');
        $('#circunferencia_muslo_maximo').val(historial.circunferencia_muslo_maximo || '');
        $('#circunferencia_muneca').val(historial.circunferencia_muneca || '');
        $('#diametro_biacromial').val(historial.diametro_biacromial || '');
        $('#diametro_bi_iliocristal').val(historial.diametro_bi_iliocristal || '');
        $('#diametro_torax_transverso').val(historial.diametro_torax_transverso || '');
        $('#diametro_torax_anteroposterior').val(historial.diametro_torax_anteroposterior || '');
        $('#diametro_humero').val(historial.diametro_humero || '');
        $('#diametro_femur').val(historial.diametro_femur || '');
        $('#diametro_muneca').val(historial.diametro_muneca || '');
        $('#diametro_tobillo').val(historial.diametro_tobillo || '');
        $('#grasa_corporal').val(historial.grasa_corporal || '');
        $('#masa_muscular').val(historial.masa_muscular || '');
        $('#pliegue_tricipital').val(historial.pliegue_tricipital || '');
        $('#pliegue_bicipital').val(historial.pliegue_bicipital || '');
        $('#pliegue_subescapular').val(historial.pliegue_subescapular || '');
        $('#pliegue_suprailíaco').val(historial.pliegue_suprailíaco || '');
        $('#pliegue_supraespinal').val(historial.pliegue_supraespinal || '');
        $('#pliegue_abdominal').val(historial.pliegue_abdominal || '');
        $('#pliegue_muslo_anterior').val(historial.pliegue_muslo_anterior || '');
        $('#pliegue_pantorrilla_medial').val(historial.pliegue_pantorrilla_medial || '');
        $('#pliegue_pectoral').val(historial.pliegue_pectoral || '');
        $('#pliegue_axilar_medio').val(historial.pliegue_axilar_medio || '');
        $('#pliegue_muslo_medial').val(historial.pliegue_muslo_medial || '');
        $('#suma_pliegues').val(historial.suma_pliegues || '');
        $('#grasa_corporal_calculada').val(historial.grasa_corporal_calculada || '');
        $('#anamnesis').val(historial.anamnesis || '');
        $('#diagnostico').val(historial.diagnostico || '');
        $('#plan_tratamiento').val(historial.plan_tratamiento || '');
        // Anamnesis clínica estructurada: si viene en JSON, rellenar cada campo; si es texto legacy, poner en Otros
        var acRaw = historial.anamnesis_clinica || '';
        if (acRaw) {
            try {
                var ac = JSON.parse(acRaw);
                if (ac && typeof ac === 'object') {
                    $('#ac_tabaco').val(ac.tabaco || '');
                    $('#ac_alcohol').val(ac.alcohol || '');
                    $('#ac_drogas').val(ac.drogas || '');
                    $('#ac_enfermedad_base').val(ac.enfermedad_base || '');
                    $('#ac_signos_sintomas').val(ac.signos_sintomas || '');
                    $('#ac_transito_bristol').val(ac.transito_bristol || '');
                    $('#ac_medicamentos').val(ac.medicamentos || '');
                    $('#ac_suplementos').val(ac.suplementos || '');
                    $('#ac_ingesta_hidrica').val(ac.ingesta_hidrica || '');
                    $('#ac_actividad_fisica').val(ac.actividad_fisica || '');
                    $('#ac_sueno').val(ac.sueno || '');
                    $('#ac_otros').val(ac.otros || '');
                } else {
                    $('#ac_otros').val(acRaw);
                }
            } catch (e) {
                $('#ac_otros').val(acRaw);
            }
        }
        // Anamnesis alimentaria estructurada
        var aaRaw = historial.anamnesis_alimentaria || '';
        if (aaRaw) {
            try {
                var aa = JSON.parse(aaRaw);
                if (aa && typeof aa === 'object') {
                    $('#aa_relacion_familiar').val(aa.relacion_familiar || '');
                    $('#aa_quien_cocina').val(aa.quien_cocina || '');
                    $('#aa_apetito').val(aa.apetito || '');
                    $('#aa_relacion_comida').val(aa.relacion_comida || '');
                    $('#aa_dieta_restrictiva').val(aa.dieta_restrictiva || '');
                    $('#aa_historia_peso').val(aa.historia_peso || '');
                    $('#aa_ansiedad_comida').val(aa.ansiedad_comida || '');
                    $('#aa_otros').val(aa.otros || '');
                } else {
                    $('#aa_otros').val(aaRaw);
                }
            } catch (e) {
                $('#aa_otros').val(aaRaw);
            }
        }
        // Tendencia de consumo se carga desde PHP en la tabla (tbodyTendenciaConsumo)
        $('#recordatorio_24h').val(historial.recordatorio_24h || '');
        
        // Tabla de exámenes bioquímicos
        var examenesBioquimicos = <?= json_encode($examenes_bioquimicos ?? []) ?>;
        var tbodyExamenes = $('#tbodyExamenesBioquimicos');
        tbodyExamenes.empty();
        if (examenesBioquimicos && examenesBioquimicos.length > 0) {
            examenesBioquimicos.forEach(function(ex) {
                tbodyExamenes.append(
                    '<tr class="fila-examen">' +
                    '<td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia" value="' + (ex.nombre || '').replace(/"/g, '&quot;') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95" value="' + (ex.valor || '').replace(/"/g, '&quot;') + '"></td>' +
                    '<td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal" value="' + (ex.fecha_interpretacion || '').replace(/"/g, '&quot;') + '"></td>' +
                    '<td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>' +
                    '</tr>'
                );
            });
        }
        tbodyExamenes.append(
            '<tr class="fila-examen">' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>' +
            '</tr>'
        );
        
        // La sección permanece colapsada por defecto (no se abre automáticamente)
    }
});
<?php endif; ?>

// Variable global para la instancia de Tagify
var tagifyInstance = null;

// Inicializar Tagify para tags con autocompletado
$(document).ready(function() {
    var input = document.querySelector('#tags');
    if (input) {
        var tagsSugeridos = <?= json_encode(array_column($tags_sugeridos ?? [], 'tag_display')) ?>;
        
        // Preparar tags para cargar después de la inicialización
        <?php 
        $tagsParaTagify = [];
        if (!empty($cita->tags)) {
            $tagsDecodificados = json_decode($cita->tags, true);
            if (is_array($tagsDecodificados) && !empty($tagsDecodificados)) {
                // Asegurarse de que todos los tags sean strings simples
                foreach ($tagsDecodificados as $tag) {
                    if (is_string($tag)) {
                        $tagsParaTagify[] = $tag;
                    } elseif (is_array($tag) && isset($tag['value'])) {
                        $tagsParaTagify[] = $tag['value'];
                    } elseif (is_array($tag) && isset($tag['tag'])) {
                        $tagsParaTagify[] = $tag['tag'];
                    }
                }
            }
        }
        $tagsJson = !empty($tagsParaTagify) ? json_encode($tagsParaTagify) : '[]';
        ?>
        
        tagifyInstance = new Tagify(input, {
            whitelist: tagsSugeridos,
            maxTags: 10,
            dropdown: {
                maxItems: 20,
                classname: 'tags-look',
                enabled: 1,
                closeOnSelect: false
            }
        });

        // Cargar tags desde detalle_agenda después de que Tagify esté inicializado
        var tagsParaCargar = <?= $tagsJson ?>;
        if (Array.isArray(tagsParaCargar) && tagsParaCargar.length > 0) {
            try {
                // Limpiar cualquier valor previo y agregar los tags correctamente
                tagifyInstance.removeAllTags();
                // Agregar tags como strings simples
                tagifyInstance.addTags(tagsParaCargar.map(function(tag) {
                    return typeof tag === 'string' ? tag : String(tag);
                }));
            } catch(e) {
                console.error('Error al cargar tags en Tagify:', e);
            }
        }

        // Cargar tags sugeridos dinámicamente
        tagifyInstance.on('input', function(e) {
            var value = e.detail.value;
            if (value.length < 1) return;
            
            $.ajax({
                url: '<?= base_url('dashboard/historial/getTagsSugeridos') ?>',
                dataType: 'json',
                data: { q: value },
                success: function(data) {
                    var whitelist = data.results.map(function(item) {
                        return item.text;
                    });
                    tagifyInstance.settings.whitelist = whitelist;
                    tagifyInstance.dropdown.show.call(tagifyInstance, value);
                }
            });
        });
    }
});

// Calcular IMC automáticamente
function calcularIMC() {
    var peso = parseFloat($('#peso_actual').val());
    var altura = parseFloat($('#altura_actual').val());
    
    if (peso > 0 && altura > 0) {
        var alturaMetros = altura / 100;
        var imc = peso / (alturaMetros * alturaMetros);
        $('#imc_actual').val(imc.toFixed(2));
    } else {
        $('#imc_actual').val('');
    }
}

// Calcular suma de pliegues automáticamente
function calcularSumaPliegues() {
    var pliegues = [
        'pliegue_tricipital',
        'pliegue_bicipital',
        'pliegue_subescapular',
        'pliegue_suprailíaco',
        'pliegue_supraespinal',
        'pliegue_abdominal',
        'pliegue_muslo_anterior',
        'pliegue_pantorrilla_medial',
        'pliegue_pectoral',
        'pliegue_axilar_medio',
        'pliegue_muslo_medial'
    ];
    
    var suma = 0;
    pliegues.forEach(function(pliegue) {
        var valor = parseFloat($('#' + pliegue).val());
        if (!isNaN(valor) && valor > 0) {
            suma += valor;
        }
    });
    
    if (suma > 0) {
        $('#suma_pliegues').val(suma.toFixed(2));
        
        // Calcular grasa corporal aproximada usando fórmula de Durnin-Womersley simplificada
        // Fórmula: %GC = (4.95 / D) - 4.5, donde D = densidad corporal
        // Para simplificar, usamos: %GC = (suma_pliegues * factor) + constante
        // Factor y constante varían según género y edad, pero usamos valores promedio
        var peso = parseFloat($('#peso_actual').val());
        var altura = parseFloat($('#altura_actual').val());
        
        if (peso > 0 && altura > 0) {
            // Fórmula mejorada basada en suma de pliegues y datos corporales
            // Usando aproximación de Jackson-Pollock (7 pliegues)
            var densidad = 1.112 - (0.00043499 * suma) + (0.00000055 * suma * suma) - (0.00028826 * altura);
            var grasaCalculada = ((4.95 / densidad) - 4.5) * 100;
            
            // Validar que el resultado sea razonable (entre 5% y 50%)
            if (grasaCalculada >= 5 && grasaCalculada <= 50) {
                $('#grasa_corporal_calculada').val(grasaCalculada.toFixed(2));
            } else {
                // Si el resultado no es razonable, usar fórmula simplificada
                var grasaSimplificada = (suma * 0.5) + 5;
                $('#grasa_corporal_calculada').val(grasaSimplificada.toFixed(2));
            }
        } else if (suma > 0) {
            // Si no hay peso/altura, usar fórmula simplificada basada solo en pliegues
            var grasaSimplificada = (suma * 0.5) + 5;
            $('#grasa_corporal_calculada').val(grasaSimplificada.toFixed(2));
        } else {
            $('#grasa_corporal_calculada').val('');
        }
    } else {
        $('#suma_pliegues').val('');
        $('#grasa_corporal_calculada').val('');
    }
}

// Event listeners para cálculos automáticos
$(document).ready(function() {
    $('#peso_actual, #altura_actual').on('input', calcularIMC);
    
    // Agregar listeners a todos los campos de pliegues por ID
    $('#pliegue_tricipital, #pliegue_bicipital, #pliegue_subescapular, #pliegue_suprailíaco, #pliegue_supraespinal, #pliegue_abdominal, #pliegue_muslo_anterior, #pliegue_pantorrilla_medial, #pliegue_pectoral, #pliegue_axilar_medio, #pliegue_muslo_medial').on('input', function() {
        calcularSumaPliegues();
    });
    
    // También recalcular cuando cambia el peso (necesario para grasa calculada)
    $('#peso_actual').on('input', function() {
        calcularSumaPliegues();
    });
});

// Guardar mediciones
function guardarMediciones(event) {
    event.preventDefault();
    
    // Sincronizar token CSRF con la cookie (CI4 usa cookie; el token del form puede estar desactualizado)
    var tokenFromCookie = obtenerTokenCSRF();
    if (tokenFromCookie) {
        $('input[name="csrf_test_name"]').val(tokenFromCookie);
        if ($('meta[name="csrf-token"]').length) {
            $('meta[name="csrf-token"]').attr('content', tokenFromCookie);
        }
    }
    var csrfToken = $('input[name="csrf_test_name"]').val() || tokenFromCookie || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Obtener tags de Tagify correctamente (como string separado por comas)
    var tagsValue = '';
    try {
        if (typeof tagifyInstance !== 'undefined' && tagifyInstance !== null) {
            var tagsData = tagifyInstance.value;
            if (tagsData && Array.isArray(tagsData) && tagsData.length > 0) {
                // Extraer solo los valores como strings simples
                tagsValue = tagsData.map(function(item) {
                    if (typeof item === 'string') {
                        return item;
                    } else if (item && typeof item === 'object') {
                        return item.value || item.tag || String(item);
                    }
                    return String(item);
                }).filter(function(tag) {
                    return tag && tag.trim() !== '';
                }).join(',');
            }
        } else {
            // Fallback: obtener valor directamente del input
            tagsValue = $('#tags').val() || '';
        }
    } catch(e) {
        console.error('Error al obtener tags de Tagify:', e);
        tagsValue = $('#tags').val() || '';
    }
    
    // Anamnesis clínica estructurada: armar objeto y guardar como JSON en el hidden
    var acObj = {
        tabaco: $('#ac_tabaco').val() || '',
        alcohol: $('#ac_alcohol').val() || '',
        drogas: $('#ac_drogas').val() || '',
        enfermedad_base: $('#ac_enfermedad_base').val() || '',
        signos_sintomas: $('#ac_signos_sintomas').val() || '',
        transito_bristol: $('#ac_transito_bristol').val() || '',
        medicamentos: $('#ac_medicamentos').val() || '',
        suplementos: $('#ac_suplementos').val() || '',
        ingesta_hidrica: $('#ac_ingesta_hidrica').val() || '',
        actividad_fisica: $('#ac_actividad_fisica').val() || '',
        sueno: $('#ac_sueno').val() || '',
        otros: $('#ac_otros').val() || ''
    };
    $('#anamnesis_clinica').val(JSON.stringify(acObj));
    
    // Anamnesis alimentaria estructurada
    var aaObj = {
        relacion_familiar: $('#aa_relacion_familiar').val() || '',
        quien_cocina: $('#aa_quien_cocina').val() || '',
        apetito: $('#aa_apetito').val() || '',
        relacion_comida: $('#aa_relacion_comida').val() || '',
        dieta_restrictiva: $('#aa_dieta_restrictiva').val() || '',
        historia_peso: $('#aa_historia_peso').val() || '',
        ansiedad_comida: $('#aa_ansiedad_comida').val() || '',
        otros: $('#aa_otros').val() || ''
    };
    $('#anamnesis_alimentaria').val(JSON.stringify(aaObj));
    
    // Tendencia de consumo: tabla normalizada (grupo, preferencia, alergia_intolerancia)
    var tendenciaRows = [];
    $('#tbodyTendenciaConsumo tr[data-grupo]').each(function() {
        var grupo = $(this).data('grupo');
        var preferencia = $(this).find('.tc-preferencia').val() || '';
        var alergia = $(this).find('.tc-alergia').val() || '';
        tendenciaRows.push({ grupo: grupo, preferencia: preferencia, alergia_intolerancia: alergia });
    });
    $('#tendencia_consumo').val(JSON.stringify(tendenciaRows));
    
    // Serializar tabla de exámenes bioquímicos a JSON y poner en el hidden
    var filasExamenes = [];
    $('#tbodyExamenesBioquimicos tr.fila-examen').each(function() {
        var $tr = $(this);
        var nombre = $tr.find('input[name="examen_nombre[]"]').val() || '';
        var valor = $tr.find('input[name="examen_valor[]"]').val() || '';
        var fecha = $tr.find('input[name="examen_fecha[]"]').val() || '';
        filasExamenes.push({ nombre: nombre, valor: valor, fecha_interpretacion: fecha });
    });
    $('#examenes_bioquimicos_hidden').val(JSON.stringify(filasExamenes));
    
    // Serializar el formulario y reemplazar el campo tags con el valor correcto
    var formData = $('#formMediciones').serialize();
    
    // Remover el campo tags si existe en el formData serializado
    formData = formData.replace(/&?tags=[^&]*/g, '');
    
    // Agregar el campo tags con el valor correcto
    if (tagsValue) {
        formData += '&tags=' + encodeURIComponent(tagsValue);
    }
    
    // Asegurar que el token CSRF esté en el body (CI4 lo valida también por POST)
    formData = formData.replace(/&?csrf_test_name=[^&]*/g, '');
    formData += (formData ? '&' : '') + 'csrf_test_name=' + encodeURIComponent(csrfToken);
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/guardarMediciones') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Mediciones guardadas correctamente', 'Éxito', {
                    timeOut: 3000
                });
                
                // Actualizar historial_id si es nuevo registro
                if (response.historial_id && !$('#historial_id').val()) {
                    $('#historial_id').val(response.historial_id);
                }
            } else {
                toastr.error(response.error || 'Error al guardar', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var errorMsg = 'Error al guardar las mediciones';
            if (xhr.status === 403) {
                errorMsg = 'Sesión o token de seguridad expirado. Actualizá el token e intentá guardar de nuevo.';
                // Actualizar token desde cookie por si la respuesta no lo trajo
                var nuevoToken = obtenerTokenCSRF();
                if (nuevoToken) {
                    $('input[name="csrf_test_name"]').val(nuevoToken);
                    $('meta[name="csrf-token"]').attr('content', nuevoToken);
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Agregar fila a la tabla de exámenes bioquímicos
$(document).on('click', '#btnAgregarExamen', function() {
    var fila = '<tr class="fila-examen">' +
        '<td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>' +
        '<td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>' +
        '<td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>' +
        '<td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>' +
        '</tr>';
    $('#tbodyExamenesBioquimicos').append(fila);
});

// Quitar fila de la tabla de exámenes bioquímicos (dejar al menos una)
$(document).on('click', '.btn-quitar-examen', function() {
    var $tbody = $('#tbodyExamenesBioquimicos');
    if ($tbody.find('tr.fila-examen').length > 1) {
        $(this).closest('tr').remove();
    }
});

// Limpiar formulario de mediciones
function limpiarFormularioMediciones() {
    if (confirm('¿Está seguro de limpiar todos los campos de mediciones?')) {
        $('#formMediciones')[0].reset();
        $('#historial_id').val('');
        $('#imc_actual').val('');
        $('#suma_pliegues').val('');
        $('#grasa_corporal_calculada').val('');
        $('#anamnesis_clinica').val('');
        $('#ac_tabaco, #ac_alcohol, #ac_drogas, #ac_enfermedad_base, #ac_signos_sintomas, #ac_transito_bristol, #ac_medicamentos, #ac_suplementos, #ac_ingesta_hidrica, #ac_actividad_fisica, #ac_sueno, #ac_otros').val('');
        $('#anamnesis_alimentaria').val('');
        $('#aa_relacion_familiar, #aa_quien_cocina, #aa_apetito, #aa_relacion_comida, #aa_dieta_restrictiva, #aa_historia_peso, #aa_ansiedad_comida, #aa_otros').val('');
        $('#tendencia_consumo').val('');
        $('#tbodyTendenciaConsumo .tc-preferencia, #tbodyTendenciaConsumo .tc-alergia').val('');
        $('#recordatorio_24h').val('');
        $('#examenes_bioquimicos_hidden').val('');
        // Restaurar una sola fila vacía en la tabla de exámenes
        var tbodyExamenes = $('#tbodyExamenesBioquimicos');
        tbodyExamenes.empty();
        tbodyExamenes.append(
            '<tr class="fila-examen">' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>' +
            '<td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>' +
            '<td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>' +
            '</tr>'
        );
    }
}

// ============================================
// CÁLCULO DE COMPOSICIÓN CORPORAL (helpers; el binding está al inicio del script)
// ============================================

function mostrarModalErrorCalculo(payload) {
    var titulo = (payload && payload.error) ? payload.error : 'Error al calcular';
    var mensaje = (payload && payload.message) ? payload.message : 'Ocurrió un error inesperado.';
    var metodo = (payload && payload.metodo) ? payload.metodo : null;
    var warnings = (payload && Array.isArray(payload.warnings)) ? payload.warnings : [];
    var modalHtml = '<div class="modal fade" id="modalErrorCalculo" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-danger text-white"><h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>' + titulo + '</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body">' + (metodo ? '<div class="small text-muted mb-2">Método: <strong>' + metodo + '</strong></div>' : '') + (warnings.length ? '<div class="alert alert-warning border small"><strong>Revisar:</strong><ul class="mb-0">' + warnings.map(function(w){ return '<li>' + w + '</li>'; }).join('') + '</ul></div>' : '') + '<div class="alert alert-light border"><div class="fw-semibold mb-1">Detalle</div><pre class="mb-0" style="white-space: pre-wrap; word-break: break-word;">' + mensaje + '</pre></div><div class="small text-muted">Si el error menciona perímetros/diámetros "muy bajos", revisá que estén cargados en <strong>cm</strong> (no pulgadas) y que los pliegues estén en <strong>mm</strong>.</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div></div></div></div>';
    $('#modalErrorCalculo').remove();
    $('body').append(modalHtml);
    (new bootstrap.Modal(document.getElementById('modalErrorCalculo'))).show();
}

function validarUnidadesHolway5C() {
    function numByName(name) {
        var v = $('*[name="' + name + '"]').val();
        if (v === undefined || v === null || v === '') return null;
        var n = parseFloat(String(v).replace(',', '.'));
        return isNaN(n) ? null : n;
    }
    var issues = [];
    function pushIf(name, val, cond, hint) {
        if (val == null) return;
        if (cond) issues.push({ campo: name, valor: val, hint: hint });
    }
    var PBR = numByName('circunferencia_brazo_relajado');
    var PAM = numByName('circunferencia_antebrazo_maximo');
    var PMUS = numByName('circunferencia_muslo_maximo');
    var PPAN = numByName('circunferencia_pantorrilla');
    var PTX = numByName('circunferencia_torax');
    pushIf('circunferencia_brazo_relajado', PBR, PBR > 0 && PBR < 18, 'Brazo relajado en cm suele ser > 18. ¿Pulgadas?');
    pushIf('circunferencia_antebrazo_maximo', PAM, PAM > 0 && PAM < 18, 'Antebrazo en cm suele ser > 18. ¿Pulgadas?');
    pushIf('circunferencia_muslo_maximo', PMUS, PMUS > 0 && PMUS < 30, 'Muslo máximo en cm suele ser > 30. ¿Pulgadas?');
    pushIf('circunferencia_pantorrilla', PPAN, PPAN > 0 && PPAN < 25, 'Pantorrilla en cm suele ser > 25. ¿Pulgadas?');
    pushIf('circunferencia_torax', PTX, PTX > 0 && PTX < 60, 'Tórax en cm suele ser > 60. ¿Pulgadas?');
    ['pliegue_tricipital','pliegue_subescapular','pliegue_supraespinal','pliegue_abdominal','pliegue_muslo_medial','pliegue_pantorrilla_medial'].forEach(function(nm){
        var v = numByName(nm);
        pushIf(nm, v, v > 0 && v < 1, 'Pliegues se ingresan en mm (no cm).');
    });
    return issues;
}

var etiquetasCamposComposicion = {
    peso_actual: 'Peso (kg)', altura_actual: 'Talla (cm)', altura_sentado: 'Altura sentado (cm)',
    pliegue_tricipital: 'Pliegue tríceps (mm)', pliegue_subescapular: 'Pliegue subescapular (mm)', pliegue_suprailíaco: 'Pliegue suprailíaco (mm)', pliegue_abdominal: 'Pliegue abdominal (mm)', pliegue_supraespinal: 'Pliegue supraespinal (mm)', pliegue_muslo_medial: 'Pliegue muslo medial (mm)', pliegue_pantorrilla_medial: 'Pliegue pantorrilla medial (mm)', pliegue_bicipital: 'Pliegue bicipital (mm)', pliegue_muslo_anterior: 'Pliegue muslo anterior (mm)',
    diametro_humero: 'Diámetro húmero (cm)', diametro_femur: 'Diámetro fémur (cm)', diametro_muneca: 'Diámetro muñeca (cm)', diametro_biacromial: 'Diámetro biacromial (cm)', diametro_bi_iliocristal: 'Diámetro bi-iliocristal (cm)', diametro_torax_transverso: 'Diámetro tórax transverso (cm)', diametro_torax_anteroposterior: 'Diámetro tórax anteroposterior (cm)',
    circunferencia_brazo_relajado: 'Circunferencia brazo relajado (cm)', circunferencia_brazo_contraido: 'Circunferencia brazo contraído (cm)', circunferencia_pantorrilla: 'Circunferencia pantorrilla (cm)', circunferencia_cintura: 'Circunferencia cintura (cm)', circunferencia_cadera: 'Circunferencia cadera (cm)', circunferencia_muneca: 'Circunferencia muñeca (cm)', circunferencia_muslo_medio: 'Circunferencia muslo medio (cm)', circunferencia_cabeza: 'Circunferencia cabeza (cm)', circunferencia_antebrazo_maximo: 'Circunferencia antebrazo máximo (cm)', circunferencia_muslo_maximo: 'Circunferencia muslo máximo (cm)', circunferencia_torax: 'Circunferencia tórax (cm)'
};

function mostrarModalUnidadesSospechosas(issues) {
    var rows = issues.map(function(it){
        var etiqueta = (typeof etiquetasCamposComposicion !== 'undefined' && etiquetasCamposComposicion[it.campo]) ? etiquetasCamposComposicion[it.campo] : it.campo;
        return '<tr><th>' + etiqueta + '</th><td><strong>' + it.valor + '</strong></td><td class="text-muted small">' + (it.hint || '') + '</td></tr>';
    }).join('');
    var modalHtml = '<div class="modal fade" id="modalUnidadesSospechosas" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header bg-warning"><h5 class="modal-title"><i class="fas fa-ruler-combined me-2"></i>Unidades sospechosas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="small text-muted mb-2">Antes de calcular 5 componentes (Holway), revisá estos valores. Si están en pulgadas o son placeholders (ej. 20,00), el cálculo muscular puede dar <strong>MMUSC ≤ 0</strong>.</p><div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Campo</th><th>Valor</th><th>Recomendación</th></tr></thead><tbody>' + rows + '</tbody></table></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div></div></div></div>';
    $('#modalUnidadesSospechosas').remove();
    $('body').append(modalHtml);
    (new bootstrap.Modal(document.getElementById('modalUnidadesSospechosas'))).show();
}

function mostrarModalDatosInsuficientes(response) {
    var faltantes = response.faltantes || [];
    var metodo = response.metodo || '';
    var listaHtml = faltantes.map(function(campo) {
        var etiqueta = (typeof etiquetasCamposComposicion !== 'undefined' && etiquetasCamposComposicion[campo]) ? etiquetasCamposComposicion[campo] : campo.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        return '<li class="list-group-item"><i class="fas fa-exclamation-circle text-warning me-2"></i>' + etiqueta + '</li>';
    }).join('');
    var metodoNombre = { '2-componentes': '2 componentes', '4-componentes': '4 componentes (De Rose)', '5-componentes': '5 componentes (Kerr)', 'somatotipo': 'Somatotipo' }[metodo] || metodo;
    var modalHtml = '<div class="modal fade" id="modalDatosInsuficientes" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Datos insuficientes</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p class="mb-3">Para calcular <strong>' + metodoNombre + '</strong> faltan los siguientes datos. Complétalos en esta ficha y vuelve a intentar.</p><ul class="list-group list-group-flush">' + listaHtml + '</ul></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button></div></div></div></div>';
    $('#modalDatosInsuficientes').remove();
    $('body').append(modalHtml);
    (new bootstrap.Modal(document.getElementById('modalDatosInsuficientes'))).show();
}

function mostrarErrorUpgrade(response) {
    var modalHtml = '<div class="modal fade" id="modalErrorUpgrade" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-warning text-dark"><h5 class="modal-title"><i class="fas fa-lock me-2"></i>Método No Disponible</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Este método de cálculo no está disponible en tu plan actual (<strong>' + (response.paquete_actual || '') + '</strong>).</p><p>Para acceder a este método, necesitas actualizar tu plan.</p><div class="alert alert-info"><i class="fas fa-info-circle me-2"></i><strong>¿Interesado en actualizar?</strong> Contacta con soporte para más información sobre nuestros planes.</div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button><button type="button" class="btn btn-primary" onclick="window.location.href=\'<?= base_url('dashboard/paquetes') ?>\'">Ver Planes</button></div></div></div></div>';
    $('#modalErrorUpgrade').remove();
    $('body').append(modalHtml);
    (new bootstrap.Modal(document.getElementById('modalErrorUpgrade'))).show();
}

// Mostrar resultados: misma estructura que historial/editar (modal base)
function mostrarResultadosCalculo(nombreMetodo, resultado, metodoInfo) {
    var modalHtml = `
        <div class="modal fade" id="modalResultadosCalculo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calculator me-2"></i>Resultados: ${nombreMetodo}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="contenidoResultados"></div>
                        <div class="row mt-4" id="bloqueGraficos" style="display:none;">
                            <div class="col-md-6 mb-3" id="bloqueGraficoComposicion" style="display:none;">
                                <div class="card">
                                    <div class="card-header"><strong>Composición (kg)</strong></div>
                                    <div class="card-body">
                                        <canvas id="chartComposicion" height="220"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3" id="bloqueGraficoSomato" style="display:none;">
                                <div class="card">
                                    <div class="card-header"><strong>Somatocarta (Heath-Carter)</strong></div>
                                    <div class="card-body">
                                        <canvas id="chartSomatocarta" height="220"></canvas>
                                        <small class="text-muted d-block mt-2">Punto calculado según coordenadas Heath-Carter: \(X = Ecto - Endo\), \(Y = 2·Meso - (Endo + Ecto)\)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="imprimirResultados()">
                            <i class="fas fa-print me-1"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#modalResultadosCalculo').remove();
    $('body').append(modalHtml);
    var contenido = '';
    if (resultado && Array.isArray(resultado.warnings) && resultado.warnings.length) {
        contenido += '<div class="alert alert-warning border small mb-3"><strong>Revisar:</strong><ul class="mb-0">' + resultado.warnings.map(function(w){ return '<li>' + w + '</li>'; }).join('') + '</ul></div>';
    }
    contenido += '<div class="table-responsive"><table class="table table-bordered table-striped">';
    if (resultado && resultado.componentes) {
        var c = resultado.componentes;
        var esHolway5C = !!(c.masa_muscular || c.masa_residual || c.masa_osea_total || c.masa_piel || c.masa_total);
        if (c.masa_adiposa) contenido += '<tr><th>Masa Adiposa</th><td><strong>' + c.masa_adiposa.kg + ' kg</strong>' + (c.masa_adiposa.porcentaje != null ? ' (' + c.masa_adiposa.porcentaje + '%)' : '') + '</td></tr>';
        if (c.masa_magra) contenido += '<tr><th>Masa Magra</th><td><strong>' + c.masa_magra.kg + ' kg</strong>' + (c.masa_magra.porcentaje != null ? ' (' + c.masa_magra.porcentaje + '%)' : '') + '</td></tr>';
        if (esHolway5C) {
            if (c.masa_muscular) contenido += '<tr><th>Masa Muscular</th><td><strong>' + c.masa_muscular.kg + ' kg</strong>' + (c.masa_muscular.porcentaje != null ? ' (' + c.masa_muscular.porcentaje + '%)' : '') + '</td></tr>';
            if (c.masa_residual) contenido += '<tr><th>Masa Residual</th><td><strong>' + c.masa_residual.kg + ' kg</strong>' + (c.masa_residual.porcentaje != null ? ' (' + c.masa_residual.porcentaje + '%)' : '') + '</td></tr>';
            if (c.masa_osea_total) { contenido += '<tr><th>Masa Ósea Total</th><td><strong>' + c.masa_osea_total.kg + ' kg</strong>' + (c.masa_osea_total.porcentaje != null ? ' (' + c.masa_osea_total.porcentaje + '%)' : '') + '</td></tr>'; if (c.masa_osea_total.osea_cabeza_kg != null) contenido += '<tr><th class="text-muted">Ósea Cabeza</th><td><strong>' + c.masa_osea_total.osea_cabeza_kg + ' kg</strong></td></tr>'; if (c.masa_osea_total.osea_cuerpo_kg != null) contenido += '<tr><th class="text-muted">Ósea Cuerpo</th><td><strong>' + c.masa_osea_total.osea_cuerpo_kg + ' kg</strong></td></tr>'; }
            if (c.masa_piel) contenido += '<tr><th>Masa de la Piel</th><td><strong>' + c.masa_piel.kg + ' kg</strong>' + (c.masa_piel.porcentaje != null ? ' (' + c.masa_piel.porcentaje + '%)' : '') + '</td></tr>';
            if (c.masa_total) contenido += '<tr><th>Masa Total</th><td><strong>' + c.masa_total.kg + ' kg</strong></td></tr>';
        }
        if (!esHolway5C && c.grasa) {
            contenido += '<tr><th>Grasa</th><td><strong>' + c.grasa.kg + ' kg</strong>' + (c.grasa.porcentaje != null ? ' (' + c.grasa.porcentaje + '%)' : '') + '</td></tr><tr><th>Músculo</th><td><strong>' + c.musculo.kg + ' kg</strong>' + (c.musculo.porcentaje != null ? ' (' + c.musculo.porcentaje + '%)' : '') + '</td></tr><tr><th>Hueso</th><td><strong>' + c.hueso.kg + ' kg</strong>' + (c.hueso.porcentaje != null ? ' (' + c.hueso.porcentaje + '%)' : '') + '</td></tr><tr><th>Residual</th><td><strong>' + c.residual.kg + ' kg</strong>' + (c.residual.porcentaje != null ? ' (' + c.residual.porcentaje + '%)' : '') + '</td></tr>';
            if (c.piel) contenido += '<tr><th>Piel</th><td><strong>' + c.piel.kg + ' kg</strong>' + (c.piel.porcentaje != null ? ' (' + c.piel.porcentaje + '%)' : '') + '</td></tr>';
        }
        if (c.endomorfia !== undefined) contenido += '<tr><th>Endomorfia</th><td><strong>' + c.endomorfia + '</strong></td></tr><tr><th>Mesomorfia</th><td><strong>' + c.mesomorfia + '</strong></td></tr><tr><th>Ectomorfia</th><td><strong>' + c.ectomorfia + '</strong></td></tr>';
    } else if (resultado && (resultado.masa_adiposa_kg !== undefined || resultado.grasa_kg !== undefined)) {
        if (resultado.masa_adiposa_kg !== undefined) contenido += '<tr><th>Masa Adiposa</th><td><strong>' + resultado.masa_adiposa_kg + ' kg</strong> (' + resultado.masa_adiposa_porcentaje + '%)</td></tr><tr><th>Masa Magra</th><td><strong>' + resultado.masa_magra_kg + ' kg</strong> (' + resultado.masa_magra_porcentaje + '%)</td></tr>';
        if (resultado.grasa_kg !== undefined) contenido += '<tr><th>Grasa</th><td><strong>' + resultado.grasa_kg + ' kg</strong></td></tr><tr><th>Músculo</th><td><strong>' + resultado.musculo_kg + ' kg</strong></td></tr><tr><th>Hueso</th><td><strong>' + resultado.hueso_kg + ' kg</strong></td></tr><tr><th>Residual</th><td><strong>' + resultado.residual_kg + ' kg</strong></td></tr>';
        if (resultado.piel_kg !== undefined) contenido += '<tr><th>Piel</th><td><strong>' + resultado.piel_kg + ' kg</strong></td></tr>';
        if (resultado.endomorfia !== undefined) contenido += '<tr><th>Endomorfia</th><td><strong>' + resultado.endomorfia + '</strong></td></tr><tr><th>Mesomorfia</th><td><strong>' + resultado.mesomorfia + '</strong></td></tr><tr><th>Ectomorfia</th><td><strong>' + resultado.ectomorfia + '</strong></td></tr>';
    }
    contenido += '</table></div>';
    if (resultado && resultado.cierre_peso && typeof resultado.cierre_peso === 'object') {
        var cp = resultado.cierre_peso;
        var deltaSign = (cp.delta_kg >= 0) ? '+' : '';
        contenido += '<div class="mt-4"><h6 class="text-secondary border-bottom pb-2"><i class="fas fa-balance-scale me-2"></i>Cierre de peso (Kerr)</h6>';
        contenido += '<p class="small text-muted mb-2">Peso medido vs. peso reconstituido por el modelo. Es estándar en Kerr que no coincidan exactamente (redondeos, referencia poblacional).</p>';
        contenido += '<div class="table-responsive"><table class="table table-sm table-bordered">';
        contenido += '<tr><th>Peso medido</th><td><strong>' + cp.peso_medido_kg + ' kg</strong></td></tr>';
        contenido += '<tr><th>Peso reconstituido (modelo Kerr)</th><td><strong>' + cp.peso_reconstituido_kg + ' kg</strong></td></tr>';
        contenido += '<tr><th>Δ</th><td><strong>' + deltaSign + cp.delta_kg + ' kg</strong> (' + (cp.delta_pct >= 0 ? '+' : '') + cp.delta_pct + '%)</td></tr>';
        contenido += '</table></div>';
        if (cp.aviso_delta) {
            contenido += '<div class="alert alert-warning border small mt-2 mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Diferencia entre peso medido y peso estimado por el modelo superior al 7%. Revisar mediciones o referencia poblacional.</div>';
        }
        contenido += '</div>';
    }
    var etiqPasos = {
        suma_4_pliegues_mm: 'Σ 4 pliegues (mm)',
        suma_6_pliegues_mm: 'Σ 6 pliegues (mm)',
        log10_suma_pliegues: 'log₁₀(Σ pliegues)',
        densidad_d_durnin: 'Densidad corporal D (Durnin & Womersley)',
        densidad_de_rose: 'Densidad corporal D (De Rose)',
        densidad_kerr: 'Densidad corporal D (Kerr)',
        pct_grasa_siri: '% Grasa (fórmula Siri)',
        masa_grasa_kg: 'Masa grasa (kg)',
        masa_magra_kg: 'Masa magra (kg)',
        masa_osea_rocha_kg: 'Masa ósea (Rocha) (kg)',
        masa_residual_kg: 'Masa residual (kg)',
        masa_muscular_diferencia_kg: 'Masa muscular por diferencia (kg)',
        superficie_corporal_mosteller: 'Superficie corporal (Mosteller)',
        masa_piel_kg: 'Masa piel (kg)',
        suma_3_pliegues_endo: 'Σ 3 pliegues endomorfia (mm)',
        endomorfia: 'Endomorfia',
        mesomorfia: 'Mesomorfia',
        altura_peso_ratio: 'Altura / Peso^(1/3)',
        ectomorfia: 'Ectomorfia',
        k_area_superficial: 'K (constante área superficial)',
        gp_grosor_piel: 'GP (grosor piel)',
        as_area_superficial: 'AS (área superficial)',
        mpiell_masa_piel_kg: 'MPIEL (masa de la piel) (kg)',
        z_adip: 'Z_ADIP',
        madip_kg: 'MADIP (masa adiposa) (kg)',
        pbr_corr: 'PBR_CORR (brazo corregido) (cm)',
        pmus_corr: 'PMUS_CORR (muslo corregido) (cm)',
        ppan_corr: 'PPAN_CORR (pantorrilla corregida) (cm)',
        ptx_corr: 'PTX_CORR (tórax corregido) (cm)',
        sum_per_corr: 'SUM_PER_CORR',
        z_musc: 'Z_MUSC',
        mmusc_kg: 'MMUSC (masa muscular) (kg)',
        pcin_corr: 'PCIN_CORR (cintura corregida) (cm)',
        sum_torax: 'SUM_TORAX',
        z_res: 'Z_RES',
        mres_kg: 'MRES (masa residual) (kg)',
        sum_diam: 'SUM_DIAM',
        z_cab: 'Z_CAB',
        z_osea: 'Z_OSEA'
    };
    if (resultado && resultado.pasos_calculo && typeof resultado.pasos_calculo === 'object') {
        var definiciones = [];
        var metodoSlug = (resultado && resultado.metodo) ? String(resultado.metodo) : '';
        if (resultado.pasos_calculo.suma_4_pliegues_mm != null || (resultado.datos_usados && resultado.datos_usados.suma_4_pliegues != null)) {
            definiciones.push('Σ4 = Tricipital + Subescapular + Suprailíaco + Abdominal');
        }
        if (resultado.pasos_calculo.suma_6_pliegues_mm != null || (resultado.datos_usados && resultado.datos_usados.suma_6_pliegues != null)) {
            if (metodoSlug === '5-componentes' && (resultado.pasos_calculo.z_adip != null || resultado.pasos_calculo.madip_kg != null)) {
                definiciones.push('Σ6 = Tricipital + Subescapular + Supraespinal + Abdominal + Muslo medial + Pantorrilla (pliegue)');
            } else {
                definiciones.push('Σ6 = Tricipital + Subescapular + Suprailíaco + Abdominal + Muslo medial + Pantorrilla medial');
            }
        }
        if (resultado.pasos_calculo.suma_3_pliegues_endo != null || (resultado.datos_usados && resultado.datos_usados.suma_pliegues != null)) {
            definiciones.push('Σ3 (Endomorfia) = Tricipital + Subescapular + Suprailíaco');
        }
        if (metodoSlug === '2-componentes') {
            definiciones.push('Nota: en 2 componentes la talla NO participa del cálculo (se muestra como dato de ficha).');
        }
        var filasPasos = [];
        Object.keys(resultado.pasos_calculo).forEach(function(k) {
            var v = resultado.pasos_calculo[k];
            var etiq = etiqPasos[k] || k.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            if (v !== null && v !== undefined && v !== '') filasPasos.push('<tr><th>' + etiq + '</th><td><strong>' + v + '</strong></td></tr>');
        });
        if (filasPasos.length) {
            contenido += '<div class="mt-4"><h6 class="text-secondary border-bottom pb-2"><i class="fas fa-calculator me-2"></i>Pasos del cálculo</h6>';
            if (definiciones.length) {
                contenido += '<div class="alert alert-light border small py-2 mb-2"><strong>Definiciones:</strong><ul class="mb-0">' + definiciones.map(function(x){ return '<li>' + x + '</li>'; }).join('') + '</ul></div>';
            }
            contenido += '<p class="small text-muted mb-2">Valores intermedios obtenidos en cada paso. Sirven para verificar las fórmulas aplicadas.</p>';
            contenido += '<div class="table-responsive"><table class="table table-sm table-bordered">' + filasPasos.join('') + '</table></div></div>';
        }
    }
    if (resultado && resultado.datos_ficha && typeof resultado.datos_ficha === 'object') {
        var etiqFicha = { talla: 'Talla (cm)', altura: 'Altura (cm)' };
        var filasFicha = [];
        Object.keys(resultado.datos_ficha).forEach(function(k) {
            var v = resultado.datos_ficha[k];
            var etiq = etiqFicha[k] || k.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            if (v !== null && v !== undefined && v !== '') filasFicha.push('<tr><th>' + etiq + '</th><td><strong>' + v + '</strong></td></tr>');
        });
        if (filasFicha.length) {
            contenido += '<div class="mt-4"><h6 class="text-secondary border-bottom pb-2"><i class="fas fa-id-card me-2"></i>Datos de ficha (referencia)</h6>';
            contenido += '<p class="small text-muted mb-2">Se muestran como referencia clínica y no influyen en el cálculo del método.</p>';
            contenido += '<div class="table-responsive"><table class="table table-sm table-bordered">' + filasFicha.join('') + '</table></div></div>';
        }
    }
    if (resultado && resultado.datos_usados && typeof resultado.datos_usados === 'object') {
        var etiqDatos = {
            peso: 'Peso (kg)', talla: 'Talla (cm)', altura: 'Altura (cm)', altura_sentado: 'Altura sentado (cm)',
            edad: 'Edad (años)', sexo: 'Sexo', suma_4_pliegues: 'Suma 4 pliegues (mm)', suma_6_pliegues: 'Suma 6 pliegues (mm)',
            suma_pliegues: 'Suma pliegues (mm)', diametro_humero: 'Diámetro húmero (cm)', diametro_femur: 'Diámetro fémur (cm)'
        };
        var subEtiqMap = { humero: 'húmero (cm)', femur: 'fémur (cm)', brazo: 'brazo (cm)', pantorrilla: 'pantorrilla (cm)' };
        var filasDatos = [];
        function addRow(etiq, valor) {
            if (valor !== null && valor !== undefined && valor !== '') filasDatos.push('<tr><th>' + etiq + '</th><td><strong>' + valor + '</strong></td></tr>');
        }
        Object.keys(resultado.datos_usados).forEach(function(k) {
            var v = resultado.datos_usados[k];
            var etiq = etiqDatos[k] || k.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            if (v !== null && typeof v === 'object' && !Array.isArray(v)) {
                Object.keys(v).forEach(function(sub) {
                    var subEtiq = (k === 'diametros' ? 'Diámetro ' : (k === 'circunferencias' ? 'Circunferencia ' : '')) + (subEtiqMap[sub] || sub);
                    addRow(subEtiq, v[sub]);
                });
            } else {
                addRow(etiq, v);
            }
        });
        if (filasDatos.length) {
            contenido += '<div class="mt-4"><h6 class="text-secondary border-bottom pb-2"><i class="fas fa-clipboard-list me-2"></i>Datos utilizados para el cálculo</h6>';
            contenido += '<p class="small text-muted mb-2">Valores con los que se realizó el cálculo. Puede corroborarlos con la ficha del paciente.</p>';
            contenido += '<div class="table-responsive"><table class="table table-sm table-bordered">' + filasDatos.join('') + '</table></div></div>';
        }
    }
    $('#contenidoResultados').html(contenido);
    var modal = new bootstrap.Modal(document.getElementById('modalResultadosCalculo'));
    modal.show();
    setTimeout(function () {
        try {
            renderizarGraficosResultados(resultado);
        } catch (e) {
            console.warn('No se pudieron renderizar los gráficos:', e);
        }
    }, 150);
}

window.__chartComposicion = window.__chartComposicion || null;
window.__chartSomatocarta = window.__chartSomatocarta || null;
function destruirChartSiExiste(refName) {
    if (window[refName] && typeof window[refName].destroy === 'function') {
        window[refName].destroy();
        window[refName] = null;
    }
}
function renderizarGraficosResultados(resultado) {
    if (typeof Chart === 'undefined') return;
    $('#bloqueGraficos').hide();
    $('#bloqueGraficoComposicion').hide();
    $('#bloqueGraficoSomato').hide();
    destruirChartSiExiste('__chartComposicion');
    destruirChartSiExiste('__chartSomatocarta');
    var comp = resultado && resultado.componentes ? resultado.componentes : null;
    if (comp && (comp.grasa || comp.musculo || comp.hueso || comp.residual || comp.piel)) {
        var labels = [], values = [];
        if (comp.grasa && comp.grasa.kg != null) { labels.push('Grasa'); values.push(Number(comp.grasa.kg)); }
        if (comp.musculo && comp.musculo.kg != null) { labels.push('Músculo'); values.push(Number(comp.musculo.kg)); }
        if (comp.hueso && comp.hueso.kg != null) { labels.push('Hueso'); values.push(Number(comp.hueso.kg)); }
        if (comp.residual && comp.residual.kg != null) { labels.push('Residual'); values.push(Number(comp.residual.kg)); }
        if (comp.piel && comp.piel.kg != null) { labels.push('Piel'); values.push(Number(comp.piel.kg)); }
        var filtered = labels.map(function(l, i) { return { l: l, v: values[i] }; }).filter(function(x) { return !isNaN(x.v) && isFinite(x.v); });
        labels = filtered.map(function(x) { return x.l; });
        values = filtered.map(function(x) { return x.v; });
        if (values.length > 0) {
            $('#bloqueGraficos').show();
            $('#bloqueGraficoComposicion').show();
            var ctx = document.getElementById('chartComposicion');
            if (ctx) {
                window.__chartComposicion = new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels: labels, datasets: [{ data: values, backgroundColor: ['#f59e0b', '#3b82f6', '#64748b', '#94a3b8', '#22c55e'], borderWidth: 1 }] },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        var val = context.parsed !== undefined ? context.parsed : (context.raw || 0);
                                        return context.label + ': ' + val + ' kg';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
    if (comp && comp.endomorfia !== undefined && comp.mesomorfia !== undefined && comp.ectomorfia !== undefined) {
        var endo = Number(comp.endomorfia), meso = Number(comp.mesomorfia), ecto = Number(comp.ectomorfia);
        if (!isNaN(endo) && !isNaN(meso) && !isNaN(ecto)) {
            var x = ecto - endo, y = (2 * meso) - (endo + ecto);
            var pad = 2;
            var minX = Math.floor(Math.min(-10, x - pad)), maxX = Math.ceil(Math.max(10, x + pad));
            var minY = Math.floor(Math.min(-10, y - pad)), maxY = Math.ceil(Math.max(10, y + pad));
            $('#bloqueGraficos').show();
            $('#bloqueGraficoSomato').show();
            var ctx2 = document.getElementById('chartSomatocarta');
            if (ctx2) {
                window.__chartSomatocarta = new Chart(ctx2, {
                    type: 'scatter',
                    data: { datasets: [{ label: 'Somatotipo', data: [{ x: x, y: y }], pointBackgroundColor: '#ef4444', pointRadius: 7, pointHoverRadius: 9 }] },
                    options: {
                        responsive: true,
                        scales: {
                            x: { type: 'linear', min: minX, max: maxX, grid: { color: '#e5e7eb' }, title: { display: true, text: 'X (Ecto - Endo)' } },
                            y: { type: 'linear', min: minY, max: maxY, grid: { color: '#e5e7eb' }, title: { display: true, text: 'Y (2·Meso - Endo - Ecto)' } }
                        },
                        plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(context) { return 'X=' + context.parsed.x.toFixed(2) + ', Y=' + context.parsed.y.toFixed(2); } } } }
                    }
                });
            }
        }
    }
}
function imprimirResultados() {
    window.print();
}

// Auto-guardar cada 2 minutos si hay cambios
var ultimoContenido = {
    notas: '',
    objetivos: $('#objetivos').val(),
    plan: $('#plan_alimentacion').val(),
    recomendaciones: $('#recomendaciones').val()
};

// Inicializar contenido de todos los editores después de que TinyMCE esté listo
setTimeout(function() {
    if (tinymce.get('notas_consulta')) {
        ultimoContenido.notas = tinymce.get('notas_consulta').getContent();
    } else {
        ultimoContenido.notas = $('#notas_consulta').val();
    }
    
    if (tinymce.get('objetivos')) {
        ultimoContenido.objetivos = tinymce.get('objetivos').getContent();
    } else {
        ultimoContenido.objetivos = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        ultimoContenido.plan = tinymce.get('plan_alimentacion').getContent();
    } else {
        ultimoContenido.plan = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        ultimoContenido.recomendaciones = tinymce.get('recomendaciones').getContent();
    } else {
        ultimoContenido.recomendaciones = $('#recomendaciones').val();
    }
}, 1500);

setInterval(function() {
    var notasActuales = '';
    var objetivosActuales = '';
    var planActual = '';
    var recomendacionesActuales = '';
    
    if (tinymce.get('notas_consulta')) {
        notasActuales = tinymce.get('notas_consulta').getContent();
    } else {
        notasActuales = $('#notas_consulta').val();
    }
    
    if (tinymce.get('objetivos')) {
        objetivosActuales = tinymce.get('objetivos').getContent();
    } else {
        objetivosActuales = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        planActual = tinymce.get('plan_alimentacion').getContent();
    } else {
        planActual = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        recomendacionesActuales = tinymce.get('recomendaciones').getContent();
    } else {
        recomendacionesActuales = $('#recomendaciones').val();
    }
    
    var hayCambios = 
        notasActuales !== ultimoContenido.notas ||
        objetivosActuales !== ultimoContenido.objetivos ||
        planActual !== ultimoContenido.plan ||
        recomendacionesActuales !== ultimoContenido.recomendaciones;
    
    if (hayCambios && consultaIniciada) {
        guardarNotasConsulta();
        ultimoContenido = {
            notas: notasActuales,
            objetivos: objetivosActuales,
            plan: planActual,
            recomendaciones: recomendacionesActuales
        };
    }
}, 120000); // 2 minutos

// Iniciar timer si la consulta ya está iniciada
$(document).ready(function() {
    if (consultaIniciada && fechaInicio) {
        iniciarTimer();
    }
    
    // Función para inicializar TinyMCE cuando esté listo
    function inicializarTinyMCE() {
        // Verificar que TinyMCE esté cargado
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE no está cargado. Reintentando en 500ms...');
            setTimeout(inicializarTinyMCE, 500);
            return;
        }
        
        console.log('TinyMCE está cargado, procediendo a inicializar editores...');
    
    // Configuración común de TinyMCE (simplificada para evitar errores)
    var tinymceConfig = {
        height: 300,
        menubar: false,
        plugins: [
            'lists', 'link', 'table', 'code', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link table | code',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        language: 'es',
        branding: false,
        promotion: false,
        setup: function(editor) {
            // Auto-guardar cuando se hace un cambio (después de 2 segundos de inactividad)
            var timeout;
            editor.on('keyup', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    if (consultaIniciada) {
                        guardarNotasConsulta();
                    }
                }, 2000); // 2 segundos después de dejar de escribir
            });
        }
    };

    // Función para inicializar TinyMCE con manejo de errores
    function inicializarEditor(selector, height) {
        if (!$(selector).length) {
            console.error('Elemento no encontrado:', selector);
            return;
        }
        
        try {
            var config = Object.assign({}, tinymceConfig, {
                selector: selector,
                height: height
            });
            
            tinymce.init(config).then(function(editors) {
                console.log('TinyMCE inicializado correctamente para:', selector);
            }).catch(function(error) {
                console.error('Error al inicializar TinyMCE para ' + selector + ':', error);
            });
        } catch (error) {
            console.error('Error al inicializar TinyMCE para ' + selector + ':', error);
        }
    }

        // Esperar un momento para asegurar que el DOM esté completamente cargado
        setTimeout(function() {
            // Inicializar TinyMCE para el editor de notas (más alto)
            inicializarEditor('#notas_consulta', 400);

            // Inicializar TinyMCE para Objetivos
            inicializarEditor('#objetivos', 250);

            // Inicializar TinyMCE para Plan de Alimentación
            inicializarEditor('#plan_alimentacion', 250);

            // Inicializar TinyMCE para Recomendaciones
            inicializarEditor('#recomendaciones', 200);
        }, 100);
    }
    
    // Iniciar la inicialización de TinyMCE
    inicializarTinyMCE();
    
    // ========== Agendar hora real (próxima cita en agenda/detalle_agenda) ==========
    var pacienteIdAgendar = <?= (int)($cita->paciente_id ?? 0) ?>;
    var flatpickrModalAgendar = null;
    
    window.abrirModalAgendarHoraReal = function() {
        var fechaRec = ($('#proxima_cita_recomendada').val() || '').split(' ')[0] || '';
        if (!fechaRec) {
            var hoy = new Date();
            var dd = String(hoy.getDate()).padStart(2, '0');
            var mm = String(hoy.getMonth() + 1).padStart(2, '0');
            var yyyy = hoy.getFullYear();
            fechaRec = dd + '-' + mm + '-' + yyyy;
        }
        $('#modalAgendarFechaBuscar').val(fechaRec);
        $('#slotsAgendarLoading').hide();
        $('#slotsAgendarLista').hide().empty();
        $('#slotsAgendarVacio').hide();
        if (!flatpickrModalAgendar) {
            flatpickrModalAgendar = flatpickr("#modalAgendarFechaBuscar", {
                dateFormat: "d-m-Y",
                locale: "es",
                minDate: "today",
                allowInput: false,
                clickOpens: true
            });
        }
        var modal = new bootstrap.Modal(document.getElementById('modalAgendarHoraReal'));
        modal.show();
    };
    
    function fechaDdMmYyyyToYyyyMmDd(str) {
        if (!str || str.length < 10) return '';
        var parts = str.split('-');
        if (parts.length === 3) {
            if (parts[0].length === 2 && parts[1].length === 2 && parts[2].length === 4)
                return parts[2] + '-' + parts[1] + '-' + parts[0];
            if (parts[0].length === 4 && parts[1].length === 2 && parts[2].length === 2)
                return str;
        }
        return str;
    }
    
    $('#btnBuscarHorariosAgendar').on('click', function() {
        var fechaStr = $('#modalAgendarFechaBuscar').val();
        if (!fechaStr) {
            toastr.warning('Elegí una fecha para buscar horarios.', 'Fecha requerida');
            return;
        }
        var start = fechaDdMmYyyyToYyyyMmDd(fechaStr);
        if (!start) {
            toastr.warning('Formato de fecha inválido. Usá DD-MM-YYYY.', 'Error');
            return;
        }
        var endDate = new Date(start);
        endDate.setDate(endDate.getDate() + 7);
        var end = endDate.getFullYear() + '-' + String(endDate.getMonth() + 1).padStart(2, '0') + '-' + String(endDate.getDate()).padStart(2, '0');
        $('#slotsAgendarLoading').show();
        $('#slotsAgendarLista').hide().empty();
        $('#slotsAgendarVacio').hide();
        $.get('<?= base_url('dashboard/agenda/getEventos') ?>', { start: start, end: end }, function(res) {
            $('#slotsAgendarLoading').hide();
            var events = (res && res.events) ? res.events : [];
            var disponibles = events.filter(function(ev) {
                return (ev.extendedProps && ev.extendedProps.estado_cita === 'disponible') || (!ev.extendedProps || !ev.extendedProps.paciente_id);
            });
            if (disponibles.length === 0) {
                $('#slotsAgendarVacio').show();
                return;
            }
            var html = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Fecha</th><th>Hora</th><th>Modalidad</th><th></th></tr></thead><tbody>';
            disponibles.forEach(function(ev) {
                var startDt = ev.start ? new Date(ev.start) : null;
                var endDt = ev.end ? new Date(ev.end) : null;
                var fechaLabel = startDt ? (String(startDt.getDate()).padStart(2, '0') + '-' + String(startDt.getMonth() + 1).padStart(2, '0') + '-' + startDt.getFullYear()) : '';
                var horaDisplay = (startDt && endDt) ? (startDt.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' }) + ' - ' + endDt.toLocaleTimeString('es-CL', { hour: '2-digit', minute: '2-digit' })) : '';
                var horaParaGuardar = (startDt && endDt) ? (String(startDt.getHours()).padStart(2, '0') + ':' + String(startDt.getMinutes()).padStart(2, '0') + ' - ' + String(endDt.getHours()).padStart(2, '0') + ':' + String(endDt.getMinutes()).padStart(2, '0')) : '';
                var modId = (ev.extendedProps && ev.extendedProps.modalidad_id) ? ev.extendedProps.modalidad_id : 3;
                var modLabel = modId == 1 ? 'Presencial' : (modId == 2 ? 'Online' : 'Sin definir');
                html += '<tr><td>' + fechaLabel + '</td><td>' + horaDisplay + '</td><td>' + modLabel + '</td><td><button type="button" class="btn btn-sm btn-success btn-reservar-slot" data-id="' + ev.id + '" data-fecha="' + fechaLabel + '" data-hora="' + horaParaGuardar + '"><i class="fas fa-check me-1"></i> Reservar</button></td></tr>';
            });
            html += '</tbody></table></div>';
            $('#slotsAgendarLista').html(html).show();
        }).fail(function() {
            $('#slotsAgendarLoading').hide();
            $('#slotsAgendarVacio').show();
            toastr.error('No se pudieron cargar los horarios.', 'Error');
        });
    });
    
    $(document).on('click', '.btn-reservar-slot', function() {
        var detalleAgendaId = $(this).data('id');
        var fechaLabel = $(this).data('fecha');
        var horaLabel = $(this).data('hora');
        if (!detalleAgendaId || !pacienteIdAgendar) {
            toastr.error('Faltan datos para reservar.', 'Error');
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Reservando...');
        var csrfToken = $('input[name="csrf_test_name"]').val() || obtenerTokenCSRF() || '<?= csrf_hash() ?>';
        $.ajax({
            url: '<?= base_url('dashboard/agenda/agendar') ?>',
            type: 'POST',
            data: {
                detalle_agenda_id: detalleAgendaId,
                paciente_id: pacienteIdAgendar,
                tipo_consulta: 'seguimiento',
                motivo: 'Próxima cita recomendada desde consulta',
                csrf_test_name: csrfToken
            },
            headers: { 'X-CSRF-TOKEN': csrfToken },
            dataType: 'json',
            success: function(response, textStatus, xhr) {
                // Actualizar token CSRF (meta + input) para que Guardar todo / Finalizar sigan funcionando
                actualizarTokenCSRF(xhr);
                if (response && response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAgendarHoraReal')).hide();
                    toastr.success('Cita agendada: ' + fechaLabel + ' a las ' + horaLabel + '. Podés verla en el calendario.', 'Éxito', { timeOut: 5000 });
                    // Guardar en formato 24h (ej. "30-01-2026 15:30 - 16:00") para que persista y se vea bien al actualizar
                    $('#proxima_cita_recomendada').val(fechaLabel + ' ' + horaLabel);
                    setTimeout(function() {
                        guardarNotasConsulta();
                    }, 400);
                    setTimeout(function() {
                        toastr.info('Podés ver la cita en <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="alert-link">Calendario</a>', 'Ver agenda', { timeOut: 6000 });
                    }, 800);
                } else {
                    $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Reservar');
                    toastr.error(response.message || response.error || 'Error al agendar.', 'Error');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i> Reservar');
                actualizarTokenCSRF(xhr);
                var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'Error al agendar la cita.';
                toastr.error(msg, 'Error');
            }
        });
    });
    
    // Event listener para el botón de confirmar terminar consulta
    $('#btnConfirmarTerminar').on('click', function() {
        confirmarTerminarConsulta();
    });

    // Al expandir "Calorimetría y plan alimentario", cargar/recargar calorimetría guardada
    // (por si al cargar la página aún no existían los inputs de actividades)
    $('#calorimetriaPlanCollapse').on('shown.bs.collapse', function() {
        if (typeof cargarCalorimetriaExistente === 'function') {
            cargarCalorimetriaExistente();
        }
    });
});
</script>

<?= $this->endSection() ?>

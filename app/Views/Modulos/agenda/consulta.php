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
                
                <div class="collapse" id="medicionesCollapse">
                    <form id="formMediciones" onsubmit="guardarMediciones(event)">
                        <input type="hidden" name="detalle_agenda_id" value="<?= $cita->id ?>">
                        <input type="hidden" name="paciente_id" value="<?= $cita->paciente_id ?>">
                        <input type="hidden" name="historial_id" id="historial_id" value="<?= isset($historial) && $historial ? $historial->id : '' ?>">
                        
                        <!-- Medidas Básicas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-weight me-2"></i> Medidas Básicas</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Peso (kg) *</label>
                                <input type="number" name="peso_actual" id="peso_actual" class="form-control" step="0.01" min="0" placeholder="Ej: 70.5">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Altura (cm) *</label>
                                <input type="number" name="altura_actual" id="altura_actual" class="form-control" step="0.01" min="0" placeholder="Ej: 170">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IMC</label>
                                <input type="number" name="imc_actual" id="imc_actual" class="form-control" step="0.01" readonly>
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>

                        <!-- Circunferencias -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-circle-notch me-2"></i> Circunferencias</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cintura (cm)</label>
                                <input type="number" name="circunferencia_cintura" id="circunferencia_cintura" class="form-control" step="0.01" min="0" placeholder="Ej: 85.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cadera (cm)</label>
                                <input type="number" name="circunferencia_cadera" id="circunferencia_cadera" class="form-control" step="0.01" min="0" placeholder="Ej: 95.0">
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
                                <input type="number" name="pliegue_tricipital" id="pliegue_tricipital" class="form-control" step="0.01" min="0" placeholder="Ej: 12.5">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Bicipital</label>
                                <input type="number" name="pliegue_bicipital" id="pliegue_bicipital" class="form-control" step="0.01" min="0" placeholder="Ej: 8.3">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Subescapular</label>
                                <input type="number" name="pliegue_subescapular" id="pliegue_subescapular" class="form-control" step="0.01" min="0" placeholder="Ej: 15.2">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Suprailíaco</label>
                                <input type="number" name="pliegue_suprailíaco" id="pliegue_suprailíaco" class="form-control" step="0.01" min="0" placeholder="Ej: 18.7">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Abdominal</label>
                                <input type="number" name="pliegue_abdominal" id="pliegue_abdominal" class="form-control" step="0.01" min="0" placeholder="Ej: 22.1">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Muslo Anterior</label>
                                <input type="number" name="pliegue_muslo_anterior" id="pliegue_muslo_anterior" class="form-control" step="0.01" min="0" placeholder="Ej: 20.5">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pantorrilla Medial</label>
                                <input type="number" name="pliegue_pantorrilla_medial" id="pliegue_pantorrilla_medial" class="form-control" step="0.01" min="0" placeholder="Ej: 10.8">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Suma de Pliegues (mm)</label>
                                <input type="number" name="suma_pliegues" id="suma_pliegues" class="form-control" step="0.01" readonly>
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>

                        <!-- Composición Corporal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-chart-pie me-2"></i> Composición Corporal</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grasa Corporal (%)</label>
                                <input type="number" name="grasa_corporal" id="grasa_corporal" class="form-control" step="0.01" min="0" max="100" placeholder="Ej: 25.5">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Grasa Corporal Calculada (%)</label>
                                <input type="number" name="grasa_corporal_calculada" id="grasa_corporal_calculada" class="form-control" step="0.01" readonly>
                                <small class="text-muted">Se calcula a partir de los pliegues</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Masa Muscular (kg)</label>
                                <input type="number" name="masa_muscular" id="masa_muscular" class="form-control" step="0.01" min="0" placeholder="Ej: 45.2">
                            </div>
                        </div>

                        <!-- Información Clínica -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3"><i class="fas fa-stethoscope me-2"></i> Información Clínica</h6>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Anamnesis</label>
                                <textarea name="anamnesis" id="anamnesis" class="form-control" rows="4" placeholder="Historia clínica del paciente, antecedentes, síntomas..."></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Diagnóstico Nutricional</label>
                                <textarea name="diagnostico" id="diagnostico" class="form-control" rows="3" placeholder="Diagnóstico nutricional establecido..."></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Plan de Tratamiento</label>
                                <textarea name="plan_tratamiento" id="plan_tratamiento" class="form-control" rows="3" placeholder="Plan de tratamiento propuesto..."></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Tags <small class="text-muted">(Escriba y presione Enter o coma para agregar)</small></label>
                                <input type="text" name="tags" id="tags" class="form-control" 
                                       placeholder="Ej: diabetes, hipertensión, seguimiento, control"
                                       value="">
                                <small class="form-text text-muted">
                                    Los tags ayudan a categorizar y buscar consultas. Ejemplos: diabetes, hipertensión, seguimiento, control, etc.
                                </small>
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

                <div class="section-card">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-secondary mb-3"><i class="fas fa-calendar-check me-2"></i> Próxima Cita Recomendada</h5>
                            <div class="alert alert-info d-flex align-items-start mb-3" role="alert">
                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Recomendación:</strong> Indica la fecha sugerida para la próxima consulta de seguimiento. Esta fecha es una recomendación y puede ser modificada al agendar la próxima cita.
                                </div>
                            </div>
                            <input 
                                type="text" 
                                name="proxima_cita_recomendada" 
                                id="proxima_cita_recomendada" 
                                class="form-control" 
                                placeholder="DD-MM-YYYY"
                                value="<?= $cita->proxima_cita_recomendada ? date('d-m-Y', strtotime($cita->proxima_cita_recomendada)) : '' ?>"
                                readonly
                            >
                            <small class="form-text text-muted">Haz clic en el campo para seleccionar la fecha</small>
                        </div>
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
        } else {
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
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
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
        $('#imc_actual').val(historial.imc_actual || '');
        $('#circunferencia_cintura').val(historial.circunferencia_cintura || '');
        $('#circunferencia_cadera').val(historial.circunferencia_cadera || '');
        $('#grasa_corporal').val(historial.grasa_corporal || '');
        $('#masa_muscular').val(historial.masa_muscular || '');
        $('#pliegue_tricipital').val(historial.pliegue_tricipital || '');
        $('#pliegue_bicipital').val(historial.pliegue_bicipital || '');
        $('#pliegue_subescapular').val(historial.pliegue_subescapular || '');
        $('#pliegue_suprailíaco').val(historial.pliegue_suprailíaco || '');
        $('#pliegue_abdominal').val(historial.pliegue_abdominal || '');
        $('#pliegue_muslo_anterior').val(historial.pliegue_muslo_anterior || '');
        $('#pliegue_pantorrilla_medial').val(historial.pliegue_pantorrilla_medial || '');
        $('#suma_pliegues').val(historial.suma_pliegues || '');
        $('#grasa_corporal_calculada').val(historial.grasa_corporal_calculada || '');
        $('#anamnesis').val(historial.anamnesis || '');
        $('#diagnostico').val(historial.diagnostico || '');
        $('#plan_tratamiento').val(historial.plan_tratamiento || '');
        
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
        'pliegue_abdominal',
        'pliegue_muslo_anterior',
        'pliegue_pantorrilla_medial'
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
    $('#pliegue_tricipital, #pliegue_bicipital, #pliegue_subescapular, #pliegue_suprailíaco, #pliegue_abdominal, #pliegue_muslo_anterior, #pliegue_pantorrilla_medial').on('input', function() {
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
    
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
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
    
    // Serializar el formulario y reemplazar el campo tags con el valor correcto
    var formData = $('#formMediciones').serialize();
    
    // Remover el campo tags si existe en el formData serializado
    formData = formData.replace(/&?tags=[^&]*/g, '');
    
    // Agregar el campo tags con el valor correcto
    if (tagsValue) {
        formData += '&tags=' + encodeURIComponent(tagsValue);
    }
    
    formData += '&' + csrfName + '=' + csrfToken;
    
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
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Limpiar formulario de mediciones
function limpiarFormularioMediciones() {
    if (confirm('¿Está seguro de limpiar todos los campos de mediciones?')) {
        $('#formMediciones')[0].reset();
        $('#historial_id').val('');
        $('#imc_actual').val('');
        $('#suma_pliegues').val('');
        $('#grasa_corporal_calculada').val('');
    }
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
    
    // Inicializar Flatpickr para el selector de fecha
    var fechaProximaCita = <?= $cita->proxima_cita_recomendada ? "'" . date('d-m-Y', strtotime($cita->proxima_cita_recomendada)) . "'" : 'null' ?>;
    
    flatpickr("#proxima_cita_recomendada", {
        dateFormat: "d-m-Y",
        locale: "es",
        minDate: "today",
        allowInput: false,
        clickOpens: true,
        defaultDate: fechaProximaCita,
        onChange: function(selectedDates, dateStr, instance) {
            // Opcional: auto-guardar cuando se cambia la fecha
            if (consultaIniciada && dateStr) {
                setTimeout(function() {
                    guardarNotasConsulta();
                }, 500);
            }
        }
    });
    
    // Event listener para el botón de confirmar terminar consulta
    $('#btnConfirmarTerminar').on('click', function() {
        confirmarTerminarConsulta();
    });
});
</script>

<?= $this->endSection() ?>

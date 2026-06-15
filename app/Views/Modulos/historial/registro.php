<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/registro') ?>

<!-- Tagify para tags -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #fa709a;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }
    
    /* Leyenda de métodos de cálculo (igual que en Agenda/Consulta y Editar) */
    .metodo-4 { border-left: 4px solid #dc3545 !important; background-color: #fff5f5 !important; }
    .metodo-5 { border-left: 4px solid #0d6efd !important; background-color: #f0f7ff !important; }
    .metodo-2 { border-left: 4px solid #198754 !important; background-color: #f0fff4 !important; }
    .metodo-4.metodo-5 { border-left: 4px solid #6f42c1 !important; background: linear-gradient(90deg, #fff5f5 0%, #f0f7ff 100%) !important; }
    .metodo-4.metodo-2 { border-left: 4px solid #fd7e14 !important; background: linear-gradient(90deg, #fff5f5 0%, #f0fff4 100%) !important; }
    .metodo-5.metodo-2 { border-left: 4px solid #20c997 !important; background: linear-gradient(90deg, #f0f7ff 0%, #f0fff4 100%) !important; }
    .metodo-4.metodo-5.metodo-2 { border-left: 4px solid #6c757d !important; background: linear-gradient(90deg, #fff5f5 0%, #f0f7ff 50%, #f0fff4 100%) !important; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-stethoscope me-2"></i> Registrar Nueva Consulta</h2>
                        <p style="color: white;">Registre una nueva consulta en el historial clínico</p>
                    </div>
                    <a href="<?= base_url('dashboard/historial/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <form action="<?= base_url('dashboard/historial/registrar') ?>" method="post" id="formHistorialRegistro" onsubmit="return prepararFichaIngresoHistorial(this)">
                <?= csrf_field() ?>
                
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">1</span>
                        <span><i class="fas fa-calendar icon-label"></i> Información de la Consulta</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paciente <span class="text-danger">*</span></label>
                                <select name="paciente_id" class="form-control" required>
                                    <option value="">-- Seleccione un paciente --</option>
                                    <?php foreach ($pacientes as $paciente) : ?>
                                        <option value="<?= $paciente->id ?>"><?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo <span class="text-danger">*</span></label>
                                <select name="tipo_registro" class="form-control" required>
                                    <option value="consulta">Consulta</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="control">Control</option>
                                    <option value="emergencia">Emergencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_consulta" class="form-control" required value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora</label>
                                <input type="time" name="hora_consulta" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próxima Cita</label>
                                <input type="date" name="proxima_cita" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">2</span>
                        <span><i class="fas fa-weight icon-label"></i> Medidas Corporales</span>
                    </div>
                    
                    <!-- Leyenda de Métodos de Cálculo (igual que en Agenda/Consulta y Editar) -->
                    <div class="alert alert-light border mb-4" style="background-color: #f8f9fa;">
                        <div class="d-flex align-items-center mb-2">
                            <strong class="me-2"><i class="fas fa-info-circle me-1"></i> Leyenda de Métodos de Cálculo:</strong>
                        </div>
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #dc3545 !important; background-color: #fff5f5;">
                                <span class="fw-bold text-danger">4 Componentes</span>
                            </div>
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #0d6efd !important; background-color: #f0f7ff;">
                                <span class="fw-bold text-primary">5 Componentes</span>
                            </div>
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #198754 !important; background-color: #f0fff4;">
                                <span class="fw-bold text-success">2 Componentes</span>
                            </div>
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #6f42c1 !important; background-color: #f3effd;">
                                <span class="fw-bold" style="color: #6f42c1;">4 y 5 Componentes</span>
                            </div>
                            <div class="border rounded p-2" style="border-width: 3px !important; border-color: #6c757d !important; background-color: #e9ecef;">
                                <span class="fw-bold text-secondary">4, 5 y 2 Componentes</span>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            El borde de cada campo indica para qué método(s) se usa: <strong>Rojo</strong> solo 4 · <strong>Azul</strong> solo 5 · <strong>Verde</strong> solo 2 · <strong>Morado</strong> 4 y 5 · <strong>Gris</strong> 4, 5 y 2. Los que no llevan color no se usan en estos métodos.
                        </small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" step="0.01" name="peso_actual" class="form-control" id="peso_actual">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Altura (cm)</label>
                                <input type="number" step="0.01" name="altura_actual" class="form-control" id="altura_actual">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Altura Sentado (cm)</label>
                                <input type="number" step="0.01" name="altura_sentado" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>IMC</label>
                                <input type="text" class="form-control" id="imc_actual" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grasa Corporal (%)</label>
                                <input type="number" step="0.01" name="grasa_corporal" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Masa Muscular (kg)</label>
                                <input type="number" step="0.01" name="masa_muscular" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-circle-notch me-2"></i> Circunferencias (cm)</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cintura</label>
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control metodo-4 metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cadera</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Relajado</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_relajado" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Contraído</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_contraido" class="form-control metodo-somato">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medio</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_medio" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla</label>
                                <input type="number" step="0.01" name="circunferencia_pantorrilla" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cuello</label>
                                <input type="number" step="0.01" name="circunferencia_cuello" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax</label>
                                <input type="number" step="0.01" name="circunferencia_torax" class="form-control metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cabeza</label>
                                <input type="number" step="0.01" name="circunferencia_cabeza" class="form-control metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Antebrazo Máximo</label>
                                <input type="number" step="0.01" name="circunferencia_antebrazo_maximo" class="form-control metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Máximo</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_maximo" class="form-control metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="circunferencia_muneca" class="form-control" placeholder="Circunferencia (cm)">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-ruler me-2"></i> Diámetros Óseos (cm)</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Biacromial</label>
                                <input type="number" step="0.01" name="diametro_biacromial" class="form-control metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bi-iliocristal</label>
                                <input type="number" step="0.01" name="diametro_bi_iliocristal" class="form-control metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Húmero</label>
                                <input type="number" step="0.01" name="diametro_humero" class="form-control metodo-4 metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fémur</label>
                                <input type="number" step="0.01" name="diametro_femur" class="form-control metodo-4 metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="diametro_muneca" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tobillo</label>
                                <input type="number" step="0.01" name="diametro_tobillo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax Transverso</label>
                                <input type="number" step="0.01" name="diametro_torax_transverso" class="form-control metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax Anteroposterior</label>
                                <input type="number" step="0.01" name="diametro_torax_anteroposterior" class="form-control metodo-5">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-compress-alt me-2"></i> Pliegues Cutáneos (mm)</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tricipital</label>
                                <input type="number" step="0.01" name="pliegue_tricipital" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bicipital</label>
                                <input type="number" step="0.01" name="pliegue_bicipital" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subescapular</label>
                                <input type="number" step="0.01" name="pliegue_subescapular" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suprailíaco</label>
                                <input type="number" step="0.01" name="pliegue_suprailíaco" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Supraespinal</label>
                                <input type="number" step="0.01" name="pliegue_supraespinal" class="form-control metodo-2 metodo-5">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Abdominal</label>
                                <input type="number" step="0.01" name="pliegue_abdominal" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Anterior</label>
                                <input type="number" step="0.01" name="pliegue_muslo_anterior" class="form-control metodo-4">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla Medial</label>
                                <input type="number" step="0.01" name="pliegue_pantorrilla_medial" class="form-control metodo-4 metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pectoral</label>
                                <input type="number" step="0.01" name="pliegue_pectoral" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Axilar Medio</label>
                                <input type="number" step="0.01" name="pliegue_axilar_medio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medial</label>
                                <input type="number" step="0.01" name="pliegue_muslo_medial" class="form-control metodo-5 metodo-2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suma de Pliegues (mm)</label>
                                <input type="number" step="0.01" name="suma_pliegues" class="form-control" readonly>
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">3</span>
                        <span><i class="fas fa-file-medical icon-label"></i> Información Clínica</span>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-bullseye me-2"></i> Motivo de consulta y/o Objetivo Principal</h6>
                            <p class="text-muted small mb-1">Indique el motivo de la consulta o el objetivo principal acordado con el paciente.</p>
                            <textarea name="motivo_consulta" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="mb-2" style="color: #0dcaf0;"><i class="fas fa-utensils me-2"></i> Plan de Tratamiento</h6>
                            <p class="text-muted small mb-1">Describe el plan de tratamiento y alimentación acordado.</p>
                            <textarea name="plan_tratamiento" class="form-control" rows="4"></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6 class="mb-2" style="color: #fd7e14;"><i class="fas fa-lightbulb me-2"></i> Recomendaciones u Observaciones</h6>
                            <p class="text-muted small mb-1">Recomendaciones y observaciones para el paciente.</p>
                            <textarea name="recomendaciones" class="form-control" rows="4"></textarea>
                            <input type="hidden" name="observaciones" value="">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tags <small class="text-muted">(Escriba y presione Enter o coma para agregar)</small></label>
                                <input type="text" name="tags" id="tags" class="form-control" 
                                       placeholder="Ej: diabetes, hipertensión, seguimiento, control"
                                       value="">
                                <small class="form-text text-muted">
                                    Los tags ayudan a categorizar y buscar consultas. Ejemplos: diabetes, hipertensión, seguimiento, control, etc.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Ficha de Ingreso: Anamnesis clínica, alimentaria, exámenes, tendencia, recordatorio 24h (igual que en editar/consulta) -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12"><h6 class="text-info mb-3"><i class="fas fa-notes-medical me-2"></i> Anamnesis Clínica</h6><p class="text-muted small mb-3">Completar cada ítem según corresponda.</p><input type="hidden" name="anamnesis_clinica" id="anamnesis_clinica" value=""></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Tabaco</label><textarea class="form-control form-control-sm" id="ac_tabaco" rows="2" placeholder="Ej: No fumador / 5 cig/día"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Alcohol</label><textarea class="form-control form-control-sm" id="ac_alcohol" rows="2" placeholder="Ej: Ocasional / No"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Drogas</label><textarea class="form-control form-control-sm" id="ac_drogas" rows="2" placeholder="Ej: No / Especificar"></textarea></div>
                        <div class="col-12 mb-2"><label class="form-label small mb-0">Enfermedad de base / RCV</label><textarea class="form-control form-control-sm" id="ac_enfermedad_base" rows="2" placeholder="Ej: HTA, DM2, dislipidemia, ninguno"></textarea></div>
                        <div class="col-12 mb-2"><label class="form-label small mb-0">Signos y síntomas</label><textarea class="form-control form-control-sm" id="ac_signos_sintomas" rows="2" placeholder="Ej: Cefaleas ocasionales, sin otros"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Tránsito intestinal / Bristol / diuresis</label><textarea class="form-control form-control-sm" id="ac_transito_bristol" rows="2" placeholder="Ej: Regular, Bristol 3-4"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Medicamentos</label><textarea class="form-control form-control-sm" id="ac_medicamentos" rows="2" placeholder="Ej: Metformina 850 mg c/12 h"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Suplementos</label><textarea class="form-control form-control-sm" id="ac_suplementos" rows="2" placeholder="Ej: Vit D, omega 3 / Ninguno"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Ingesta hídrica</label><textarea class="form-control form-control-sm" id="ac_ingesta_hidrica" rows="2" placeholder="Ej: 6-8 vasos/día"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Actividad física</label><textarea class="form-control form-control-sm" id="ac_actividad_fisica" rows="2" placeholder="Ej: 3 veces/semana, caminata"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Sueño</label><textarea class="form-control form-control-sm" id="ac_sueno" rows="2" placeholder="Ej: 6-7 h, insomnio ocasional"></textarea></div>
                        <div class="col-12 mb-2"><label class="form-label small mb-0">Otros / notas</label><textarea class="form-control form-control-sm" id="ac_otros" rows="3" placeholder="Cualquier dato adicional"></textarea></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Anamnesis Alimentaria</h6><p class="text-muted small mb-3">Relación familiar, quién cocina, apetito, relación con la comida, historia de peso y dietas.</p><input type="hidden" name="anamnesis_alimentaria" id="anamnesis_alimentaria" value=""></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Relación familiar / apoyo / recursos</label><textarea class="form-control form-control-sm" id="aa_relacion_familiar" rows="2" placeholder="Ej: Vive con pareja, apoyo en compras"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Quién cocina</label><textarea class="form-control form-control-sm" id="aa_quien_cocina" rows="2" placeholder="Ej: La paciente / Pareja / Delivery"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Apetito</label><textarea class="form-control form-control-sm" id="aa_apetito" rows="2" placeholder="Ej: Bueno / Variable / Bajo"></textarea></div>
                        <div class="col-12 mb-2"><label class="form-label small mb-0">Relación con la comida</label><textarea class="form-control form-control-sm" id="aa_relacion_comida" rows="2" placeholder="Ej: Come por ansiedad..."></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Historia de dietas restrictivas</label><textarea class="form-control form-control-sm" id="aa_dieta_restrictiva" rows="2" placeholder="Ej: Múltiples intentos, dieta keto 2024"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Historia de peso</label><textarea class="form-control form-control-sm" id="aa_historia_peso" rows="2" placeholder="Ej: Estable 5 años, sube en invierno"></textarea></div>
                        <div class="col-md-6 col-lg-4 mb-2"><label class="form-label small mb-0">Ansiedad con/sin comida</label><textarea class="form-control form-control-sm" id="aa_ansiedad_comida" rows="2" placeholder="Ej: Ansiedad en las tardes, picoteo nocturno"></textarea></div>
                        <div class="col-12 mb-2"><label class="form-label small mb-0">Otros / notas</label><textarea class="form-control form-control-sm" id="aa_otros" rows="3" placeholder="Cualquier dato adicional"></textarea></div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-info mb-3"><i class="fas fa-vial me-2"></i> Exámenes Bioquímicos</h6><input type="hidden" name="examenes_bioquimicos" id="examenes_bioquimicos_hidden" value=""></div>
                        <div class="col-12">
                            <div class="table-responsive mb-2">
                                <table class="table table-sm table-bordered" id="tablaExamenesBioquimicos"><thead class="table-light"><tr><th>Nombre</th><th>Valor</th><th>Fecha / Interpretación</th><th width="50"></th></tr></thead>
                                <tbody id="tbodyExamenesBioquimicos">
                                    <tr class="fila-examen"><td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td><td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td><td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td><td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td></tr>
                                </tbody></table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarExamen"><i class="fas fa-plus me-1"></i> Agregar examen</button>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-info mb-3"><i class="fas fa-apple-alt me-2"></i> Tendencia de Consumo / Preferencia alimentaria y Alergias</h6><input type="hidden" name="tendencia_consumo" id="tendencia_consumo" value=""></div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered" id="tablaTendenciaConsumo"><thead class="table-light"><tr><th class="align-middle">Tendencia de consumo</th><th colspan="1" class="text-center bg-success bg-opacity-25">Preferencia alimentaria</th><th colspan="1" class="text-center bg-danger bg-opacity-25">Alergias / Intolerancias</th></tr></thead>
                                <tbody id="tbodyTendenciaConsumo">
                                    <?php foreach (\App\Models\HistorialTendenciaConsumo::getGrupos() as $slug => $etiqueta): ?>
                                    <tr data-grupo="<?= esc($slug) ?>"><td class="align-middle fw-medium"><?= esc($etiqueta) ?></td><td class="p-1 bg-success bg-opacity-10"><textarea class="form-control form-control-sm tc-preferencia" rows="2" placeholder="Ej: Alto / Bajo / 0"></textarea></td><td class="p-1 bg-danger bg-opacity-10"><textarea class="form-control form-control-sm tc-alergia" rows="2" placeholder="Ej: Sí / No / 0"></textarea></td></tr>
                                    <?php endforeach; ?>
                                </tbody></table>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-12"><h6 class="text-info mb-3"><i class="fas fa-clock me-2"></i> Recordatorio 24 h</h6><p class="text-muted small">Desayuno, colación, almuerzo, once, cena: horarios y contenido.</p></div>
                        <div class="col-12"><textarea name="recordatorio_24h" id="recordatorio_24h" class="form-control" rows="5" placeholder="Ej: Desayuno 08:00: café con leche, pan integral..."></textarea></div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i> Guardar Consulta
                    </button>
                    <a href="<?= base_url('dashboard/historial/lista') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Antes de enviar: armar JSON de ficha de ingreso y poner en los hidden (igual que en editar)
function prepararFichaIngresoHistorial(form) {
    var acObj = { tabaco: ($('#ac_tabaco').val()||'').trim(), alcohol: ($('#ac_alcohol').val()||'').trim(), drogas: ($('#ac_drogas').val()||'').trim(), enfermedad_base: ($('#ac_enfermedad_base').val()||'').trim(), signos_sintomas: ($('#ac_signos_sintomas').val()||'').trim(), transito_bristol: ($('#ac_transito_bristol').val()||'').trim(), medicamentos: ($('#ac_medicamentos').val()||'').trim(), suplementos: ($('#ac_suplementos').val()||'').trim(), ingesta_hidrica: ($('#ac_ingesta_hidrica').val()||'').trim(), actividad_fisica: ($('#ac_actividad_fisica').val()||'').trim(), sueno: ($('#ac_sueno').val()||'').trim(), otros: ($('#ac_otros').val()||'').trim() };
    $('#anamnesis_clinica').val(JSON.stringify(acObj));
    var aaObj = { relacion_familiar: ($('#aa_relacion_familiar').val()||'').trim(), quien_cocina: ($('#aa_quien_cocina').val()||'').trim(), apetito: ($('#aa_apetito').val()||'').trim(), relacion_comida: ($('#aa_relacion_comida').val()||'').trim(), dieta_restrictiva: ($('#aa_dieta_restrictiva').val()||'').trim(), historia_peso: ($('#aa_historia_peso').val()||'').trim(), ansiedad_comida: ($('#aa_ansiedad_comida').val()||'').trim(), otros: ($('#aa_otros').val()||'').trim() };
    $('#anamnesis_alimentaria').val(JSON.stringify(aaObj));
    var tendenciaRows = []; $('#tbodyTendenciaConsumo tr[data-grupo]').each(function() { var grupo = $(this).data('grupo'); tendenciaRows.push({ grupo: grupo, preferencia: ($(this).find('.tc-preferencia').val()||'').trim(), alergia_intolerancia: ($(this).find('.tc-alergia').val()||'').trim() }); });
    $('#tendencia_consumo').val(JSON.stringify(tendenciaRows));
    var filasExamenes = []; $('#tbodyExamenesBioquimicos tr.fila-examen').each(function() { var $tr = $(this); filasExamenes.push({ nombre: ($tr.find('input[name="examen_nombre[]"]').val()||'').trim(), valor: ($tr.find('input[name="examen_valor[]"]').val()||'').trim(), fecha_interpretacion: ($tr.find('input[name="examen_fecha[]"]').val()||'').trim() }); });
    $('#examenes_bioquimicos_hidden').val(JSON.stringify(filasExamenes));
    return true;
}
$(document).on('click', '#btnAgregarExamen', function() {
    var fila = '<tr class="fila-examen"><td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td><td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td><td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td><td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td></tr>';
    $('#tbodyExamenesBioquimicos').append(fila);
});
$(document).on('click', '.btn-quitar-examen', function() {
    if ($('#tbodyExamenesBioquimicos tr.fila-examen').length > 1) $(this).closest('tr').remove();
});

$('#peso_actual, #altura_actual').on('input', function() {
    var peso = parseFloat($('#peso_actual').val());
    var altura = parseFloat($('#altura_actual').val());
    
    if (peso > 0 && altura > 0) {
        var alturaMetros = altura / 100;
        var imc = peso / (alturaMetros * alturaMetros);
        $('#imc_actual').val(imc.toFixed(2));
    } else {
        $('#imc_actual').val('');
    }
});

// Calcular suma de pliegues automáticamente (igual que en editar/consulta)
function calcularSumaPliegues() {
    var pliegues = [
        'pliegue_tricipital', 'pliegue_bicipital', 'pliegue_subescapular', 'pliegue_suprailíaco',
        'pliegue_supraespinal', 'pliegue_abdominal', 'pliegue_muslo_anterior', 'pliegue_pantorrilla_medial',
        'pliegue_pectoral', 'pliegue_axilar_medio', 'pliegue_muslo_medial'
    ];
    var suma = 0;
    pliegues.forEach(function(pliegue) {
        var valor = parseFloat($('input[name="' + pliegue + '"]').val());
        if (!isNaN(valor) && valor > 0) suma += valor;
    });
    $('input[name="suma_pliegues"]').val(suma > 0 ? suma.toFixed(2) : '');
}
$(document).ready(function() {
    $('input[name^="pliegue_"]').on('input', calcularSumaPliegues);
    calcularSumaPliegues();
});

// Inicializar Tagify para tags con autocompletado
$(document).ready(function() {
    if (typeof Tagify === 'undefined') {
        console.warn('Tagify no está disponible en esta vista. Los tags funcionarán como input normal.');
        return;
    }
    // Obtener tags sugeridos
    var tagsSugeridos = <?= json_encode(array_column($tags_sugeridos ?? [], 'tag_display')) ?>;
    
    var input = document.querySelector('input[name=tags]');
    if (!input) return;

    // Old tags (si vuelve con error de validación) — parse robusto
    var tagsRaw = <?= json_encode(old('tags', '')) ?>;
    function parseTagsRaw(raw) {
        var out = [];
        function pushVal(v) {
            if (v === null || v === undefined) return;
            if (typeof v === 'string') {
                var s = v.trim();
                if (!s) return;
                if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                    try { consume(JSON.parse(s)); return; } catch (e) {}
                }
                out.push(s); return;
            }
            if (Array.isArray(v)) { v.forEach(consume); return; }
            if (typeof v === 'object') { pushVal(v.value ?? v.tag ?? v.text ?? v.name ?? ''); return; }
        }
        function consume(v){ pushVal(v); }
        if (raw === null || raw === undefined) return [];
        if (typeof raw !== 'string') consume(raw);
        else {
            var s = raw.trim();
            if (!s) return [];
            if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                try { consume(JSON.parse(s)); } catch (e) { s.split(',').forEach(x => out.push(x.trim())); }
            } else s.split(',').forEach(x => out.push(x.trim()));
        }
        var uniq = {};
        return out.map(t => (t||'').toLowerCase().trim()).filter(Boolean).filter(t => (uniq[t] ? false : (uniq[t]=true)));
    }
    var tagsArray = parseTagsRaw(tagsRaw);

    window.__tagifyHistorialRegistro = new Tagify(input, {
        whitelist: tagsSugeridos,
        maxTags: 10,
        dropdown: {
            maxItems: 20,
            classname: 'tags-look',
            enabled: 1,
            closeOnSelect: false
        },
        transformTag: function(tagData) {
            tagData.value = tagData.value.toLowerCase().trim();
        }
    });

    if (tagsArray.length > 0) {
        window.__tagifyHistorialRegistro.addTags(tagsArray);
    }

    // Cargar tags sugeridos dinámicamente
    window.__tagifyHistorialRegistro.on('input', function(e) {
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
                window.__tagifyHistorialRegistro.settings.whitelist = whitelist;
                window.__tagifyHistorialRegistro.dropdown.show.call(window.__tagifyHistorialRegistro, value);
            }
        });
    });

    // Convertir tags a CSV al enviar el formulario (evita guardar JSON de Tagify en DB)
    $('form').on('submit', function() {
        try {
            var csv = (window.__tagifyHistorialRegistro.value || []).map(x => x.value).join(',');
            input.value = csv;
        } catch (e) {}
    });
});
</script>

<?= $this->endSection() ?>

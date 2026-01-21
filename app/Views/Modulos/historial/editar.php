<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/editar') ?>

<!-- Tagify para tags (necesario en esta vista) -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<!-- Chart.js (para gráficos de composición corporal y somatocarta) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-edit me-2"></i> Editar Consulta</h2>
                        <p style="color: white;">Edite la información de la consulta del historial clínico</p>
                    </div>
                    <a href="<?= base_url('dashboard/historial/lista') ?>" class="btn btn-light">
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

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    $errors = session()->getFlashdata('errors');
                    if (is_array($errors)) {
                        echo implode('<br>', $errors);
                    } else {
                        echo $errors;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dashboard/historial/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $historial->id ?>">
                
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
                                        <option value="<?= $paciente->id ?>" <?= ($historial->paciente_id == $paciente->id) ? 'selected' : '' ?>>
                                            <?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo <span class="text-danger">*</span></label>
                                <select name="tipo_registro" class="form-control" required>
                                    <option value="consulta" <?= ($historial->tipo_registro == 'consulta') ? 'selected' : '' ?>>Consulta</option>
                                    <option value="seguimiento" <?= ($historial->tipo_registro == 'seguimiento') ? 'selected' : '' ?>>Seguimiento</option>
                                    <option value="control" <?= ($historial->tipo_registro == 'control') ? 'selected' : '' ?>>Control</option>
                                    <option value="emergencia" <?= ($historial->tipo_registro == 'emergencia') ? 'selected' : '' ?>>Emergencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha_consulta" class="form-control" required 
                                       value="<?= old('fecha_consulta', $historial->fecha_consulta) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Hora</label>
                                <input type="time" name="hora_consulta" class="form-control" 
                                       value="<?= old('hora_consulta', $historial->hora_consulta) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Próxima Cita</label>
                                <input type="date" name="proxima_cita" class="form-control" 
                                       value="<?= old('proxima_cita', $historial->proxima_cita) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">2</span>
                        <span><i class="fas fa-weight icon-label"></i> Medidas Corporales</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" step="0.01" name="peso_actual" class="form-control" id="peso_actual" 
                                       value="<?= old('peso_actual', $historial->peso_actual) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Altura (cm)</label>
                                <input type="number" step="0.01" name="altura_actual" class="form-control" id="altura_actual" 
                                       value="<?= old('altura_actual', $historial->altura_actual) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Altura Sentado (cm)</label>
                                <input type="number" step="0.01" name="altura_sentado" class="form-control" 
                                       value="<?= old('altura_sentado', $historial->altura_sentado ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>IMC</label>
                                <input type="text" class="form-control" id="imc_actual" readonly 
                                       value="<?= old('imc_actual', $historial->imc_actual) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grasa Corporal (%)</label>
                                <input type="number" step="0.01" name="grasa_corporal" class="form-control" 
                                       value="<?= old('grasa_corporal', $historial->grasa_corporal) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Masa Muscular (kg)</label>
                                <input type="number" step="0.01" name="masa_muscular" class="form-control" 
                                       value="<?= old('masa_muscular', $historial->masa_muscular) ?>">
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
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control" 
                                       value="<?= old('circunferencia_cintura', $historial->circunferencia_cintura) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cadera</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control" 
                                       value="<?= old('circunferencia_cadera', $historial->circunferencia_cadera) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Relajado</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_relajado" class="form-control" 
                                       value="<?= old('circunferencia_brazo_relajado', $historial->circunferencia_brazo_relajado ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Contraído</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_contraido" class="form-control" 
                                       value="<?= old('circunferencia_brazo_contraido', $historial->circunferencia_brazo_contraido ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medio</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_medio" class="form-control" 
                                       value="<?= old('circunferencia_muslo_medio', $historial->circunferencia_muslo_medio ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla</label>
                                <input type="number" step="0.01" name="circunferencia_pantorrilla" class="form-control" 
                                       value="<?= old('circunferencia_pantorrilla', $historial->circunferencia_pantorrilla ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cuello</label>
                                <input type="number" step="0.01" name="circunferencia_cuello" class="form-control" 
                                       value="<?= old('circunferencia_cuello', $historial->circunferencia_cuello ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax</label>
                                <input type="number" step="0.01" name="circunferencia_torax" class="form-control" 
                                       value="<?= old('circunferencia_torax', $historial->circunferencia_torax ?? '') ?>">
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
                                <input type="number" step="0.01" name="diametro_biacromial" class="form-control" 
                                       value="<?= old('diametro_biacromial', $historial->diametro_biacromial ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bi-iliocristal</label>
                                <input type="number" step="0.01" name="diametro_bi_iliocristal" class="form-control" 
                                       value="<?= old('diametro_bi_iliocristal', $historial->diametro_bi_iliocristal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Húmero</label>
                                <input type="number" step="0.01" name="diametro_humero" class="form-control" 
                                       value="<?= old('diametro_humero', $historial->diametro_humero ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fémur</label>
                                <input type="number" step="0.01" name="diametro_femur" class="form-control" 
                                       value="<?= old('diametro_femur', $historial->diametro_femur ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="diametro_muneca" class="form-control" 
                                       value="<?= old('diametro_muneca', $historial->diametro_muneca ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tobillo</label>
                                <input type="number" step="0.01" name="diametro_tobillo" class="form-control" 
                                       value="<?= old('diametro_tobillo', $historial->diametro_tobillo ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-hand-paper me-2"></i> Pliegues Cutáneos (mm)</h6>
                            <p class="text-muted small mb-2">Medición con plicómetro. Se mide en milímetros (mm).</p>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tricipital</label>
                                <input type="number" step="0.01" name="pliegue_tricipital" class="form-control" 
                                       value="<?= old('pliegue_tricipital', $historial->pliegue_tricipital ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bicipital</label>
                                <input type="number" step="0.01" name="pliegue_bicipital" class="form-control" 
                                       value="<?= old('pliegue_bicipital', $historial->pliegue_bicipital ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subescapular</label>
                                <input type="number" step="0.01" name="pliegue_subescapular" class="form-control" 
                                       value="<?= old('pliegue_subescapular', $historial->pliegue_subescapular ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suprailíaco</label>
                                <input type="number" step="0.01" name="pliegue_suprailíaco" class="form-control" 
                                       value="<?= old('pliegue_suprailíaco', $historial->pliegue_suprailíaco ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Abdominal</label>
                                <input type="number" step="0.01" name="pliegue_abdominal" class="form-control" 
                                       value="<?= old('pliegue_abdominal', $historial->pliegue_abdominal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Anterior</label>
                                <input type="number" step="0.01" name="pliegue_muslo_anterior" class="form-control" 
                                       value="<?= old('pliegue_muslo_anterior', $historial->pliegue_muslo_anterior ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla Medial</label>
                                <input type="number" step="0.01" name="pliegue_pantorrilla_medial" class="form-control" 
                                       value="<?= old('pliegue_pantorrilla_medial', $historial->pliegue_pantorrilla_medial ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pectoral</label>
                                <input type="number" step="0.01" name="pliegue_pectoral" class="form-control" 
                                       value="<?= old('pliegue_pectoral', $historial->pliegue_pectoral ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Axilar Medio</label>
                                <input type="number" step="0.01" name="pliegue_axilar_medio" class="form-control" 
                                       value="<?= old('pliegue_axilar_medio', $historial->pliegue_axilar_medio ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medial</label>
                                <input type="number" step="0.01" name="pliegue_muslo_medial" class="form-control" 
                                       value="<?= old('pliegue_muslo_medial', $historial->pliegue_muslo_medial ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suma de Pliegues (mm)</label>
                                <input type="number" step="0.01" name="suma_pliegues" class="form-control" readonly
                                       value="<?= old('suma_pliegues', $historial->suma_pliegues ?? '') ?>">
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grasa Corporal Calculada (%)</label>
                                <input type="number" step="0.01" name="grasa_corporal_calculada" class="form-control" readonly
                                       value="<?= old('grasa_corporal_calculada', $historial->grasa_corporal_calculada ?? '') ?>">
                                <small class="text-muted">Se calcula a partir de los pliegues</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">3</span>
                        <span><i class="fas fa-file-medical icon-label"></i> Información Clínica</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Motivo de Consulta</label>
                                <textarea name="motivo_consulta" class="form-control" rows="3"><?= old('motivo_consulta', $historial->motivo_consulta) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Anamnesis</label>
                                <textarea name="anamnesis" class="form-control" rows="4"><?= old('anamnesis', $historial->anamnesis) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control" rows="3"><?= old('diagnostico', $historial->diagnostico) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Plan de Tratamiento</label>
                                <textarea name="plan_tratamiento" class="form-control" rows="4"><?= old('plan_tratamiento', $historial->plan_tratamiento) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Recomendaciones</label>
                                <textarea name="recomendaciones" class="form-control" rows="3"><?= old('recomendaciones', $historial->recomendaciones) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2"><?= old('observaciones', $historial->observaciones) ?></textarea>
                            </div>
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
                </div>

                <!-- Sección: Métodos de Cálculo de Composición Corporal -->
                <?php if (!empty($metodos_calculo ?? [])): ?>
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">4</span>
                        <span><i class="fas fa-calculator icon-label"></i> Métodos de Cálculo de Composición Corporal</span>
                    </div>
                    
                    <div class="row">
                        <?php foreach ($metodos_calculo as $metodo): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 <?= $metodo['disponible'] ? 'border-success' : 'border-secondary opacity-75' ?>">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="fas fa-<?= $metodo['disponible'] ? 'check-circle text-success' : 'lock text-secondary' ?> me-2"></i>
                                        <?= esc($metodo['nombre']) ?>
                                    </h5>
                                    <p class="card-text text-muted small"><?= esc($metodo['descripcion'] ?? '') ?></p>
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
                </div>
                <?php endif; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i> Actualizar Consulta
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
// Proteger ejecución si jQuery aún no está disponible (evita que se rompa todo el script)
if (typeof window.jQuery === 'undefined') {
    console.error('jQuery no está cargado: no se pueden inicializar cálculos ni botones de composición corporal.');
} else {
$(function () {
// Calcular IMC automáticamente
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

// Calcular suma de pliegues automáticamente
function calcularSumaPliegues() {
    var pliegues = [
        'pliegue_tricipital',
        'pliegue_bicipital',
        'pliegue_subescapular',
        'pliegue_suprailíaco',
        'pliegue_abdominal',
        'pliegue_muslo_anterior',
        'pliegue_pantorrilla_medial',
        'pliegue_pectoral',
        'pliegue_axilar_medio',
        'pliegue_muslo_medial'
    ];
    
    var suma = 0;
    pliegues.forEach(function(pliegue) {
        var valor = parseFloat($('input[name="' + pliegue + '"]').val());
        if (!isNaN(valor) && valor > 0) {
            suma += valor;
        }
    });
    
    if (suma > 0) {
        $('input[name="suma_pliegues"]').val(suma.toFixed(2));
        
        // Calcular grasa corporal aproximada
        var peso = parseFloat($('#peso_actual').val());
        var altura = parseFloat($('#altura_actual').val());
        
        if (peso > 0 && altura > 0) {
            // Fórmula mejorada basada en suma de pliegues y datos corporales
            var densidad = 1.112 - (0.00043499 * suma) + (0.00000055 * suma * suma) - (0.00028826 * altura);
            var grasaCalculada = ((4.95 / densidad) - 4.5) * 100;
            
            // Validar que el resultado sea razonable (entre 5% y 50%)
            if (grasaCalculada >= 5 && grasaCalculada <= 50) {
                $('input[name="grasa_corporal_calculada"]').val(grasaCalculada.toFixed(2));
            } else {
                // Si el resultado no es razonable, usar fórmula simplificada
                var grasaSimplificada = (suma * 0.5) + 5;
                $('input[name="grasa_corporal_calculada"]').val(grasaSimplificada.toFixed(2));
            }
        } else if (suma > 0) {
            var grasaSimplificada = (suma * 0.5) + 5;
            $('input[name="grasa_corporal_calculada"]').val(grasaSimplificada.toFixed(2));
        } else {
            $('input[name="grasa_corporal_calculada"]').val('');
        }
    } else {
        $('input[name="suma_pliegues"]').val('');
        $('input[name="grasa_corporal_calculada"]').val('');
    }
}

// Event listeners para cálculos automáticos
$(document).ready(function() {
    // Agregar listeners a todos los campos de pliegues
    $('input[name^="pliegue_"]').on('input', calcularSumaPliegues);
    
    // También recalcular cuando cambia el peso (necesario para grasa calculada)
    $('#peso_actual').on('input', calcularSumaPliegues);
    
    // Calcular suma inicial si hay valores
    calcularSumaPliegues();
});

// Inicializar Tagify para tags con autocompletado (no debe romper el resto si Tagify no carga)
$(document).ready(function() {
    if (typeof Tagify === 'undefined') {
        console.warn('Tagify no está disponible en esta vista. Los tags funcionarán como input normal.');
        return;
    }

    // Obtener tags sugeridos
    var tagsSugeridos = <?= json_encode(array_column($tags_sugeridos ?? [], 'tag_display')) ?>;
    
    // Cargar tags existentes/old() de forma robusta (soporta CSV, JSON, doble-JSON de Tagify)
    var tagsRaw = <?= json_encode(old('tags', $tags_string ?? '')) ?>;
    function parseTagsRaw(raw) {
        var out = [];
        function pushVal(v) {
            if (v === null || v === undefined) return;
            if (typeof v === 'string') {
                var s = v.trim();
                if (!s) return;
                // Si viene un JSON como string, intentar parsear
                if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                    try {
                        var parsed = JSON.parse(s);
                        consume(parsed);
                        return;
                    } catch (e) {
                        // seguir como string normal
                    }
                }
                out.push(s);
                return;
            }
            if (Array.isArray(v)) {
                v.forEach(consume);
                return;
            }
            if (typeof v === 'object') {
                pushVal(v.value ?? v.tag ?? v.text ?? v.name ?? '');
                return;
            }
        }
        function consume(v) {
            pushVal(v);
        }

        if (raw === null || raw === undefined) return [];
        if (typeof raw !== 'string') {
            consume(raw);
        } else {
            var s = raw.trim();
            if (!s) return [];
            // Intentar JSON primero
            if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                try {
                    consume(JSON.parse(s));
                } catch (e) {
                    // fallback CSV
                    s.split(',').forEach(function(x){ out.push(x.trim()); });
                }
            } else {
                s.split(',').forEach(function(x){ out.push(x.trim()); });
            }
        }

        // Normalizar: minúsculas, trim, únicos
        var uniq = {};
        return out
            .map(function(t){ return (t || '').toLowerCase().trim(); })
            .filter(Boolean)
            .filter(function(t){ if (uniq[t]) return false; uniq[t] = true; return true; });
    }
    var tagsArray = parseTagsRaw(tagsRaw);
    
    var input = document.querySelector('input[name=tags]');
    if (!input) return;

    window.__tagifyHistorial = new Tagify(input, {
        whitelist: tagsSugeridos,
        maxTags: 10,
        dropdown: {
            maxItems: 20,
            classname: 'tags-look',
            enabled: 1,
            closeOnSelect: false
        },
        transformTag: function(tagData) {
            tagData.value = (tagData.value || '').toLowerCase().trim();
        }
    });

    // Cargar tags existentes
    if (tagsArray.length > 0) {
        window.__tagifyHistorial.addTags(tagsArray);
    }

    // Cargar tags sugeridos dinámicamente
    window.__tagifyHistorial.on('input', function(e) {
        var value = e.detail.value;
        if (!value || value.length < 1) return;
        
        $.ajax({
            url: '<?= base_url('dashboard/historial/getTagsSugeridos') ?>',
            dataType: 'json',
            data: { q: value },
            success: function(data) {
                var whitelist = (data.results || []).map(function(item) {
                    return item.text;
                });
                window.__tagifyHistorial.settings.whitelist = whitelist;
                window.__tagifyHistorial.dropdown.show.call(window.__tagifyHistorial, value);
            }
        });
    });

    // Antes de enviar el formulario, convertir a CSV para que el backend NO reciba JSON de Tagify
    var $form = $('form[action$="dashboard/historial/update"]');
    if ($form.length) {
        $form.on('submit', function() {
            try {
                var csv = (window.__tagifyHistorial.value || []).map(function(x){ return x.value; }).join(',');
                input.value = csv;
            } catch (e) {}
        });
    }
});

// Manejar clicks en botones de cálculo (delegado, por si el DOM cambia)
$(document).ready(function() {
    // CSRF dinámico (CI4 regenera token en cada POST cuando Security::$regenerate=true)
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';
    
    function setCsrfHash(newHash) {
        if (!newHash) return;
        csrfHash = newHash;
        // Actualizar el hidden input del form principal para evitar 403 al "Actualizar Consulta"
        var $csrfInput = $('input[name="' + csrfName + '"]');
        if ($csrfInput.length) {
            $csrfInput.val(csrfHash);
        }
    }

    $(document).on('click', '.calcular-metodo', function() {
        console.log('[Composición] Click calcular-metodo', this);
        var metodoSlug = $(this).data('metodo');
        var metodoNombre = $(this).data('nombre');
        var historialId = <?= $historial->id ?? 0 ?>;
        
        if (!historialId) {
            alert('Error: No se pudo obtener el ID del historial');
            return;
        }
        
        // Mostrar loading
        var $btn = $(this);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Calculando...');
        
        // Llamar al endpoint de cálculo
        var postData = { historial_id: historialId };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: '<?= base_url('dashboard/historial/calcular-') ?>' + metodoSlug,
            type: 'POST',
            data: postData,
            dataType: 'json',
            beforeSend: function(xhr) {
                // Enviar también por header (config Security::$headerName = X-CSRF-TOKEN)
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfHash);
            },
            complete: function(xhr) {
                // Si el backend envía el token nuevo por header, sincronizarlo
                try {
                    var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                    if (headerToken) setCsrfHash(headerToken);
                } catch (e) {}
            },
            success: function(response) {
                $btn.prop('disabled', false).html(originalHtml);

                // Actualizar CSRF si viene en respuesta/header
                if (response && response.csrf_hash) setCsrfHash(response.csrf_hash);
                // (jQuery no expone el xhr acá sin usar complete; se actualiza en error/response JSON)
                
                if (response.success) {
                    // Mostrar resultados en modal
                    mostrarResultadosCalculo(metodoNombre, response.resultado, response.metodo);
                } else {
                    // Mostrar error
                    if (response.requiere_upgrade) {
                        mostrarErrorUpgrade(response);
                    } else {
                        alert('Error: ' + (response.error || response.message || 'Error desconocido'));
                    }
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);
                var errorMsg = 'Error al calcular';
                if (xhr.responseJSON) {
                    errorMsg = xhr.responseJSON.error || xhr.responseJSON.message || errorMsg;
                    if (xhr.responseJSON.requiere_upgrade) {
                        mostrarErrorUpgrade(xhr.responseJSON);
                        return;
                    }
                    if (xhr.responseJSON.csrf_hash) {
                        setCsrfHash(xhr.responseJSON.csrf_hash);
                    }
                }
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) setCsrfHash(headerToken);
                alert(errorMsg);
            }
        });
    });
});

// Función para mostrar resultados en modal
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
    
    // Remover modal anterior si existe
    $('#modalResultadosCalculo').remove();
    
    // Agregar modal al body
    $('body').append(modalHtml);
    
    // Generar contenido de resultados
    var contenido = '<div class="table-responsive"><table class="table table-bordered table-striped">';
    
    // Manejar estructura anidada del servicio
    if (resultado.componentes) {
        // Estructura nueva (anidada)
        if (resultado.componentes.masa_adiposa) {
            contenido += `
                <tr><th>Masa Adiposa</th><td><strong>${resultado.componentes.masa_adiposa.kg} kg</strong> (${resultado.componentes.masa_adiposa.porcentaje}%)</td></tr>
                <tr><th>Masa Magra</th><td><strong>${resultado.componentes.masa_magra.kg} kg</strong> (${resultado.componentes.masa_magra.porcentaje}%)</td></tr>
            `;
        }
        if (resultado.componentes.grasa) {
            contenido += `
                <tr><th>Grasa</th><td><strong>${resultado.componentes.grasa.kg} kg</strong></td></tr>
                <tr><th>Músculo</th><td><strong>${resultado.componentes.musculo.kg} kg</strong></td></tr>
                <tr><th>Hueso</th><td><strong>${resultado.componentes.hueso.kg} kg</strong></td></tr>
                <tr><th>Residual</th><td><strong>${resultado.componentes.residual.kg} kg</strong></td></tr>
            `;
        }
        if (resultado.componentes.piel) {
            contenido += `<tr><th>Piel</th><td><strong>${resultado.componentes.piel.kg} kg</strong></td></tr>`;
        }
        if (resultado.componentes.endomorfia !== undefined) {
            contenido += `
                <tr><th>Endomorfia</th><td><strong>${resultado.componentes.endomorfia}</strong></td></tr>
                <tr><th>Mesomorfia</th><td><strong>${resultado.componentes.mesomorfia}</strong></td></tr>
                <tr><th>Ectomorfia</th><td><strong>${resultado.componentes.ectomorfia}</strong></td></tr>
            `;
        }
    } else {
        // Estructura plana (compatibilidad)
        if (resultado.masa_adiposa_kg !== undefined) {
            contenido += `
                <tr><th>Masa Adiposa</th><td><strong>${resultado.masa_adiposa_kg} kg</strong> (${resultado.masa_adiposa_porcentaje}%)</td></tr>
                <tr><th>Masa Magra</th><td><strong>${resultado.masa_magra_kg} kg</strong> (${resultado.masa_magra_porcentaje}%)</td></tr>
            `;
        }
        if (resultado.grasa_kg !== undefined) {
            contenido += `
                <tr><th>Grasa</th><td><strong>${resultado.grasa_kg} kg</strong></td></tr>
                <tr><th>Músculo</th><td><strong>${resultado.musculo_kg} kg</strong></td></tr>
                <tr><th>Hueso</th><td><strong>${resultado.hueso_kg} kg</strong></td></tr>
                <tr><th>Residual</th><td><strong>${resultado.residual_kg} kg</strong></td></tr>
            `;
        }
        if (resultado.piel_kg !== undefined) {
            contenido += `<tr><th>Piel</th><td><strong>${resultado.piel_kg} kg</strong></td></tr>`;
        }
        if (resultado.endomorfia !== undefined) {
            contenido += `
                <tr><th>Endomorfia</th><td><strong>${resultado.endomorfia}</strong></td></tr>
                <tr><th>Mesomorfia</th><td><strong>${resultado.mesomorfia}</strong></td></tr>
                <tr><th>Ectomorfia</th><td><strong>${resultado.ectomorfia}</strong></td></tr>
            `;
        }
    }
    
    contenido += '</table></div>';
    
    // Agregar información adicional si está disponible
    if (resultado.datos_usados) {
        contenido += '<div class="mt-3"><small class="text-muted"><strong>Datos utilizados:</strong> ';
        var datosUsados = [];
        if (resultado.datos_usados.peso) datosUsados.push('Peso: ' + resultado.datos_usados.peso + ' kg');
        if (resultado.datos_usados.altura) datosUsados.push('Altura: ' + resultado.datos_usados.altura + ' cm');
        if (resultado.datos_usados.suma_pliegues) datosUsados.push('Suma pliegues: ' + resultado.datos_usados.suma_pliegues + ' mm');
        contenido += datosUsados.join(', ') + '</small></div>';
    }
    
    $('#contenidoResultados').html(contenido);
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('modalResultadosCalculo'));
    modal.show();

    // Renderizar gráficos (si corresponde)
    setTimeout(function () {
        try {
            renderizarGraficosResultados(resultado);
        } catch (e) {
            console.warn('No se pudieron renderizar los gráficos:', e);
        }
    }, 150);
}

// Charts globales para poder destruirlos entre aperturas
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

    // Reset visual
    $('#bloqueGraficos').hide();
    $('#bloqueGraficoComposicion').hide();
    $('#bloqueGraficoSomato').hide();
    destruirChartSiExiste('__chartComposicion');
    destruirChartSiExiste('__chartSomatocarta');

    // 1) Composición: donut con kg
    var comp = resultado && resultado.componentes ? resultado.componentes : null;
    if (comp && (comp.grasa || comp.musculo || comp.hueso || comp.residual || comp.piel)) {
        var labels = [];
        var values = [];

        if (comp.grasa && comp.grasa.kg != null) { labels.push('Grasa'); values.push(Number(comp.grasa.kg)); }
        if (comp.musculo && comp.musculo.kg != null) { labels.push('Músculo'); values.push(Number(comp.musculo.kg)); }
        if (comp.hueso && comp.hueso.kg != null) { labels.push('Hueso'); values.push(Number(comp.hueso.kg)); }
        if (comp.residual && comp.residual.kg != null) { labels.push('Residual'); values.push(Number(comp.residual.kg)); }
        if (comp.piel && comp.piel.kg != null) { labels.push('Piel'); values.push(Number(comp.piel.kg)); }

        // Filtrar valores no numéricos
        var filtered = labels.map((l, i) => ({ l, v: values[i] })).filter(x => !isNaN(x.v) && isFinite(x.v));
        labels = filtered.map(x => x.l);
        values = filtered.map(x => x.v);

        if (values.length > 0) {
            $('#bloqueGraficos').show();
            $('#bloqueGraficoComposicion').show();
            var ctx = document.getElementById('chartComposicion');
            if (ctx) {
                window.__chartComposicion = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: ['#f59e0b', '#3b82f6', '#64748b', '#94a3b8', '#22c55e'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed} kg`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }

    // 2) Somatocarta: scatter (Heath-Carter)
    if (comp && comp.endomorfia !== undefined && comp.mesomorfia !== undefined && comp.ectomorfia !== undefined) {
        var endo = Number(comp.endomorfia);
        var meso = Number(comp.mesomorfia);
        var ecto = Number(comp.ectomorfia);
        if (!isNaN(endo) && !isNaN(meso) && !isNaN(ecto)) {
            var x = ecto - endo;
            var y = (2 * meso) - (endo + ecto);

            $('#bloqueGraficos').show();
            $('#bloqueGraficoSomato').show();
            var ctx2 = document.getElementById('chartSomatocarta');
            if (ctx2) {
                // Auto-ajustar ejes para que el punto siempre sea visible (con margen)
                var pad = 2;
                var minX = Math.floor(Math.min(-10, x - pad));
                var maxX = Math.ceil(Math.max(10, x + pad));
                var minY = Math.floor(Math.min(-10, y - pad));
                var maxY = Math.ceil(Math.max(10, y + pad));

                window.__chartSomatocarta = new Chart(ctx2, {
                    type: 'scatter',
                    data: {
                        datasets: [{
                            label: 'Somatotipo',
                            data: [{ x: x, y: y }],
                            pointBackgroundColor: '#ef4444',
                            pointRadius: 7,
                            pointHoverRadius: 9
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { type: 'linear', min: minX, max: maxX, grid: { color: '#e5e7eb' }, title: { display: true, text: 'X (Ecto - Endo)' } },
                            y: { type: 'linear', min: minY, max: maxY, grid: { color: '#e5e7eb' }, title: { display: true, text: 'Y (2·Meso - Endo - Ecto)' } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `X=${context.parsed.x.toFixed(2)}, Y=${context.parsed.y.toFixed(2)}`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
}

// Función para mostrar error de upgrade
function mostrarErrorUpgrade(response) {
    var modalHtml = `
        <div class="modal fade" id="modalErrorUpgrade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-lock me-2"></i>Método No Disponible
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Este método de cálculo no está disponible en tu plan actual (<strong>${response.paquete_actual}</strong>).</p>
                        <p>Para acceder a este método, necesitas actualizar tu plan.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>¿Interesado en actualizar?</strong> Contacta con soporte para más información sobre nuestros planes.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="window.location.href='<?= base_url('dashboard/paquetes') ?>'">
                            Ver Planes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#modalErrorUpgrade').remove();
    $('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('modalErrorUpgrade'));
    modal.show();
}

// Función para imprimir resultados
function imprimirResultados() {
    window.print();
}
}); // $(function(){ ... })
} // if jQuery
</script>

<?= $this->endSection() ?>

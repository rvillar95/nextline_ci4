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
    
    /* Leyenda de métodos de cálculo (igual que en Agenda/Consulta) */
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

            <form action="<?= base_url('dashboard/historial/update') ?>" method="post" id="formHistorialEditar" onsubmit="return prepararFichaIngresoHistorial(this)">
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
                    
                    <!-- Leyenda de Métodos de Cálculo (igual que en Agenda/Consulta) -->
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
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('circunferencia_cintura', $historial->circunferencia_cintura) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cadera</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('circunferencia_cadera', $historial->circunferencia_cadera) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Relajado</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_relajado" class="form-control metodo-4 metodo-5 metodo-2" 
                                       value="<?= old('circunferencia_brazo_relajado', $historial->circunferencia_brazo_relajado ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Contraído</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_contraido" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('circunferencia_brazo_contraido', $historial->circunferencia_brazo_contraido ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medio</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_medio" class="form-control metodo-4" 
                                       value="<?= old('circunferencia_muslo_medio', $historial->circunferencia_muslo_medio ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla</label>
                                <input type="number" step="0.01" name="circunferencia_pantorrilla" class="form-control metodo-4 metodo-5 metodo-2" 
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
                                <input type="number" step="0.01" name="circunferencia_torax" class="form-control metodo-5 metodo-2" 
                                       value="<?= old('circunferencia_torax', $historial->circunferencia_torax ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cabeza</label>
                                <input type="number" step="0.01" name="circunferencia_cabeza" class="form-control metodo-5" 
                                       value="<?= old('circunferencia_cabeza', $historial->circunferencia_cabeza ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Antebrazo Máximo</label>
                                <input type="number" step="0.01" name="circunferencia_antebrazo_maximo" class="form-control metodo-5 metodo-2" 
                                       value="<?= old('circunferencia_antebrazo_maximo', $historial->circunferencia_antebrazo_maximo ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Máximo</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_maximo" class="form-control metodo-5 metodo-2" 
                                       value="<?= old('circunferencia_muslo_maximo', $historial->circunferencia_muslo_maximo ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="circunferencia_muneca" class="form-control metodo-4" 
                                       value="<?= old('circunferencia_muneca', $historial->circunferencia_muneca ?? '') ?>">
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
                                <input type="number" step="0.01" name="diametro_biacromial" class="form-control metodo-5" 
                                       value="<?= old('diametro_biacromial', $historial->diametro_biacromial ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bi-iliocristal</label>
                                <input type="number" step="0.01" name="diametro_bi_iliocristal" class="form-control metodo-5" 
                                       value="<?= old('diametro_bi_iliocristal', $historial->diametro_bi_iliocristal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Húmero</label>
                                <input type="number" step="0.01" name="diametro_humero" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('diametro_humero', $historial->diametro_humero ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fémur</label>
                                <input type="number" step="0.01" name="diametro_femur" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('diametro_femur', $historial->diametro_femur ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="diametro_muneca" class="form-control metodo-4" 
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax Transverso</label>
                                <input type="number" step="0.01" name="diametro_torax_transverso" class="form-control metodo-5" 
                                       value="<?= old('diametro_torax_transverso', $historial->diametro_torax_transverso ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tórax Anteroposterior</label>
                                <input type="number" step="0.01" name="diametro_torax_anteroposterior" class="form-control metodo-5" 
                                       value="<?= old('diametro_torax_anteroposterior', $historial->diametro_torax_anteroposterior ?? '') ?>">
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
                                <input type="number" step="0.01" name="pliegue_tricipital" class="form-control metodo-4 metodo-5 metodo-2" 
                                       value="<?= old('pliegue_tricipital', $historial->pliegue_tricipital ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bicipital</label>
                                <input type="number" step="0.01" name="pliegue_bicipital" class="form-control metodo-4" 
                                       value="<?= old('pliegue_bicipital', $historial->pliegue_bicipital ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Subescapular</label>
                                <input type="number" step="0.01" name="pliegue_subescapular" class="form-control metodo-4 metodo-5 metodo-2" 
                                       value="<?= old('pliegue_subescapular', $historial->pliegue_subescapular ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suprailíaco</label>
                                <input type="number" step="0.01" name="pliegue_suprailíaco" class="form-control metodo-4 metodo-5" 
                                       value="<?= old('pliegue_suprailíaco', $historial->pliegue_suprailíaco ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Supraespinal</label>
                                <input type="number" step="0.01" name="pliegue_supraespinal" class="form-control metodo-2 metodo-5" 
                                       value="<?= old('pliegue_supraespinal', $historial->pliegue_supraespinal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Abdominal</label>
                                <input type="number" step="0.01" name="pliegue_abdominal" class="form-control metodo-4 metodo-5 metodo-2" 
                                       value="<?= old('pliegue_abdominal', $historial->pliegue_abdominal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Anterior</label>
                                <input type="number" step="0.01" name="pliegue_muslo_anterior" class="form-control metodo-4" 
                                       value="<?= old('pliegue_muslo_anterior', $historial->pliegue_muslo_anterior ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla Medial</label>
                                <input type="number" step="0.01" name="pliegue_pantorrilla_medial" class="form-control metodo-4 metodo-5 metodo-2" 
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
                                <input type="number" step="0.01" name="pliegue_muslo_medial" class="form-control metodo-5 metodo-2" 
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

                    <!-- Ficha de Ingreso: Anamnesis clínica (estructurada) -->
                    <div class="row mb-4 mt-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-notes-medical me-2"></i> Anamnesis Clínica</h6>
                            <p class="text-muted small mb-3">Completar cada ítem según corresponda.</p>
                            <input type="hidden" name="anamnesis_clinica" id="anamnesis_clinica" value="">
                        </div>
                        <?php
                        $ac = [];
                        if (!empty($historial->anamnesis_clinica)) {
                            $ac = json_decode($historial->anamnesis_clinica, true) ?: [];
                        }
                        $ac = is_array($ac) ? $ac : [];
                        ?>
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
                            <textarea class="form-control form-control-sm" id="ac_otros" rows="3" placeholder="Cualquier dato adicional"><?= esc($ac['otros'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <!-- Anamnesis alimentaria (estructurada) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Anamnesis Alimentaria</h6>
                            <p class="text-muted small mb-3">Relación familiar, quién cocina, apetito, relación con la comida, historia de peso y dietas.</p>
                            <input type="hidden" name="anamnesis_alimentaria" id="anamnesis_alimentaria" value="">
                        </div>
                        <?php
                        $aa = [];
                        if (!empty($historial->anamnesis_alimentaria)) {
                            $aa = json_decode($historial->anamnesis_alimentaria, true) ?: [];
                        }
                        $aa = is_array($aa) ? $aa : [];
                        ?>
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
                            <textarea class="form-control form-control-sm" id="aa_relacion_comida" rows="2" placeholder="Ej: Come por ansiedad..."><?= esc($aa['relacion_comida'] ?? '') ?></textarea>
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
                            <textarea class="form-control form-control-sm" id="aa_otros" rows="3" placeholder="Cualquier dato adicional"><?= esc($aa['otros'] ?? '') ?></textarea>
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
                                        <tr><th>Nombre</th><th>Valor</th><th>Fecha / Interpretación</th><th width="50"></th></tr>
                                    </thead>
                                    <tbody id="tbodyExamenesBioquimicos">
                                        <?php foreach ($examenes_bioquimicos ?? [] as $ex): ?>
                                        <tr class="fila-examen">
                                            <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia" value="<?= esc($ex->nombre ?? '') ?>"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95" value="<?= esc($ex->valor ?? '') ?>"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal" value="<?= esc($ex->fecha_interpretacion ?? '') ?>"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="fila-examen">
                                            <td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td>
                                            <td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarExamen"><i class="fas fa-plus me-1"></i> Agregar examen</button>
                        </div>
                    </div>

                    <!-- Tendencia de consumo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-info mb-3"><i class="fas fa-apple-alt me-2"></i> Tendencia de Consumo / Preferencia alimentaria y Alergias</h6>
                            <p class="text-muted small mb-3">Completar preferencia y/o alergia/intolerancia por grupo.</p>
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
                            <textarea name="recordatorio_24h" id="recordatorio_24h" class="form-control" rows="5" placeholder="Ej: Desayuno 08:00: café con leche, pan integral..."><?= old('recordatorio_24h', $historial->recordatorio_24h ?? '') ?></textarea>
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
// Antes de enviar el formulario: armar JSON de ficha de ingreso y poner en los hidden
function prepararFichaIngresoHistorial(form) {
    var acObj = {
        tabaco: ($('#ac_tabaco').val() || '').trim(),
        alcohol: ($('#ac_alcohol').val() || '').trim(),
        drogas: ($('#ac_drogas').val() || '').trim(),
        enfermedad_base: ($('#ac_enfermedad_base').val() || '').trim(),
        signos_sintomas: ($('#ac_signos_sintomas').val() || '').trim(),
        transito_bristol: ($('#ac_transito_bristol').val() || '').trim(),
        medicamentos: ($('#ac_medicamentos').val() || '').trim(),
        suplementos: ($('#ac_suplementos').val() || '').trim(),
        ingesta_hidrica: ($('#ac_ingesta_hidrica').val() || '').trim(),
        actividad_fisica: ($('#ac_actividad_fisica').val() || '').trim(),
        sueno: ($('#ac_sueno').val() || '').trim(),
        otros: ($('#ac_otros').val() || '').trim()
    };
    $('#anamnesis_clinica').val(JSON.stringify(acObj));
    var aaObj = {
        relacion_familiar: ($('#aa_relacion_familiar').val() || '').trim(),
        quien_cocina: ($('#aa_quien_cocina').val() || '').trim(),
        apetito: ($('#aa_apetito').val() || '').trim(),
        relacion_comida: ($('#aa_relacion_comida').val() || '').trim(),
        dieta_restrictiva: ($('#aa_dieta_restrictiva').val() || '').trim(),
        historia_peso: ($('#aa_historia_peso').val() || '').trim(),
        ansiedad_comida: ($('#aa_ansiedad_comida').val() || '').trim(),
        otros: ($('#aa_otros').val() || '').trim()
    };
    $('#anamnesis_alimentaria').val(JSON.stringify(aaObj));
    var tendenciaRows = [];
    $('#tbodyTendenciaConsumo tr[data-grupo]').each(function() {
        var grupo = $(this).data('grupo');
        var preferencia = $(this).find('.tc-preferencia').val() || '';
        var alergia = $(this).find('.tc-alergia').val() || '';
        tendenciaRows.push({ grupo: grupo, preferencia: preferencia, alergia_intolerancia: alergia });
    });
    $('#tendencia_consumo').val(JSON.stringify(tendenciaRows));
    var filasExamenes = [];
    $('#tbodyExamenesBioquimicos tr.fila-examen').each(function() {
        var $tr = $(this);
        filasExamenes.push({
            nombre: ($tr.find('input[name="examen_nombre[]"]').val() || '').trim(),
            valor: ($tr.find('input[name="examen_valor[]"]').val() || '').trim(),
            fecha_interpretacion: ($tr.find('input[name="examen_fecha[]"]').val() || '').trim()
        });
    });
    $('#examenes_bioquimicos_hidden').val(JSON.stringify(filasExamenes));
    return true;
}

// Proteger ejecución si jQuery aún no está disponible (evita que se rompa todo el script)
if (typeof window.jQuery === 'undefined') {
    console.error('jQuery no está cargado: no se pueden inicializar cálculos ni botones de composición corporal.');
} else {
$(function () {
// Ficha de ingreso: agregar / quitar fila de exámenes bioquímicos
$(document).on('click', '#btnAgregarExamen', function() {
    var fila = '<tr class="fila-examen"><td><input type="text" class="form-control form-control-sm" name="examen_nombre[]" placeholder="Ej: Glicemia"></td><td><input type="text" class="form-control form-control-sm" name="examen_valor[]" placeholder="Ej: 95"></td><td><input type="text" class="form-control form-control-sm" name="examen_fecha[]" placeholder="Ej: 15-01-2026 / Normal"></td><td><button type="button" class="btn btn-sm btn-outline-danger btn-quitar-examen" title="Quitar"><i class="fas fa-times"></i></button></td></tr>';
    $('#tbodyExamenesBioquimicos').append(fila);
});
$(document).on('click', '.btn-quitar-examen', function() {
    if ($('#tbodyExamenesBioquimicos tr.fila-examen').length > 1) $(this).closest('tr').remove();
});

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

        // Validación rápida de unidades (evita enviar datos claramente erróneos y terminar en MMUSC <= 0)
        if (metodoSlug === '5-componentes') {
            var issues = validarUnidadesHolway5C();
            if (issues.length) {
                mostrarModalUnidadesSospechosas(issues);
                return;
            }
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
                    if (xhr.responseJSON.error === 'Datos insuficientes' && xhr.responseJSON.faltantes && xhr.responseJSON.faltantes.length) {
                        mostrarModalDatosInsuficientes(xhr.responseJSON);
                        return;
                    }
                    if (xhr.responseJSON.csrf_hash) {
                        setCsrfHash(xhr.responseJSON.csrf_hash);
                    }
                }
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) setCsrfHash(headerToken);
                mostrarModalErrorCalculo(xhr.responseJSON || { error: 'Error al calcular', message: errorMsg, metodo: metodoSlug });
            }
        });
    });
});

// Modal genérico para errores de cálculo (evita alert y muestra detalle clínico)
function mostrarModalErrorCalculo(payload) {
    var titulo = (payload && payload.error) ? payload.error : 'Error al calcular';
    var mensaje = (payload && payload.message) ? payload.message : 'Ocurrió un error inesperado.';
    var metodo = (payload && payload.metodo) ? payload.metodo : null;
    var warnings = (payload && Array.isArray(payload.warnings)) ? payload.warnings : [];

    var modalHtml = `
        <div class="modal fade" id="modalErrorCalculo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>${titulo}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ${metodo ? `<div class="small text-muted mb-2">Método: <strong>${metodo}</strong></div>` : ''}
                        ${warnings.length ? `<div class="alert alert-warning border small"><strong>Revisar:</strong><ul class="mb-0">${warnings.map(w => `<li>${w}</li>`).join('')}</ul></div>` : ''}
                        <div class="alert alert-light border">
                            <div class="fw-semibold mb-1">Detalle</div>
                            <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word;">${mensaje}</pre>
                        </div>
                        <div class="small text-muted">
                            Si el error menciona perímetros/diámetros “muy bajos”, revisá que estén cargados en <strong>cm</strong> (no pulgadas) y que los pliegues estén en <strong>mm</strong>.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#modalErrorCalculo').remove();
    $('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('modalErrorCalculo'));
    modal.show();
}

// Validación de unidades para Holway 5C (rangos típicos en cm/mm)
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

    // Perímetros (cm) usados en MMUSC: si están demasiado bajos suelen ser pulgadas o placeholders (ej. 20.00)
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

    // Pliegues (mm) usados: si están en cm por error salen muy bajos
    ['pliegue_tricipital','pliegue_subescapular','pliegue_supraespinal','pliegue_abdominal','pliegue_muslo_medial','pliegue_pantorrilla_medial'].forEach(function(nm){
        var v = numByName(nm);
        pushIf(nm, v, v > 0 && v < 1, 'Pliegues se ingresan en mm (no cm).');
    });

    return issues;
}

function mostrarModalUnidadesSospechosas(issues) {
    var rows = issues.map(function(it){
        var etiqueta = (window.etiquetasCamposComposicion && etiquetasCamposComposicion[it.campo]) ? etiquetasCamposComposicion[it.campo] : it.campo;
        return '<tr><th>' + etiqueta + '</th><td><strong>' + it.valor + '</strong></td><td class="text-muted small">' + (it.hint || '') + '</td></tr>';
    }).join('');

    var modalHtml = `
        <div class="modal fade" id="modalUnidadesSospechosas" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title"><i class="fas fa-ruler-combined me-2"></i>Unidades sospechosas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-2">Antes de calcular 5 componentes (Holway), revisá estos valores. Si están en pulgadas o son placeholders (ej. 20,00), el cálculo muscular puede dar <strong>MMUSC ≤ 0</strong>.</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>Campo</th><th>Valor</th><th>Recomendación</th></tr></thead>
                                <tbody>${rows}</tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#modalUnidadesSospechosas').remove();
    $('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('modalUnidadesSospechosas'));
    modal.show();
}

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
    var contenido = '';

    // Warnings (unidades / rangos)
    if (resultado && Array.isArray(resultado.warnings) && resultado.warnings.length) {
        contenido += '<div class="alert alert-warning border small mb-3"><strong>Revisar:</strong><ul class="mb-0">' +
            resultado.warnings.map(function(w){ return '<li>' + w + '</li>'; }).join('') +
            '</ul></div>';
    }

    contenido += '<div class="table-responsive"><table class="table table-bordered table-striped">';
    
    // Manejar estructura anidada del servicio
    if (resultado.componentes) {
        // Estructura nueva (anidada)
        var esHolway5C = !!(resultado.componentes.masa_muscular || resultado.componentes.masa_residual || resultado.componentes.masa_osea_total || resultado.componentes.masa_piel || resultado.componentes.masa_total);

        // 2 componentes (masa adiposa + masa magra)
        if (resultado.componentes.masa_adiposa) {
            contenido += `<tr><th>Masa Adiposa</th><td><strong>${resultado.componentes.masa_adiposa.kg} kg</strong>${(resultado.componentes.masa_adiposa.porcentaje != null ? ' (' + resultado.componentes.masa_adiposa.porcentaje + '%)' : '')}</td></tr>`;
        }
        if (resultado.componentes.masa_magra) {
            contenido += `<tr><th>Masa Magra</th><td><strong>${resultado.componentes.masa_magra.kg} kg</strong>${(resultado.componentes.masa_magra.porcentaje != null ? ' (' + resultado.componentes.masa_magra.porcentaje + '%)' : '')}</td></tr>`;
        }

        // 5 componentes Holway (salida solicitada + desglose óseo + masa total)
        if (esHolway5C) {
            if (resultado.componentes.masa_muscular) {
                contenido += `<tr><th>Masa Muscular</th><td><strong>${resultado.componentes.masa_muscular.kg} kg</strong>${(resultado.componentes.masa_muscular.porcentaje != null ? ' (' + resultado.componentes.masa_muscular.porcentaje + '%)' : '')}</td></tr>`;
            }
            if (resultado.componentes.masa_residual) {
                contenido += `<tr><th>Masa Residual</th><td><strong>${resultado.componentes.masa_residual.kg} kg</strong>${(resultado.componentes.masa_residual.porcentaje != null ? ' (' + resultado.componentes.masa_residual.porcentaje + '%)' : '')}</td></tr>`;
            }
            if (resultado.componentes.masa_osea_total) {
                contenido += `<tr><th>Masa Ósea Total</th><td><strong>${resultado.componentes.masa_osea_total.kg} kg</strong>${(resultado.componentes.masa_osea_total.porcentaje != null ? ' (' + resultado.componentes.masa_osea_total.porcentaje + '%)' : '')}</td></tr>`;
                if (resultado.componentes.masa_osea_total.osea_cabeza_kg != null) {
                    contenido += `<tr><th class="text-muted">Ósea Cabeza</th><td><strong>${resultado.componentes.masa_osea_total.osea_cabeza_kg} kg</strong></td></tr>`;
                }
                if (resultado.componentes.masa_osea_total.osea_cuerpo_kg != null) {
                    contenido += `<tr><th class="text-muted">Ósea Cuerpo</th><td><strong>${resultado.componentes.masa_osea_total.osea_cuerpo_kg} kg</strong></td></tr>`;
                }
            }
            if (resultado.componentes.masa_piel) {
                contenido += `<tr><th>Masa de la Piel</th><td><strong>${resultado.componentes.masa_piel.kg} kg</strong>${(resultado.componentes.masa_piel.porcentaje != null ? ' (' + resultado.componentes.masa_piel.porcentaje + '%)' : '')}</td></tr>`;
            }
            if (resultado.componentes.masa_total) {
                contenido += `<tr><th>Masa Total</th><td><strong>${resultado.componentes.masa_total.kg} kg</strong></td></tr>`;
            }
        }

        // Modelos 4C/5C (legacy: grasa/músculo/hueso/residual/piel)
        if (!esHolway5C && resultado.componentes.grasa) {
            contenido += `
                <tr><th>Grasa</th><td><strong>${resultado.componentes.grasa.kg} kg</strong>${(resultado.componentes.grasa.porcentaje != null ? ' (' + resultado.componentes.grasa.porcentaje + '%)' : '')}</td></tr>
                <tr><th>Músculo</th><td><strong>${resultado.componentes.musculo.kg} kg</strong>${(resultado.componentes.musculo.porcentaje != null ? ' (' + resultado.componentes.musculo.porcentaje + '%)' : '')}</td></tr>
                <tr><th>Hueso</th><td><strong>${resultado.componentes.hueso.kg} kg</strong>${(resultado.componentes.hueso.porcentaje != null ? ' (' + resultado.componentes.hueso.porcentaje + '%)' : '')}</td></tr>
                <tr><th>Residual</th><td><strong>${resultado.componentes.residual.kg} kg</strong>${(resultado.componentes.residual.porcentaje != null ? ' (' + resultado.componentes.residual.porcentaje + '%)' : '')}</td></tr>
            `;
        }
        if (!esHolway5C && resultado.componentes.piel) {
            contenido += `<tr><th>Piel</th><td><strong>${resultado.componentes.piel.kg} kg</strong>${(resultado.componentes.piel.porcentaje != null ? ' (' + resultado.componentes.piel.porcentaje + '%)' : '')}</td></tr>`;
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
                <tr><th>Grasa</th><td><strong>${resultado.grasa_kg} kg</strong>${(resultado.grasa_porcentaje != null ? ' (' + resultado.grasa_porcentaje + '%)' : '')}</td></tr>
                <tr><th>Músculo</th><td><strong>${resultado.musculo_kg} kg</strong>${(resultado.musculo_porcentaje != null ? ' (' + resultado.musculo_porcentaje + '%)' : '')}</td></tr>
                <tr><th>Hueso</th><td><strong>${resultado.hueso_kg} kg</strong>${(resultado.hueso_porcentaje != null ? ' (' + resultado.hueso_porcentaje + '%)' : '')}</td></tr>
                <tr><th>Residual</th><td><strong>${resultado.residual_kg} kg</strong>${(resultado.residual_porcentaje != null ? ' (' + resultado.residual_porcentaje + '%)' : '')}</td></tr>
            `;
        }
        if (resultado.piel_kg !== undefined) {
            contenido += `<tr><th>Piel</th><td><strong>${resultado.piel_kg} kg</strong>${(resultado.piel_porcentaje != null ? ' (' + resultado.piel_porcentaje + '%)' : '')}</td></tr>`;
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
    
    // Cierre de peso Kerr: peso medido vs peso reconstituido (estándar académico)
    if (resultado.cierre_peso && typeof resultado.cierre_peso === 'object') {
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
    
    // Bloque "Pasos del cálculo" (valores intermedios para corroborar)
    if (resultado.pasos_calculo && typeof resultado.pasos_calculo === 'object') {
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

            // 5 componentes Holway (Excel) — pasos requeridos
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

        // Definiciones/ayuda para auditoría clínica (qué incluye cada suma)
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
    
    // Bloque "Datos de ficha" (no influyen en el cálculo)
    if (resultado.datos_ficha && typeof resultado.datos_ficha === 'object') {
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

    // Bloque "Datos utilizados para el cálculo" (todos los datos para corroborar)
    if (resultado.datos_usados && typeof resultado.datos_usados === 'object') {
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

// Etiquetas legibles para campos de composición corporal
var etiquetasCamposComposicion = {
    peso_actual: 'Peso (kg)',
    altura_actual: 'Talla (cm)',
    altura_sentado: 'Altura sentado (cm)',
    pliegue_tricipital: 'Pliegue tríceps (mm)',
    pliegue_subescapular: 'Pliegue subescapular (mm)',
    pliegue_suprailíaco: 'Pliegue suprailíaco (mm)',
    pliegue_abdominal: 'Pliegue abdominal (mm)',
    pliegue_supraespinal: 'Pliegue supraespinal (mm)',
    pliegue_muslo_medial: 'Pliegue muslo medial (mm)',
    pliegue_pantorrilla_medial: 'Pliegue pantorrilla medial (mm)',
    pliegue_bicipital: 'Pliegue bicipital (mm)',
    pliegue_muslo_anterior: 'Pliegue muslo anterior (mm)',
    diametro_humero: 'Diámetro húmero (cm)',
    diametro_femur: 'Diámetro fémur (cm)',
    diametro_muneca: 'Diámetro muñeca (cm)',
    diametro_biacromial: 'Diámetro biacromial (cm)',
    diametro_bi_iliocristal: 'Diámetro bi-iliocristal (cm)',
    diametro_torax_transverso: 'Diámetro tórax transverso (cm)',
    diametro_torax_anteroposterior: 'Diámetro tórax anteroposterior (cm)',
    circunferencia_brazo_relajado: 'Circunferencia brazo relajado (cm)',
    circunferencia_brazo_contraido: 'Circunferencia brazo contraído (cm)',
    circunferencia_pantorrilla: 'Circunferencia pantorrilla (cm)',
    circunferencia_cintura: 'Circunferencia cintura (cm)',
    circunferencia_cadera: 'Circunferencia cadera (cm)',
    circunferencia_muneca: 'Circunferencia muñeca (cm)',
    circunferencia_muslo_medio: 'Circunferencia muslo medio (cm)',
    circunferencia_cabeza: 'Circunferencia cabeza (cm)',
    circunferencia_antebrazo_maximo: 'Circunferencia antebrazo máximo (cm)',
    circunferencia_muslo_maximo: 'Circunferencia muslo máximo (cm)',
    circunferencia_torax: 'Circunferencia tórax (cm)'
};

// Función para mostrar modal de datos insuficientes (lista de campos faltantes)
function mostrarModalDatosInsuficientes(response) {
    var faltantes = response.faltantes || [];
    var metodo = response.metodo || '';
    var listaHtml = faltantes.map(function(campo) {
        var etiqueta = etiquetasCamposComposicion[campo] || campo.replace(/_/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        return '<li class="list-group-item"><i class="fas fa-exclamation-circle text-warning me-2"></i>' + etiqueta + '</li>';
    }).join('');
    var metodoNombre = { '2-componentes': '2 componentes', '4-componentes': '4 componentes (De Rose)', '5-componentes': '5 componentes (Kerr)', 'somatotipo': 'Somatotipo' }[metodo] || metodo;
    var modalHtml = `
        <div class="modal fade" id="modalDatosInsuficientes" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>Datos insuficientes
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Para calcular <strong>${metodoNombre}</strong> faltan los siguientes datos. Complétalos en esta ficha y vuelve a intentar.</p>
                        <ul class="list-group list-group-flush">${listaHtml}</ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#modalDatosInsuficientes').remove();
    $('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('modalDatosInsuficientes'));
    modal.show();
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

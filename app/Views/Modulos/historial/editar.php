<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/editar') ?>

<!-- Toastr para mensajes al guardar -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Tagify para tags (necesario en esta vista) -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

<!-- Chart.js (para gráficos de composición corporal y somatocarta) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="<?= base_url('lib/css/mediciones-antropometria.css') ?>">
<!-- TinyMCE editor (motivo, plan, recomendaciones enriquecidos) -->
<script src="https://cdn.tiny.cloud/1/k10uo8qhvhuxj1ho5z73jcbhzpwlspewyrz3lkbu5b99faon/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #fa709a;
    }
    /* Bloque Información de la Consulta: header y campos más claros */
    .section-card.info-consulta-card {
        overflow: hidden;
    }
    .section-card.info-consulta-card .info-consulta-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: -25px -25px 20px -25px;
        padding: 16px 25px;
        background: linear-gradient(135deg, rgba(250, 112, 154, 0.08) 0%, rgba(254, 225, 64, 0.06) 100%);
        border-bottom: 1px solid rgba(250, 112, 154, 0.2);
    }
    .section-card.info-consulta-card .info-consulta-header .step-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(250, 112, 154, 0.35);
    }
    .section-card.info-consulta-card .info-consulta-header .info-consulta-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-card.info-consulta-card .info-consulta-header .info-consulta-title i {
        color: #fa709a;
        opacity: 0.9;
    }
    .section-card.info-consulta-card .form-group {
        margin-bottom: 1.1rem;
    }
    .section-card.info-consulta-card .form-group label {
        font-weight: 500;
        color: #495057;
        font-size: 0.9rem;
        margin-bottom: 0.35rem;
    }
    .section-card.info-consulta-card .form-control {
        border-radius: 8px;
        border-color: #dee2e6;
    }
    .section-card.info-consulta-card .form-control:focus {
        border-color: #fa709a;
        box-shadow: 0 0 0 0.2rem rgba(250, 112, 154, 0.2);
    }
    .section-card.info-consulta-card .input-group-text {
        border-radius: 0 8px 8px 0;
        border-color: #dee2e6;
        background: #f8f9fa;
    }
    .section-card.info-consulta-card .input-group .form-control {
        border-radius: 8px 0 0 8px;
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
    
    /* Tabs de secciones (igual que consulta): responsive */
    .historial-secciones-tabs .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }
    .historial-secciones-tabs .nav-tabs .nav-link {
        white-space: nowrap;
        font-weight: 500;
        border: none;
        border-bottom: 3px solid transparent;
        color: #6c757d;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .historial-secciones-tabs .nav-tabs .nav-link:hover {
        color: #0d6efd;
        border-color: transparent;
    }
    .historial-secciones-tabs .nav-tabs .nav-link.active {
        color: #0d6efd;
        background: #fff;
        border-bottom-color: #0d6efd;
    }
    .historial-secciones-tabs .nav-tabs .nav-link .tab-badge {
        font-size: 0.7rem;
        margin-left: 0.35rem;
        font-weight: 500;
    }
    .historial-secciones-tabs .tab-content {
        padding: 1.25rem 0 0;
    }
    .historial-secciones-tabs .tab-content .tab-pane { display: none !important; }
    .historial-secciones-tabs .tab-content .tab-pane.active { display: block !important; }
    @media (max-width: 768px) {
        .historial-secciones-tabs .nav-tabs .nav-link { padding: 0.6rem 0.75rem; font-size: 0.9rem; }
        .historial-secciones-tabs .nav-tabs .nav-link .tab-badge { display: block; margin-left: 0; margin-top: 0.2rem; }
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

            <form id="formHistorialEditar" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $historial->id ?>">
                <?php
                // Normalizar fechas a yyyy-MM-dd para input type="date" (si vienen en dd-mm-yyyy u otro formato)
                $fechaParaInputDate = function ($fecha) {
                    if (empty($fecha)) return '';
                    $fecha = trim((string) $fecha);
                    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $fecha, $m)) return $m[1] . '-' . $m[2] . '-' . $m[3];
                    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $fecha, $m)) return sprintf('%04d-%02d-%02d', (int)$m[3], (int)$m[2], (int)$m[1]);
                    $ts = strtotime($fecha);
                    return $ts ? date('Y-m-d', $ts) : '';
                };
                ?>
                <div class="section-card info-consulta-card">
                    <div class="info-consulta-header">
                        <span class="step-badge">1</span>
                        <h3 class="info-consulta-title"><i class="fas fa-calendar-alt"></i> Información de la Consulta</h3>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Paciente <span class="text-danger">*</span></label>
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
                                <label class="form-label">Tipo <span class="text-danger">*</span></label>
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
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="date" name="fecha_consulta" class="form-control" required 
                                           value="<?= esc($fechaParaInputDate(old('fecha_consulta', $historial->fecha_consulta ?? ''))) ?>">
                                    <span class="input-group-text"><i class="fas fa-calendar-day text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Hora</label>
                                <div class="input-group">
                                    <input type="time" name="hora_consulta" class="form-control" 
                                           value="<?= old('hora_consulta', isset($historial->hora_consulta) && $historial->hora_consulta ? (strpos($historial->hora_consulta, ':') !== false ? substr($historial->hora_consulta, 0, 5) : $historial->hora_consulta) : '') ?>">
                                    <span class="input-group-text"><i class="fas fa-clock text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Próxima Cita</label>
                                <div class="input-group">
                                    <input type="date" name="proxima_cita" class="form-control" 
                                           value="<?= esc($fechaParaInputDate(old('proxima_cita', $historial->proxima_cita ?? ''))) ?>">
                                    <span class="input-group-text"><i class="fas fa-calendar-check text-muted"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Secciones en tabs (igual que consulta): Registro Clínico, Mediciones, Calorimetría y Plan -->
                <div class="section-card historial-secciones-tabs" style="border-left: 4px solid #28a745 !important;">
                    <ul class="nav nav-tabs" id="historialSeccionesTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-registro-editar-btn" data-bs-toggle="tab" data-bs-target="#pane-registro-editar" type="button" role="tab" aria-controls="pane-registro-editar" aria-selected="true">
                                <i class="fas fa-file-medical me-1"></i> Registro Clínico <span class="badge tab-badge bg-success small" style="font-size: 0.7rem;">Guardado</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-mediciones-editar-btn" data-bs-toggle="tab" data-bs-target="#pane-mediciones-editar" type="button" role="tab" aria-controls="pane-mediciones-editar" aria-selected="false">
                                <i class="fas fa-ruler-combined me-1"></i> Mediciones <span class="badge tab-badge bg-success small" style="font-size: 0.7rem;">Guardado</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-calorimetria-editar-btn" data-bs-toggle="tab" data-bs-target="#pane-calorimetria-editar" type="button" role="tab" aria-controls="pane-calorimetria-editar" aria-selected="false">
                                <i class="fas fa-calculator me-1"></i> Calorimetría y Plan <span class="badge tab-badge bg-success small" style="font-size: 0.7rem;">Guardado</span>
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="historialSeccionesTabContent">
                        <div class="tab-pane fade" id="pane-mediciones-editar" role="tabpanel" aria-labelledby="tab-mediciones-editar-btn">
                        <p class="mediciones-intro mb-0">
                            <i class="fas fa-info-circle me-1"></i> Elegí el método y cargá solo las medidas necesarias. Peso y talla siempre visibles.
                        </p>
                        <ul class="nav nav-tabs mediciones-metodo-tabs" id="medicionesMetodoTabsEditar" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link active" data-medicion-metodo="todos" role="tab">Todas</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-medicion-metodo="2-componentes" role="tab">2 Componentes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-medicion-metodo="4-componentes" role="tab">4 Componentes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-medicion-metodo="5-componentes" role="tab">5 Componentes</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button type="button" class="nav-link" data-medicion-metodo="somatotipo" role="tab">Somatotipo</button>
                            </li>
                        </ul>
                        <p id="medicionesMetodoHintEditar" class="small mb-3 d-none"></p>
                        <div class="mediciones-panel">

                    <div class="row medicion-seccion" data-seccion="basicas">
                        <div class="col-12">
                            <h6 class="mb-2"><i class="fas fa-weight me-2"></i> Medidas Básicas</h6>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap medicion-always">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" step="0.01" name="peso_actual" class="form-control" id="peso_actual" 
                                       value="<?= old('peso_actual', $historial->peso_actual) ?>">
                            </div>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap medicion-always">
                            <div class="form-group">
                                <label>Altura (cm)</label>
                                <input type="number" step="0.01" name="altura_actual" class="form-control" id="altura_actual" 
                                       value="<?= old('altura_actual', $historial->altura_actual) ?>">
                            </div>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap">
                            <div class="form-group">
                                <label>Altura Sentado (cm)</label>
                                <input type="number" step="0.01" name="altura_sentado" class="form-control metodo-5 metodo-somato" 
                                       value="<?= old('altura_sentado', $historial->altura_sentado ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap medicion-always">
                            <div class="form-group">
                                <label>IMC</label>
                                <input type="text" class="form-control" id="imc_actual" readonly 
                                       value="<?= old('imc_actual', $historial->imc_actual) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="d-none" aria-hidden="true">
                        <input type="number" step="0.01" name="grasa_corporal" value="<?= old('grasa_corporal', $historial->grasa_corporal) ?>">
                        <input type="number" step="0.01" name="masa_muscular" value="<?= old('masa_muscular', $historial->masa_muscular) ?>">
                    </div>

                    <div class="row medicion-seccion" data-seccion="circunferencias">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-circle-notch me-2"></i> Circunferencias (cm)</h6>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap">
                            <div class="form-group">
                                <label>Cintura</label>
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control metodo-5" 
                                       value="<?= old('circunferencia_cintura', $historial->circunferencia_cintura) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cadera</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control metodo-4"
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
                                <input type="number" step="0.01" name="circunferencia_brazo_contraido" class="form-control metodo-somato" 
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
                                <input type="number" step="0.01" name="circunferencia_pantorrilla" class="form-control metodo-4 metodo-5 metodo-2 metodo-somato" 
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
                                <label>Circunferencia de cabeza</label>
                                <input type="number" step="0.01" name="circunferencia_cabeza" class="form-control metodo-5" 
                                       value="<?= old('circunferencia_cabeza', $historial->circunferencia_cabeza ?? '') ?>"
                                       placeholder="Ej: 56.0">
                                <small class="text-muted">Perímetro con cinta (cm). Solo 5 componentes.</small>
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
                                <input type="number" step="0.01" name="circunferencia_muneca" class="form-control"
                                       value="<?= old('circunferencia_muneca', $historial->circunferencia_muneca ?? '') ?>" placeholder="Circunferencia (cm)">
                            </div>
                        </div>
                    </div>

                    <div class="row medicion-seccion" data-seccion="diametros">
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
                                <input type="number" step="0.01" name="diametro_humero" class="form-control metodo-4 metodo-5 metodo-somato"
                                       value="<?= old('diametro_humero', $historial->diametro_humero ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fémur</label>
                                <input type="number" step="0.01" name="diametro_femur" class="form-control metodo-4 metodo-5 metodo-somato" 
                                       value="<?= old('diametro_femur', $historial->diametro_femur ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="diametro_muneca" class="form-control metodo-4"
                                       value="<?= old('diametro_muneca', $historial->diametro_muneca ?? '') ?>" placeholder="Diámetro (cm)">
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

                    <div class="row medicion-seccion" data-seccion="pliegues">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-hand-paper me-2"></i> Pliegues Cutáneos (mm)</h6>
                            <p class="text-muted small mb-2">Medición con plicómetro. Se mide en milímetros (mm).</p>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tricipital</label>
                                <input type="number" step="0.01" name="pliegue_tricipital" class="form-control metodo-4 metodo-5 metodo-2 metodo-somato" 
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
                                <input type="number" step="0.01" name="pliegue_subescapular" class="form-control metodo-4 metodo-5 metodo-2 metodo-somato" 
                                       value="<?= old('pliegue_subescapular', $historial->pliegue_subescapular ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Suprailíaco</label>
                                <input type="number" step="0.01" name="pliegue_suprailíaco" class="form-control metodo-4"
                                       value="<?= old('pliegue_suprailíaco', $historial->pliegue_suprailíaco ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Supraespinal</label>
                                <input type="number" step="0.01" name="pliegue_supraespinal" class="form-control metodo-2 metodo-5 metodo-somato" 
                                       value="<?= old('pliegue_supraespinal', $historial->pliegue_supraespinal ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Abdominal</label>
                                <input type="number" step="0.01" name="pliegue_abdominal" class="form-control metodo-5 metodo-2" 
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
                                <input type="number" step="0.01" name="pliegue_pantorrilla_medial" class="form-control metodo-4 metodo-5 metodo-2 metodo-somato" 
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
                                <input type="number" step="0.01" name="pliegue_muslo_medial" class="form-control metodo-4 metodo-5 metodo-2" 
                                       value="<?= old('pliegue_muslo_medial', $historial->pliegue_muslo_medial ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-3 medicion-campo-wrap medicion-suma-pliegues">
                            <div class="form-group">
                                <label>Suma de Pliegues (mm)</label>
                                <input type="number" step="0.01" name="suma_pliegues" class="form-control metodo-2 metodo-4 metodo-5 metodo-somato" readonly
                                       value="<?= old('suma_pliegues', $historial->suma_pliegues ?? '') ?>">
                                <small class="text-muted">Se calcula automáticamente</small>
                            </div>
                        </div>
                        <input type="hidden" name="grasa_corporal_calculada" value="<?= old('grasa_corporal_calculada', $historial->grasa_corporal_calculada ?? '') ?>">
                    </div>
                        </div><!-- /.mediciones-panel -->
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary" onclick="guardarMedicionesHistorial()"><i class="fas fa-save me-2"></i> Guardar Mediciones</button>
                    </div>
                        </div>
                        <!-- /pane-mediciones-editar -->
                        <div class="tab-pane fade show active" id="pane-registro-editar" role="tabpanel" aria-labelledby="tab-registro-editar-btn">
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-1"></i> Motivo, plan, recomendaciones, ficha de ingreso (anamnesis), exámenes bioquímicos, tendencia de consumo y recordatorio 24 h.
                        </p>
                        <div class="row mb-3 pb-3 border-bottom">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-2"><i class="fas fa-bullseye me-2"></i> Motivo de consulta y/o Objetivo Principal</h6>
                                <p class="text-muted small mb-1">Indique el motivo de la consulta o el objetivo principal acordado con el paciente.</p>
                                <textarea name="motivo_consulta" id="motivo_consulta" class="form-control" rows="3"><?= old('motivo_consulta', $historial->motivo_consulta) ?></textarea>
                            </div>
                        </div>
                        <div class="row mb-3 pb-3 border-bottom">
                            <div class="col-md-12">
                                <h6 class="mb-2" style="color: #0dcaf0;"><i class="fas fa-utensils me-2"></i> Plan de Tratamiento</h6>
                                <p class="text-muted small mb-1">Describe el plan de tratamiento y alimentación acordado.</p>
                                <textarea name="plan_tratamiento" id="plan_tratamiento" class="form-control" rows="4"><?= old('plan_tratamiento', $historial->plan_tratamiento) ?></textarea>
                            </div>
                        </div>
                        <div class="row mb-4 pb-4 border-bottom">
                            <div class="col-md-12">
                                <h6 class="mb-2" style="color: #fd7e14;"><i class="fas fa-lightbulb me-2"></i> Recomendaciones u Observaciones</h6>
                                <p class="text-muted small mb-1">Recomendaciones y observaciones para el paciente.</p>
                                <textarea name="recomendaciones" id="recomendaciones" class="form-control" rows="4"><?= old('recomendaciones', trim(($historial->recomendaciones ?? '') . (isset($historial->observaciones) && $historial->observaciones !== '' ? "\n\n" . $historial->observaciones : ''))) ?></textarea>
                                <input type="hidden" name="observaciones" value="">
                            </div>
                        </div>
                        <?= $this->include('Modulos/historial/partial_registro_clinico_editar') ?>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="button" class="btn btn-success" onclick="guardarRegistroClinicoHistorial()"><i class="fas fa-save me-2"></i> Guardar Registro Clínico</button>
                        </div>
                        </div>
                        <!-- /pane-registro-editar -->
                        <div class="tab-pane fade" id="pane-calorimetria-editar" role="tabpanel" aria-labelledby="tab-calorimetria-editar-btn">
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-1"></i> Calcula el gasto calórico y crea un plan alimentario estructurado con porciones e intercambios.
                        </p>
                    <ul class="nav nav-tabs mb-3" id="planAlimentarioTabsEditar" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="calorimetria-tab-editar" data-bs-toggle="tab" data-bs-target="#calorimetria-editar" type="button" role="tab" aria-controls="calorimetria-editar" aria-selected="true"><i class="fas fa-fire me-2"></i> Calorimetría</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="plan-tab-editar" data-bs-toggle="tab" data-bs-target="#plan-editar" type="button" role="tab" aria-controls="plan-editar" aria-selected="false"><i class="fas fa-clipboard-list me-2"></i> Plan Alimentario</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="distribucion-tab-editar" data-bs-toggle="tab" data-bs-target="#distribucion-editar" type="button" role="tab" aria-controls="distribucion-editar" aria-selected="false"><i class="fas fa-utensils me-2"></i> Distribución por Comidas</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="planAlimentarioTabContentEditar">
                        <div class="tab-pane fade show active" id="calorimetria-editar" role="tabpanel">
                            <?php $embebidoEnForm = true; echo $this->include('Modulos/plan_alimentario/calorimetria'); ?>
                        </div>
                        <div class="tab-pane fade" id="plan-editar" role="tabpanel">
                            <?php $embebidoEnForm = true; echo $this->include('Modulos/plan_alimentario/plan'); ?>
                        </div>
                        <div class="tab-pane fade" id="distribucion-editar" role="tabpanel">
                            <?= $this->include('Modulos/plan_alimentario/distribucion_comidas') ?>
                        </div>
                    </div>
                        </div>
                        <!-- /pane-calorimetria-editar -->
                    </div>
                </div>

                <!-- 6) Tags (mismo orden que agenda/consulta) -->
                <div class="section-card" style="border-left: 4px solid #6c757d !important;">
                    <h6 class="mb-2" style="color: #6c757d;"><i class="fas fa-tags me-2"></i> Tags</h6>
                    <p class="text-muted small mb-2">Etiquetas para categorizar y buscar esta consulta. Escriba y presione Enter o coma.</p>
                    <input type="text" name="tags" id="tags" class="form-control" 
                           placeholder="Ej: diabetes, hipertensión, seguimiento, control"
                           value="<?= esc($tags_string ?? '') ?>">
                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" id="btnGuardarTags" class="btn btn-primary" onclick="guardarInformacionClinicaHistorial()"><i class="fas fa-save me-2"></i> Guardar</button>
                    </div>
                </div>

                <!-- Sección: Métodos de Cálculo de Composición Corporal -->
                <?php if (!empty($metodos_calculo ?? [])): ?>
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">5</span>
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
                    <button type="button" class="btn btn-submit" id="btnActualizarConsulta">
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
// Toastr (igual que agenda/consulta)
if (typeof toastr !== 'undefined') {
    toastr.options = { closeButton: true, progressBar: true, positionClass: 'toast-top-right', timeOut: 3000 };
}
// Antes de enviar el formulario: armar JSON de ficha de ingreso y poner en los hidden
function prepararFichaIngresoHistorial(form) {
    if (typeof window.jQuery === 'undefined') return;
    var $ = window.jQuery;
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
    var anamnesisClinica = document.getElementById('anamnesis_clinica');
    if (anamnesisClinica) anamnesisClinica.value = JSON.stringify(acObj);
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
    var anamnesisAlim = document.getElementById('anamnesis_alimentaria');
    if (anamnesisAlim) anamnesisAlim.value = JSON.stringify(aaObj);
    var tendenciaRows = [];
    $('#tbodyTendenciaConsumo tr[data-grupo]').each(function() {
        var grupo = $(this).data('grupo');
        var preferencia = $(this).find('.tc-preferencia').val() || '';
        var alergia = $(this).find('.tc-alergia').val() || '';
        tendenciaRows.push({ grupo: grupo, preferencia: preferencia, alergia_intolerancia: alergia });
    });
    var tendenciaInput = document.getElementById('tendencia_consumo');
    if (tendenciaInput) tendenciaInput.value = JSON.stringify(tendenciaRows);
    var filasExamenes = [];
    $('#tbodyExamenesBioquimicos tr.fila-examen').each(function() {
        var $tr = $(this);
        filasExamenes.push({
            nombre: ($tr.find('input[name="examen_nombre[]"]').val() || '').trim(),
            valor: ($tr.find('input[name="examen_valor[]"]').val() || '').trim(),
            fecha_interpretacion: ($tr.find('input[name="examen_fecha[]"]').val() || '').trim()
        });
    });
    var examenesHidden = document.getElementById('examenes_bioquimicos_hidden');
    if (examenesHidden) examenesHidden.value = JSON.stringify(filasExamenes);
}

// Evitar envío clásico del formulario (todo se guarda por AJAX)
(function() {
    var form = document.getElementById('formHistorialEditar');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        try { prepararFichaIngresoHistorial(form); } catch (err) { console.error('prepararFichaIngresoHistorial:', err); }
        guardarInformacionClinicaHistorial();
        setTimeout(function() { guardarMedicionesHistorial(); }, 400);
    });
})();

function obtenerTokenCSRF() {
    var name = 'csrf_cookie_name';
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var c = cookies[i].trim();
        if (c.indexOf(name + '=') === 0) return c.substring(name.length + 1);
    }
    return '';
}
function actualizarTokenCSRF(xhr) {
    if (!xhr) return;
    var t = (xhr.getResponseHeader && xhr.getResponseHeader('X-CSRF-TOKEN')) || (xhr.responseJSON && xhr.responseJSON.csrf_token);
    if (t) {
        if (window.jQuery) {
            window.jQuery('meta[name="csrf-token"]').attr('content', t);
            window.jQuery('input[name="csrf_test_name"]').val(t);
        }
    }
}

function guardarRegistroClinicoHistorial() {
    guardarInformacionClinicaHistorial(true);
    guardarMedicionesHistorial(false);
}

// Guardar motivo, plan y recomendaciones por AJAX
function guardarInformacionClinicaHistorial(silentToast) {
    if (typeof window.jQuery === 'undefined') return;
    var $ = window.jQuery;
    var csrfToken = $('input[name="csrf_test_name"]').val() || obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var tagsValue = '';
    try {
        if (typeof window.__tagifyHistorial !== 'undefined' && window.__tagifyHistorial && window.__tagifyHistorial.value) {
            var v = window.__tagifyHistorial.value;
            if (Array.isArray(v)) tagsValue = v.map(function(x) { return (x && (x.value || x.tag || x)) ? (x.value || x.tag || x) : ''; }).filter(Boolean).join(',');
            else tagsValue = (v && typeof v === 'string') ? v : '';
        } else tagsValue = $('#tags').val() || '';
    } catch (e) { tagsValue = $('#tags').val() || ''; }
    // Soporte TinyMCE si está cargado; si no, leer del textarea por id o name
    var motivo = '', plan = '', recomendaciones = '';
    if (typeof tinymce !== 'undefined') {
        if (tinymce.get('motivo_consulta')) motivo = tinymce.get('motivo_consulta').getContent();
        else motivo = $('#motivo_consulta').val() || $('textarea[name="motivo_consulta"]').val() || '';
        if (tinymce.get('plan_tratamiento')) plan = tinymce.get('plan_tratamiento').getContent();
        else plan = $('#plan_tratamiento').val() || $('textarea[name="plan_tratamiento"]').val() || '';
        if (tinymce.get('recomendaciones')) recomendaciones = tinymce.get('recomendaciones').getContent();
        else recomendaciones = $('#recomendaciones').val() || $('textarea[name="recomendaciones"]').val() || '';
    } else {
        motivo = $('#motivo_consulta').val() || $('textarea[name="motivo_consulta"]').val() || '';
        plan = $('#plan_tratamiento').val() || $('textarea[name="plan_tratamiento"]').val() || '';
        recomendaciones = $('#recomendaciones').val() || $('textarea[name="recomendaciones"]').val() || '';
    }
    var proximaCita = $('#formHistorialEditar input[name="proxima_cita"]').val() || '';
    var formData = {
        id: $('input[name="id"]').val(),
        paciente_id: $('select[name="paciente_id"]').val(),
        tipo_registro: $('select[name="tipo_registro"]').val(),
        fecha_consulta: $('input[name="fecha_consulta"]').val(),
        hora_consulta: $('input[name="hora_consulta"]').val(),
        motivo_consulta: motivo,
        plan_tratamiento: plan,
        recomendaciones: recomendaciones,
        proxima_cita: proximaCita,
        tags: tagsValue,
        csrf_test_name: csrfToken
    };
    $.ajax({
        url: '<?= base_url('dashboard/historial/guardarInformacionClinica') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            if (response && response.success) {
                if (!silentToast) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Registro clínico guardado correctamente', 'Éxito', { timeOut: 3500, positionClass: 'toast-top-right' });
                    } else {
                        alert(response.message || 'Registro clínico guardado correctamente');
                    }
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response && (response.error || response.message) || 'Error al guardar', 'Error');
                } else {
                    alert(response && (response.error || response.message) || 'Error al guardar');
                }
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'Error al guardar la información';
            if (typeof toastr !== 'undefined') {
                toastr.error(msg, 'Error', { timeOut: 4000 });
            } else {
                alert(msg);
            }
        }
    });
}

// Guardar mediciones y ficha de registro clínico por AJAX
function guardarMedicionesHistorial(silentToast) {
    if (typeof window.jQuery === 'undefined') return;
    var $ = window.jQuery;
    var form = document.getElementById('formHistorialEditar');
    if (form) try { prepararFichaIngresoHistorial(form); } catch (e) { console.error(e); }
    var csrfToken = $('input[name="csrf_test_name"]').val() || obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var tagsValue = '';
    try {
        if (typeof window.__tagifyHistorial !== 'undefined' && window.__tagifyHistorial && window.__tagifyHistorial.value) {
            var v = window.__tagifyHistorial.value;
            if (Array.isArray(v)) tagsValue = v.map(function(x) { return (x && (x.value || x.tag || x)) ? (x.value || x.tag || x) : ''; }).filter(Boolean).join(',');
            else tagsValue = (v && typeof v === 'string') ? v : '';
        } else tagsValue = $('#tags').val() || '';
    } catch (e) { tagsValue = $('#tags').val() || ''; }
    var formData = $('#formHistorialEditar').serialize();
    formData = formData.replace(/&?tags=[^&]*/g, '');
    if (tagsValue) formData += '&tags=' + encodeURIComponent(tagsValue);
    formData = formData.replace(/&?csrf_test_name=[^&]*/g, '');
    formData += (formData ? '&' : '') + 'csrf_test_name=' + encodeURIComponent(csrfToken);
    var examenesVal = $('#examenes_bioquimicos_hidden').val() || '';
    if (examenesVal) formData += '&examenes_bioquimicos=' + encodeURIComponent(examenesVal);
    $.ajax({
        url: '<?= base_url('dashboard/historial/guardarMediciones') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            if (response && response.success) {
                if (!silentToast) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Registro clínico guardado correctamente', 'Éxito', { timeOut: 3500, positionClass: 'toast-top-right' });
                    } else {
                        alert(response.message || 'Registro clínico guardado correctamente');
                    }
                }
            } else {
                if (typeof toastr !== 'undefined') {
                    toastr.error(response && (response.error || response.message) || 'Error al guardar', 'Error');
                } else {
                    alert(response && (response.error || response.message) || 'Error al guardar');
                }
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ? (xhr.responseJSON.message || xhr.responseJSON.error) : 'Error al guardar registro clínico';
            if (typeof toastr !== 'undefined') {
                toastr.error(msg, 'Error', { timeOut: 4000 });
            } else {
                alert(msg);
            }
        }
    });
}

// Obtener tags en formato CSV desde Tagify o desde el input (para enviar al backend)
function getTagsCsvParaEnvio() {
    var tagify = window.__tagifyHistorial;
    var input = document.querySelector('input[name="tags"]');
    if (!input) return '';
    var val = tagify && tagify.value !== undefined ? tagify.value : input.value;
    if (Array.isArray(val)) {
        return val.map(function(x) { return (x && (x.value || x.name || x)) ? (x.value || x.name || x) : ''; }).filter(Boolean).join(',');
    }
    if (typeof val === 'string' && val.trim()) {
        if (val.trim().charAt(0) === '[') {
            try {
                var arr = JSON.parse(val);
                if (Array.isArray(arr)) return arr.map(function(x) { return (x && (x.value || x.name || typeof x === 'string' ? x : '')); }).filter(Boolean).join(',');
            } catch (e) {}
        }
        return val.trim();
    }
    return '';
}

// Botón "Actualizar Consulta": guardar info clínica y luego mediciones
(function() {
    if (typeof window.jQuery === 'undefined') return;
    window.jQuery('#btnActualizarConsulta').on('click', function() {
        guardarInformacionClinicaHistorial();
        setTimeout(function() { guardarMedicionesHistorial(); }, 500);
    });
})();

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

    // Antes de enviar el formulario, sincronizar Tagify a CSV en el campo (usar misma lógica que getTagsCsvParaEnvio)
    var $form = $('#formHistorialEditar');
    if ($form.length) {
        $form.on('submit', function() {
            if (typeof getTagsCsvParaEnvio === 'function' && input) {
                input.value = getTagsCsvParaEnvio();
            } else {
                try {
                    var csv = (window.__tagifyHistorial.value || []).map(function(x){ return (x && x.value) ? x.value : ''; }).filter(Boolean).join(',');
                    input.value = csv;
                } catch (e) {}
            }
        });
    }
});

// Inicializar TinyMCE para motivo, plan y recomendaciones (tab Registro Clínico activo por defecto)
$(document).ready(function() {
    var tinymceHistorialInited = false;
    function initTinyMCEHistorial() {
        if (typeof tinymce === 'undefined') return;
        if (tinymce.get('motivo_consulta')) return;
        if (!$('#motivo_consulta').length) return;
        var config = {
            height: 220,
            menubar: false,
            plugins: 'lists link table code wordcount',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            language: 'es',
            branding: false,
            promotion: false
        };
        tinymce.init(Object.assign({}, config, { selector: '#motivo_consulta' })).then(function() {});
        tinymce.init(Object.assign({}, config, { selector: '#plan_tratamiento', height: 260 })).then(function() {});
        tinymce.init(Object.assign({}, config, { selector: '#recomendaciones' })).then(function() {});
        tinymceHistorialInited = true;
    }
    setTimeout(function() {
        initTinyMCEHistorial();
    }, 400);
    $(document).on('shown.bs.tab', '#tab-registro-editar-btn', function() {
        if (!tinymceHistorialInited && typeof tinymce !== 'undefined') {
            setTimeout(initTinyMCEHistorial, 100);
        }
    });
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

    (function() {
        var METODO_CLASS = { '2-componentes': 'metodo-2', '4-componentes': 'metodo-4', '5-componentes': 'metodo-5', 'somatotipo': 'metodo-somato' };
        var METODO_LABEL = { 'todos': 'Todas las medidas', '2-componentes': '2 componentes', '4-componentes': '4 componentes', '5-componentes': '5 componentes', 'somatotipo': 'Somatotipo' };
        var STORAGE_KEY = 'nutrinext_medicion_metodo_filtro_editar';
        var ALWAYS = { peso_actual: true, altura_actual: true };

        function wrapDeInput(input) {
            return input.closest('.medicion-campo-wrap') || input.closest('.col-md-3, .col-md-4, .col-md-6');
        }

        function campoUsadoEnMetodo(input, metodo) {
            if (!metodo || metodo === 'todos') return true;
            var cls = METODO_CLASS[metodo];
            return !!(cls && input.classList.contains(cls));
        }

        window.aplicarFiltroMedicionEditar = function(metodo, opts) {
            opts = opts || {};
            var pane = document.getElementById('pane-mediciones-editar');
            if (!pane) return;
            metodo = metodo || 'todos';
            document.body.classList.toggle('mediciones-filtradas', metodo !== 'todos');

            pane.querySelectorAll('input.form-control[name], select.form-control[name], textarea.form-control[name]').forEach(function(input) {
                if (input.type === 'hidden') return;
                var wrap = wrapDeInput(input);
                if (!wrap) return;
                if (wrap.classList.contains('medicion-always') || ALWAYS[input.name]) {
                    wrap.classList.remove('d-none');
                    return;
                }
                wrap.classList.toggle('d-none', !campoUsadoEnMetodo(input, metodo));
            });

            pane.querySelectorAll('.medicion-seccion').forEach(function(seccion) {
                var camposVisibles = 0;
                seccion.querySelectorAll('input.form-control[name], select.form-control[name], textarea.form-control[name]').forEach(function(input) {
                    if (input.type === 'hidden') return;
                    var wrap = wrapDeInput(input);
                    if (wrap && !wrap.classList.contains('d-none')) camposVisibles++;
                });
                var header = seccion.querySelector('.col-md-12');
                if (header) header.classList.toggle('d-none', camposVisibles === 0);
                if (seccion.getAttribute('data-seccion') !== 'basicas') {
                    seccion.classList.toggle('d-none', camposVisibles === 0);
                }
            });

            var hint = document.getElementById('medicionesMetodoHintEditar');
            if (hint) {
                if (metodo !== 'todos') {
                    hint.textContent = 'Mostrando solo los campos necesarios para ' + (METODO_LABEL[metodo] || metodo) + '.';
                    hint.classList.remove('d-none');
                } else {
                    hint.classList.add('d-none');
                }
            }

            if (!opts.skipStorage) {
                try { sessionStorage.setItem(STORAGE_KEY, metodo); } catch (e) {}
            }

            var tabs = document.getElementById('medicionesMetodoTabsEditar');
            if (tabs) {
                tabs.querySelectorAll('[data-medicion-metodo]').forEach(function(btn) {
                    var active = btn.getAttribute('data-medicion-metodo') === metodo;
                    btn.classList.toggle('active', active);
                });
            }
        };

        $(document).on('click', '#medicionesMetodoTabsEditar [data-medicion-metodo]', function(e) {
            e.preventDefault();
            window.aplicarFiltroMedicionEditar($(this).data('medicion-metodo'));
        });

        var saved = 'todos';
        try { saved = sessionStorage.getItem(STORAGE_KEY) || 'todos'; } catch (e) {}
        if (saved !== 'todos') window.aplicarFiltroMedicionEditar(saved, { skipStorage: true });

        $('#tab-mediciones-editar-btn').on('shown.bs.tab', function() {
            var m = 'todos';
            try { m = sessionStorage.getItem(STORAGE_KEY) || 'todos'; } catch (e) {}
            window.aplicarFiltroMedicionEditar(m, { skipStorage: true });
        });
    })();

    $(document).on('click', '.calcular-metodo', function() {
        console.log('[Composición] Click calcular-metodo', this);
        var metodoSlug = $(this).data('metodo');
        var metodoNombre = $(this).data('nombre');
        if (typeof window.aplicarFiltroMedicionEditar === 'function') {
            window.aplicarFiltroMedicionEditar(metodoSlug);
        }
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
                } else if (response.error === 'Datos insuficientes' && response.faltantes && response.faltantes.length) {
                    mostrarModalDatosInsuficientes(response);
                } else if (response.requiere_upgrade) {
                    mostrarErrorUpgrade(response);
                } else {
                    mostrarModalErrorCalculo({
                        error: response.error || 'Error al calcular',
                        message: response.message || response.error || 'Error desconocido',
                        metodo: response.metodo || metodoSlug
                    });
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

function humanizarMensajeErrorCalculo(msg) {
    if (!msg) return msg;
    return String(msg)
        .replace(/\bPCAB\b/g, 'circunferencia de cabeza')
        .replace(/\bMO_CAB\b/g, 'resultado del modelo')
        .replace(/masa ósea de cabeza/gi, 'resultado del cálculo');
}

// Modal genérico para errores de cálculo (evita alert y muestra detalle clínico)
function mostrarModalErrorCalculo(payload) {
    var titulo = (payload && payload.error) ? payload.error : 'Error al calcular';
    var mensaje = humanizarMensajeErrorCalculo((payload && payload.message) ? payload.message : 'Ocurrió un error inesperado.');
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
    var PCAB = numByName('circunferencia_cabeza');

    if (PCAB === null || PCAB <= 0) {
        issues.push({ campo: 'circunferencia_cabeza', valor: PCAB == null ? '—' : PCAB, hint: 'Perímetro con cinta (cm), en Circunferencias. Requerido para 5 componentes.' });
    } else if (PCAB < 47) {
        issues.push({ campo: 'circunferencia_cabeza', valor: PCAB, hint: 'Circunferencia de cabeza en cm suele ser 50–60. ¿Pulgadas o valor incompleto?' });
    }
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
            if (resultado.coordenadas_somatochart) {
                contenido += `<tr><th>Coord. somatocarta (X)</th><td><strong>${resultado.coordenadas_somatochart.x}</strong></td></tr>`;
                contenido += `<tr><th>Coord. somatocarta (Y)</th><td><strong>${resultado.coordenadas_somatochart.y}</strong></td></tr>`;
            }
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
            if (metodoSlug === '5-componentes' || metodoSlug === '2-componentes') {
                definiciones.push('Σ6 = Tricipital + Subescapular + Supraespinal + Abdominal + Muslo medial + Pantorrilla medial');
            } else {
                definiciones.push('Σ6 = Tricipital + Subescapular + Suprailíaco + Abdominal + Muslo medial + Pantorrilla medial');
            }
        }
        if (resultado.pasos_calculo.suma_3_pliegues_endo != null || (resultado.datos_usados && resultado.datos_usados.suma_3_pliegues != null)) {
            definiciones.push('Σ3 (Endomorfia) = Tricipital + Subescapular + Supraespinal');
        }
        if (metodoSlug === '2-componentes') {
            definiciones.push('Perímetros corregidos: brazo relajado − (π·tríceps/10), antebrazo, muslo máx − (π·muslo medial/10), pantorrilla − (π·pantorrilla/10), tórax − (π·subescapular/10)');
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
    circunferencia_cabeza: 'Circunferencia de cabeza (cm)',
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
    var metodoNombre = { '2-componentes': '2 componentes (Kerr — MA y MM)', '4-componentes': '4 componentes (Fisionutdep)', '5-componentes': '5 componentes (Holway / Kerr)', 'somatotipo': 'Somatotipo (Heath-Carter)' }[metodo] || metodo;
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
                        <a href="<?= base_url('precios') ?>" class="btn btn-primary">Ver planes</a>
                        <a href="<?= base_url('contacto') ?>" class="btn btn-outline-success">Contactar ventas</a>
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

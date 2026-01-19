<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/editar') ?>

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
                                <label>IMC</label>
                                <input type="text" class="form-control" id="imc_actual" readonly 
                                       value="<?= old('imc_actual', $historial->imc_actual) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Grasa Corporal (%)</label>
                                <input type="number" step="0.01" name="grasa_corporal" class="form-control" 
                                       value="<?= old('grasa_corporal', $historial->grasa_corporal) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cintura (cm)</label>
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control" 
                                       value="<?= old('circunferencia_cintura', $historial->circunferencia_cintura) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Cadera (cm)</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control" 
                                       value="<?= old('circunferencia_cadera', $historial->circunferencia_cadera) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Masa Muscular (kg)</label>
                                <input type="number" step="0.01" name="masa_muscular" class="form-control" 
                                       value="<?= old('masa_muscular', $historial->masa_muscular) ?>">
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
                                       value="<?= old('tags', $tags_string ?? '') ?>">
                                <small class="form-text text-muted">
                                    Los tags ayudan a categorizar y buscar consultas. Ejemplos: diabetes, hipertensión, seguimiento, control, etc.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

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

// Inicializar Tagify para tags con autocompletado
$(document).ready(function() {
    // Obtener tags sugeridos
    var tagsSugeridos = <?= json_encode(array_column($tags_sugeridos ?? [], 'tag_display')) ?>;
    
    // Cargar tags existentes si hay
    var tagsExistentes = '<?= old('tags', $tags_string ?? '') ?>';
    var tagsArray = tagsExistentes ? tagsExistentes.split(',').map(t => t.trim()) : [];
    
    var input = document.querySelector('input[name=tags]');
    var tagify = new Tagify(input, {
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

    // Cargar tags existentes
    if (tagsArray.length > 0) {
        tagify.addTags(tagsArray);
    }

    // Cargar tags sugeridos dinámicamente
    tagify.on('input', function(e) {
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
                tagify.settings.whitelist = whitelist;
                tagify.dropdown.show.call(tagify, value);
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

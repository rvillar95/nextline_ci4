<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/registro') ?>

<!-- Tagify para tags -->
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify@4.17.9/dist/tagify.min.js"></script>

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
                        <h2 style="color: white;"><i class="fas fa-stethoscope me-2"></i> Registrar Nueva Consulta</h2>
                        <p style="color: white;">Registre una nueva consulta en el historial clínico</p>
                    </div>
                    <a href="<?= base_url('dashboard/historial/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <form action="<?= base_url('dashboard/historial/registrar') ?>" method="post">
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

                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-2"><i class="fas fa-circle-notch me-2"></i> Circunferencias (cm)</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cintura</label>
                                <input type="number" step="0.01" name="circunferencia_cintura" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cadera</label>
                                <input type="number" step="0.01" name="circunferencia_cadera" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Relajado</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_relajado" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Brazo Contraído</label>
                                <input type="number" step="0.01" name="circunferencia_brazo_contraido" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muslo Medio</label>
                                <input type="number" step="0.01" name="circunferencia_muslo_medio" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Pantorrilla</label>
                                <input type="number" step="0.01" name="circunferencia_pantorrilla" class="form-control">
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
                                <input type="number" step="0.01" name="circunferencia_torax" class="form-control">
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
                                <input type="number" step="0.01" name="diametro_biacromial" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Bi-iliocristal</label>
                                <input type="number" step="0.01" name="diametro_bi_iliocristal" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Húmero</label>
                                <input type="number" step="0.01" name="diametro_humero" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fémur</label>
                                <input type="number" step="0.01" name="diametro_femur" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Muñeca</label>
                                <input type="number" step="0.01" name="diametro_muneca" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tobillo</label>
                                <input type="number" step="0.01" name="diametro_tobillo" class="form-control">
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
                                <textarea name="motivo_consulta" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Anamnesis</label>
                                <textarea name="anamnesis" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Diagnóstico</label>
                                <textarea name="diagnostico" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Plan de Tratamiento</label>
                                <textarea name="plan_tratamiento" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Recomendaciones</label>
                                <textarea name="recomendaciones" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea name="observaciones" class="form-control" rows="2"></textarea>
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

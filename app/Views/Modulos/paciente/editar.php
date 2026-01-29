<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paciente/editar') ?>

<style>
    .main-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1.3rem;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .form-label {
        font-weight: 600;
        color: #34495e;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .icon-label {
        color: #667eea;
        font-size: 1rem;
    }
    
    .required {
        color: #e74c3c;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-edit me-2"></i> Editar Paciente</h2>
                        <p style="color: white;">Actualice la información del paciente</p>
                    </div>
                    <a href="<?= base_url('dashboard/paciente/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('errors')) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                    <?php if (is_array(session()->getFlashdata('errors'))) : ?>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $field => $error) : ?>
                                <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dashboard/paciente/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= esc($paciente->id) ?>">
                <!-- Mantener/asegurar nutricionista asociado al paciente -->
                <input type="hidden" name="nutricionista_id" value="<?= old('nutricionista_id', $paciente->nutricionista_id ?? session()->get('usuario')['id']) ?>">
                
                <!-- Información Básica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-id-card icon-label"></i> Información Básica</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Nombre <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control" 
                                       value="<?= old('nombre', $paciente->nombre ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Apellido <span class="required">*</span>
                                </label>
                                <input type="text" name="apellido" class="form-control" 
                                       value="<?= old('apellido', $paciente->apellido ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-id-badge icon-label"></i>
                                    RUT/DNI
                                </label>
                                <input type="text" name="rut_dni" class="form-control" 
                                       value="<?= old('rut_dni', $paciente->rut_dni ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar icon-label"></i>
                                    Fecha de Nacimiento
                                </label>
                                <input type="date" name="fecha_nacimiento" class="form-control" 
                                       value="<?= old('fecha_nacimiento', $paciente->fecha_nacimiento ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-venus-mars icon-label"></i>
                                    Género
                                </label>
                                <select name="genero" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <option value="M" <?= old('genero', $paciente->genero ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                    <option value="F" <?= old('genero', $paciente->genero ?? '') == 'F' ? 'selected' : '' ?>>Femenino</option>
                                    <option value="O" <?= old('genero', $paciente->genero ?? '') == 'O' ? 'selected' : '' ?>>Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tag icon-label"></i>
                                    Tipo de Paciente <span class="required">*</span>
                                </label>
                                <select name="tipo_paciente" class="form-control" required>
                                    <option value="">-- Seleccione --</option>
                                    <option value="particular" <?= old('tipo_paciente', $paciente->tipo_paciente ?? '') == 'particular' ? 'selected' : '' ?>>Particular</option>
                                    <option value="convenio" <?= old('tipo_paciente', $paciente->tipo_paciente ?? '') == 'convenio' ? 'selected' : '' ?>>Convenio</option>
                                    <option value="seguro" <?= old('tipo_paciente', $paciente->tipo_paciente ?? '') == 'seguro' ? 'selected' : '' ?>>Seguro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contacto -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-phone icon-label"></i> Información de Contacto</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone icon-label"></i>
                                    Teléfono
                                </label>
                                <input type="text" name="telefono" class="form-control" 
                                       value="<?= old('telefono', $paciente->telefono ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope icon-label"></i>
                                    Email
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= old('email', $paciente->email ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt icon-label"></i>
                                    Dirección
                                </label>
                                <input type="text" name="direccion" class="form-control" 
                                       value="<?= old('direccion', $paciente->direccion ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <?php if (isset($regiones) && !empty($regiones)) : ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map icon-label"></i>
                                    Región
                                </label>
                                <select name="region_id" id="region_id" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <?php foreach ($regiones as $region) : ?>
                                        <option value="<?= $region->id ?>" 
                                                <?= old('region_id', $paciente->region_id ?? '') == $region->id ? 'selected' : '' ?>>
                                            <?= esc($region->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-pin icon-label"></i>
                                    Comuna
                                </label>
                                <select name="comuna_id" id="comuna_id" class="form-control">
                                    <option value="">-- Seleccione --</option>
                                    <?php if (isset($comunas) && !empty($comunas)) : ?>
                                        <?php foreach ($comunas as $comuna) : ?>
                                            <option value="<?= $comuna->id ?>" 
                                                    <?= old('comuna_id', $paciente->comuna_id ?? '') == $comuna->id ? 'selected' : '' ?>>
                                                <?= esc($comuna->nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Información Clínica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-heartbeat icon-label"></i> Información Clínica</span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-weight icon-label"></i>
                                    Peso Inicial (kg)
                                </label>
                                <input type="number" step="0.01" name="peso_inicial" class="form-control" 
                                       value="<?= old('peso_inicial', $paciente->peso_inicial ?? '') ?>" id="peso_inicial">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-ruler-vertical icon-label"></i>
                                    Altura (cm)
                                </label>
                                <input type="number" step="0.01" name="altura" class="form-control" 
                                       value="<?= old('altura', $paciente->altura ?? '') ?>" id="altura">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calculator icon-label"></i>
                                    IMC Inicial
                                </label>
                                <input type="text" class="form-control" id="imc_inicial" readonly 
                                       value="<?= old('imc_inicial', $paciente->imc_inicial ?? '') ?>"
                                       placeholder="Se calcula automáticamente">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-bullseye icon-label"></i>
                                    Objetivo
                                </label>
                                <textarea name="objetivo" class="form-control" rows="3"><?= old('objetivo', $paciente->objetivo ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-exclamation-triangle icon-label"></i>
                                    Alergias
                                </label>
                                <textarea name="alergias" class="form-control" rows="2"><?= old('alergias', $paciente->alergias ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-pills icon-label"></i>
                                    Medicamentos Actuales
                                </label>
                                <textarea name="medicamentos" class="form-control" rows="2"><?= old('medicamentos', $paciente->medicamentos ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-file-medical icon-label"></i>
                                    Condiciones Médicas
                                </label>
                                <textarea name="condiciones_medicas" class="form-control" rows="2"><?= old('condiciones_medicas', $paciente->condiciones_medicas ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note icon-label"></i>
                                    Observaciones
                                </label>
                                <textarea name="observaciones" class="form-control" rows="3"><?= old('observaciones', $paciente->observaciones ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i> Actualizar Paciente
                    </button>
                    <a href="<?= base_url('dashboard/paciente/lista') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Calcular IMC automáticamente
$('#peso_inicial, #altura').on('input', function() {
    var peso = parseFloat($('#peso_inicial').val());
    var altura = parseFloat($('#altura').val());
    
    if (peso > 0 && altura > 0) {
        var alturaMetros = altura / 100;
        var imc = peso / (alturaMetros * alturaMetros);
        $('#imc_inicial').val(imc.toFixed(2));
    } else {
        $('#imc_inicial').val('');
    }
});

// Cargar comunas cuando se selecciona una región
<?php if (isset($regiones) && !empty($regiones)) : ?>
$('#region_id').on('change', function() {
    var regionId = $(this).val();
    var comunaSelect = $('#comuna_id');
    
    comunaSelect.html('<option value="">-- Seleccione --</option>');
    
    if (regionId) {
        $.ajax({
            url: '<?= base_url('dashboard/ubicacion/comunas') ?>',
            type: 'GET',
            data: { region_id: regionId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $.each(response.data, function(index, comuna) {
                        comunaSelect.append('<option value="' + comuna.id + '">' + comuna.nombre + '</option>');
                    });
                }
            },
            error: function() {
                console.error('Error al cargar comunas');
            }
        });
    }
});
<?php endif; ?>
</script>

<?= $this->endSection() ?>

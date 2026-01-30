<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cliente/registro') ?>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #3498db;
        transition: all 0.3s ease;
    }
    
    .section-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        transform: translateY(-2px);
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
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
    }
    
    .section-subtitle {
        color: #7f8c8d;
        font-size: 0.95rem;
        margin-bottom: 20px;
        line-height: 1.5;
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
        color: #3498db;
        font-size: 1rem;
    }
    
    .form-control, .form-select {
        border: 2px solid #e1e8ed;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
    }
    
    .help-text {
        display: block;
        color: #95a5a6;
        font-size: 0.875rem;
        margin-top: 5px;
    }
    
    .required {
        color: #e74c3c;
        font-weight: bold;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
    }
    
    .btn-cancel {
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        border: 2px solid #6c757d;
        color: #495057 !important;
        background: white;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #6c757d;
        border-color: #6c757d;
        color: white !important;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- HEADER -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-plus me-2"></i> Registrar Nuevo Cliente</h2>
                        <p style="color: white;">Complete la información del cliente para agregarlo al sistema</p>
                    </div>
                    <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- MENSAJES -->
            <?php if (session()->getFlashdata('errors') !== null) : ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <h5><i class="fas fa-exclamation-triangle"></i> Errores de Validación</h5>
                    <?php if (is_array(session()->getFlashdata('errors'))) : ?>
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $field => $error) : ?>
                                <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p class="mb-0"><?= session()->getFlashdata('errors') ?></p>
                    <?php endif; ?>
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dashboard/cliente/registrar') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- PASO 1: Información Básica -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">1</span>
                        <span><i class="fas fa-id-card icon-label"></i> Información Básica</span>
                    </div>
                    <p class="section-subtitle">
                        Tipo y nombre del cliente.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-users icon-label"></i>
                                    Tipo de Cliente
                                    <span class="required">*</span>
                                </label>
                                <select name="tipo_cliente" class="form-control" required>
                                    <option value="">-- Seleccione el tipo --</option>
                                    <option value="particular" <?= old('tipo_cliente') == 'particular' ? 'selected' : '' ?>>👤 Particular (Persona Natural)</option>
                                    <option value="empresa" <?= old('tipo_cliente') == 'empresa' ? 'selected' : '' ?>>🏢 Empresa (Persona Jurídica)</option>
                                    <option value="organizacion" <?= old('tipo_cliente') == 'organizacion' ? 'selected' : '' ?>>🏛️ Organización (Institución/ONG)</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Seleccione si es una persona, empresa u organización
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-signature icon-label"></i>
                                    Nombre / Razón Social
                                    <span class="required">*</span>
                                </label>
                                <input type="text" name="nombre_razon_social" class="form-control" 
                                       value="<?= old('nombre_razon_social') ?>" required
                                       placeholder="Ejemplo: Juan Pérez o Empresa S.A.">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Nombre completo del cliente o razón social de la empresa
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 2: Identificación y Contacto -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">2</span>
                        <span><i class="fas fa-address-card icon-label"></i> Identificación y Contacto</span>
                    </div>
                    <p class="section-subtitle">
                        Datos de identificación y formas de contacto principal.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-id-badge icon-label"></i>
                                    RUT / DNI
                                </label>
                                <input type="text" name="rut_dni" id="rut_dni" class="form-control" 
                                       value="<?= old('rut_dni') ?>" placeholder="12.345.678-9">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    RUT chileno o documento de identidad (se formatea automáticamente)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone icon-label"></i>
                                    Teléfono
                                </label>
                                <input type="text" name="telefono" class="form-control" 
                                       value="<?= old('telefono') ?>" placeholder="+56912345678">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Número de teléfono principal (incluir código país)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope icon-label"></i>
                                    Email
                                </label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?= old('email') ?>" placeholder="ejemplo@correo.com">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Correo electrónico principal del cliente
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-globe icon-label"></i>
                                    Sitio Web
                                </label>
                                <input type="url" name="sitio_web" class="form-control" 
                                       value="<?= old('sitio_web') ?>" placeholder="https://www.ejemplo.com">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Sitio web del cliente o empresa (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Persona de Contacto -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">3</span>
                        <span><i class="fas fa-user-tie icon-label"></i> Persona de Contacto</span>
                    </div>
                    <p class="section-subtitle">
                        Información de la persona de contacto (para empresas u organizaciones).
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Nombre del Contacto
                                </label>
                                <input type="text" name="contacto_nombre" class="form-control" 
                                       value="<?= old('contacto_nombre') ?>" placeholder="Ejemplo: María González">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Persona de enlace en la empresa (opcional)
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-briefcase icon-label"></i>
                                    Cargo del Contacto
                                </label>
                                <input type="text" name="contacto_cargo" class="form-control" 
                                       value="<?= old('contacto_cargo') ?>" placeholder="Ejemplo: Gerente General, Director">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Cargo o posición del contacto (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 4: Ubicación -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">4</span>
                        <span><i class="fas fa-map-marker-alt icon-label"></i> Ubicación</span>
                    </div>
                    <p class="section-subtitle">
                        Dirección y ubicación geográfica del cliente.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map icon-label"></i>
                                    Región
                                    <span class="required">*</span>
                                </label>
                                <select name="region_id" id="region_id" class="form-control" required>
                                    <option value="">-- Seleccione una región --</option>
                                    <!-- Las opciones se cargarán via AJAX -->
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Región donde se encuentra el cliente
                                </small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-city icon-label"></i>
                                    Comuna
                                    <span class="required">*</span>
                                </label>
                                <select name="comuna_id" id="comuna_id" class="form-control" required disabled>
                                    <option value="">Primero seleccione una región...</option>
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Comuna específica del cliente
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marked-alt icon-label"></i>
                                    Dirección
                                </label>
                                <input type="text" name="direccion" class="form-control" 
                                       value="<?= old('direccion') ?>" placeholder="Calle, número, departamento, información adicional">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Dirección completa del cliente (opcional)
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Información Adicional -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-clipboard icon-label"></i> Información Adicional</span>
                    </div>
                    <p class="section-subtitle">
                        Notas y observaciones relevantes sobre el cliente.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note icon-label"></i>
                                    Observaciones
                                </label>
                                <textarea name="observaciones" class="form-control" rows="4" 
                                          placeholder="Información adicional, preferencias, notas importantes..."><?= old('observaciones') ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Cualquier información adicional que sea relevante sobre el cliente
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="section-card" style="border-left-color: #28a745;">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-submit">
                                    <i class="fas fa-save me-2"></i> Guardar Cliente
                                </button>
                                <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-secondary btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <p class="text-center mt-3 mb-0 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revise toda la información antes de guardar
                            </p>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cargar regiones al inicializar
    cargarRegiones();
    
    // Manejar cambio de región
    $('#region_id').on('change', function() {
        var regionId = $(this).val();
        if (regionId) {
            cargarComunas(regionId);
        } else {
            $('#comuna_id').html('<option value="">Primero seleccione una región...</option>').prop('disabled', true);
        }
    });
    
    // Formatear RUT automáticamente
    $('#rut_dni').on('input', function() {
        var rut = $(this).val();
        var rutFormateado = formatearRut(rut);
        $(this).val(rutFormateado);
    });
});

function cargarRegiones() {
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/regiones') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">-- Seleccione una región --</option>';
                $.each(response.data, function(index, region) {
                    options += '<option value="' + region.id + '">' + region.nombre + '</option>';
                });
                $('#region_id').html(options);
                
                // Si hay un valor preseleccionado
                <?php if (old('region_id')): ?>
                    $('#region_id').val('<?= old('region_id') ?>').trigger('change');
                <?php endif; ?>
            }
        },
        error: function() {
            console.error('Error al cargar regiones');
            $('#region_id').html('<option value="">Error al cargar regiones</option>');
        }
    });
}

function cargarComunas(regionId) {
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/comunas') ?>/' + regionId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">-- Seleccione una comuna --</option>';
                $.each(response.data, function(index, comuna) {
                    options += '<option value="' + comuna.id + '">' + comuna.nombre + '</option>';
                });
                $('#comuna_id').html(options).prop('disabled', false);
                
                // Si hay un valor preseleccionado
                <?php if (old('comuna_id')): ?>
                    $('#comuna_id').val('<?= old('comuna_id') ?>');
                <?php endif; ?>
            }
        },
        error: function() {
            console.error('Error al cargar comunas');
            $('#comuna_id').html('<option value="">Error al cargar comunas</option>');
        }
    });
}

// Función para formatear RUT automáticamente
function formatearRut(rut) {
    // Limpiar el RUT
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length === 0) return '';
    
    // Separar número y dígito verificador
    var numero = rut.slice(0, -1);
    var dv = rut.slice(-1).toUpperCase();
    
    // Agregar puntos cada 3 dígitos desde la derecha
    if (numero.length > 0) {
        numero = numero.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    return numero + '-' + dv;
}
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cliente/editar') ?>

<style>
    /* Estilos generales para las cards de sección */
    .section-card {
        background: #ffffff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        padding: 30px;
        border-left: 6px solid #ff9800; /* Naranja para edición */
        transition: all 0.3s ease;
    }

    .section-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .section-title .step-number {
        background: #ff9800; /* Naranja para edición */
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(255, 152, 0, 0.3);
    }

    .section-title span {
        font-size: 1.6rem;
        font-weight: 700;
        color: #333;
    }

    .section-subtitle {
        font-size: 1.05rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 600;
        color: #444;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-label .icon-label {
        color: #ff9800; /* Naranja para edición */
        font-size: 1.1rem;
    }

    .form-control {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #ff9800; /* Naranja para edición */
        box-shadow: 0 0 0 0.2rem rgba(255, 152, 0, 0.25);
    }

    .help-text {
        font-size: 0.875rem;
        color: #888;
        margin-top: 5px;
        display: block;
    }

    .help-text .fas {
        color: #ff9800; /* Naranja para edición */
    }

    .main-header {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); /* Gradiente naranja */
        padding: 30px 40px;
        border-radius: 15px;
        margin-bottom: 30px;
        color: white;
        box-shadow: 0 8px 25px rgba(255, 152, 0, 0.3);
    }

    .main-header h2 {
        color: white;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 5px;
    }

    .main-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
    }

    .btn-light {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background-color: rgba(255, 255, 255, 0.3);
        color: white;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .btn-submit {
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
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

    .required {
        color: #dc3545;
        font-weight: 700;
    }
</style>

<div class="container-fluid">
    <div class="main-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 style="color: white;"><i class="fas fa-user-edit me-2"></i> Editar Cliente</h2>
                <p style="color: white;">Actualice la información del cliente: <strong><?= esc($cliente->nombre_razon_social) ?></strong></p>
            </div>
            <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-light">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i> Errores de Validación</h5>
                    <ul class="mb-0">
                        <?php 
                        $errors = session()->getFlashdata('errors');
                        if (is_array($errors)):
                            foreach ($errors as $field => $error): ?>
                                <li><strong><?= esc(ucfirst(str_replace('_', ' ', $field))) ?>:</strong> <?= esc($error) ?></li>
                            <?php endforeach;
                        else: ?>
                            <li><?= esc($errors) ?></li>
                        <?php endif; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('dashboard/cliente/update') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $cliente->id ?>">
                
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
                                <select name="tipo_cliente" id="tipo_cliente" class="form-control" required>
                                    <option value="">-- Seleccione el tipo --</option>
                                    <option value="particular" <?= old('tipo_cliente', $cliente->tipo_cliente) == 'particular' ? 'selected' : '' ?>>👤 Particular (Persona Natural)</option>
                                    <option value="empresa" <?= old('tipo_cliente', $cliente->tipo_cliente) == 'empresa' ? 'selected' : '' ?>>🏢 Empresa (Persona Jurídica)</option>
                                    <option value="organizacion" <?= old('tipo_cliente', $cliente->tipo_cliente) == 'organizacion' ? 'selected' : '' ?>>🏛️ Organización (Institución/ONG)</option>
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
                                       value="<?= old('nombre_razon_social', $cliente->nombre_razon_social) ?>" required
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
                                       value="<?= old('rut_dni', $cliente->rut_dni) ?>" placeholder="12.345.678-9">
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
                                       value="<?= old('telefono', $cliente->telefono) ?>" placeholder="+56912345678">
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
                                       value="<?= old('email', $cliente->email) ?>" placeholder="ejemplo@correo.com">
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
                                       value="<?= old('sitio_web', $cliente->sitio_web) ?>" placeholder="https://www.ejemplo.com">
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
                    
                    <div class="row" id="contacto_persona_fields">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user icon-label"></i>
                                    Nombre del Contacto
                                </label>
                                <input type="text" name="contacto_nombre" class="form-control" 
                                       value="<?= old('contacto_nombre', $cliente->contacto_nombre) ?>" placeholder="Ejemplo: María González">
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
                                       value="<?= old('contacto_cargo', $cliente->contacto_cargo) ?>" placeholder="Ejemplo: Gerente General, Director">
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
                        Dirección principal del cliente para fines de facturación o contacto.
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-globe-americas icon-label"></i>
                                    Región <span class="required">*</span>
                                </label>
                                <select name="region_id" id="region_id" class="form-control" required>
                                    <option value="">-- Seleccionar región --</option>
                                    <!-- Opciones cargadas por AJAX -->
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Región donde se ubica el cliente.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-city icon-label"></i>
                                    Comuna <span class="required">*</span>
                                </label>
                                <select name="comuna_id" id="comuna_id" class="form-control" required disabled>
                                    <option value="">-- Primero seleccione una región --</option>
                                    <!-- Opciones cargadas por AJAX -->
                                </select>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Comuna específica dentro de la región seleccionada.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-road icon-label"></i>
                                    Dirección Completa
                                </label>
                                <input type="text" name="direccion" class="form-control" 
                                       value="<?= old('direccion', $cliente->direccion) ?>" placeholder="Ej: Calle Falsa 123, Depto 4B">
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Dirección detallada del cliente (calle, número, departamento, etc.).
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PASO 5: Información Adicional -->
                <div class="section-card">
                    <div class="section-title">
                        <span class="step-number">5</span>
                        <span><i class="fas fa-sticky-note icon-label"></i> Información Adicional</span>
                    </div>
                    <p class="section-subtitle">
                        Notas internas o información adicional relevante sobre el cliente.
                    </p>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-sticky-note icon-label"></i>
                                    Observaciones Internas
                                </label>
                                <textarea name="observaciones" class="form-control" rows="4" 
                                          placeholder="Cualquier nota adicional sobre el cliente, preferencias, historial, etc."><?= old('observaciones', $cliente->observaciones) ?></textarea>
                                <small class="help-text">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Esta información es solo para uso interno y no será visible públicamente.
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
                                    <i class="fas fa-save me-2"></i> Actualizar Cliente
                                </button>
                                <a href="<?= base_url('dashboard/cliente/lista') ?>" class="btn btn-secondary btn-cancel">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </a>
                            </div>
                            <p class="text-center mt-3 mb-0 text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Revise toda la información antes de guardar los cambios
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
            $('#comuna_id').html('<option value="">-- Primero seleccione una región --</option>').prop('disabled', true);
        }
    });
    
    // Formatear RUT automáticamente
    $('#rut_dni').on('input', function() {
        var rut = $(this).val();
        var rutFormateado = formatearRut(rut);
        $(this).val(rutFormateado);
    });

    // Mostrar/ocultar campos de contacto según tipo de cliente
    $('#tipo_cliente').on('change', function() {
        toggleContactoFields($(this).val());
    });

    // Inicializar el estado de los campos de contacto al cargar la página
    toggleContactoFields($('#tipo_cliente').val());
});

function toggleContactoFields(tipoCliente) {
    const contactoFields = $('#contacto_persona_fields');
    if (tipoCliente === 'particular') {
        contactoFields.hide();
    } else {
        contactoFields.show();
    }
}

function cargarRegiones() {
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/regiones') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">-- Seleccionar región --</option>';
                $.each(response.data, function(index, region) {
                    options += '<option value="' + region.id + '">' + region.nombre + '</option>';
                });
                $('#region_id').html(options);
                
                // Si hay un valor preseleccionado (en modo edición)
                <?php if (old('region_id') || isset($cliente->region_id)): ?>
                    var regionId = '<?= old('region_id', $cliente->region_id ?? '') ?>';
                    if (regionId) {
                        $('#region_id').val(regionId).trigger('change');
                    }
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
                var options = '<option value="">-- Seleccionar comuna --</option>';
                $.each(response.data, function(index, comuna) {
                    options += '<option value="' + comuna.id + '">' + comuna.nombre + '</option>';
                });
                $('#comuna_id').html(options).prop('disabled', false);
                
                // Si hay un valor preseleccionado (en modo edición)
                <?php if (old('comuna_id') || isset($cliente->comuna_id)): ?>
                    var comunaId = '<?= old('comuna_id', $cliente->comuna_id ?? '') ?>';
                    if (comunaId) {
                        $('#comuna_id').val(comunaId);
                    }
                <?php endif; ?>
            }
        },
        error: function() {
            console.error('Error al cargar comunas');
            $('#comuna_id').html('<option value="">Error al cargar comunas</option>');
        }
    });
}

// Función para validar ubicación
function validarUbicacion() {
    var regionId = $('#region_id').val();
    var comunaId = $('#comuna_id').val();
    
    if (!regionId || !comunaId) {
        return true; // Dejar que la validación HTML5 maneje esto
    }
    
    $.ajax({
        url: '<?= base_url('dashboard/ubicacion/validar') ?>',
        type: 'POST',
        data: {
            region_id: regionId,
            comuna_id: comunaId
        },
        dataType: 'json',
        success: function(response) {
            if (!response.success || !response.valid) {
                alert('La comuna seleccionada no pertenece a la región elegida.');
                $('#comuna_id').focus();
                return false;
            }
        },
        error: function() {
            console.error('Error al validar ubicación');
        }
    });
    
    return true;
}

// Agregar validación al formulario
$('form').on('submit', function(e) {
    if (!validarUbicacion()) {
        e.preventDefault();
        return false;
    }
});

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

// Función para validar RUT en tiempo real
function validarRut(rut) {
    if (!rut || rut.trim() === '') return true; // Permitir vacío
    
    // Limpiar RUT
    rut = rut.replace(/[^0-9kK]/g, '');
    
    if (rut.length < 2) return false;
    
    var numero = rut.slice(0, -1);
    var dv = rut.slice(-1).toUpperCase();
    
    // Calcular dígito verificador
    var suma = 0;
    var multiplicador = 2;
    
    for (var i = numero.length - 1; i >= 0; i--) {
        suma += parseInt(numero[i]) * multiplicador;
        multiplicador++;
        if (multiplicador > 7) multiplicador = 2;
    }
    
    var resto = suma % 11;
    var dvCalculado = 11 - resto;
    
    if (dvCalculado === 11) dvCalculado = '0';
    else if (dvCalculado === 10) dvCalculado = 'K';
    else dvCalculado = dvCalculado.toString();
    
    return dv === dvCalculado;
}
</script>

<?= $this->endSection() ?>

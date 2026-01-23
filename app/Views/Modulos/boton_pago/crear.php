<?= $this->extend('layout/dashboard') ?>

<?= $this->section('boton_pago/crear') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
    }
    
    .btn-generar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 35px;
        font-size: 1.05rem;
        font-weight: 600;
        border-radius: 8px;
    }
    
    .boton-url-container {
        background: #f8f9fa;
        border: 2px dashed #667eea;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
    
    .boton-url {
        font-family: monospace;
        background: white;
        padding: 10px;
        border-radius: 4px;
        word-break: break-all;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;">
                            <i class="fas <?= isset($es_edicion) && $es_edicion ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i> 
                            <?= isset($es_edicion) && $es_edicion ? 'Editar' : 'Crear' ?> Plantilla de Pago
                        </h2>
                        <p style="color: white;">
                            <?= isset($es_edicion) && $es_edicion ? 'Modifica los datos de la plantilla' : 'Genera un botón de pago para compartir con tus pacientes' ?>
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>

            <?php if (isset($cita) && $cita): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Cita seleccionada:</strong> 
                    <?php if (isset($cita->paciente) && $cita->paciente): ?>
                        <?= esc($cita->paciente->nombre . ' ' . ($cita->paciente->apellido ?? '')) ?>
                        <?php if (isset($cita->fecha)): ?>
                            - <?= date('d/m/Y H:i', strtotime($cita->fecha)) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form id="formCrearBoton" class="section-card">
                <?= csrf_field() ?>
                
                <?php if (isset($es_edicion) && $es_edicion && isset($plantilla)): ?>
                    <input type="hidden" name="plantilla_id" value="<?= $plantilla->id ?? $plantilla['id'] ?? '' ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Título del Pago <span class="text-danger">*</span></label>
                            <input type="text" name="titulo" class="form-control" 
                                   value="<?= isset($plantilla) ? esc($plantilla->titulo ?? $plantilla['titulo'] ?? '') : (isset($cita) && $cita ? 'Consulta Nutricional - ' . date('d/m/Y', strtotime($cita->fecha ?? 'now')) : '') ?>" 
                                   required placeholder="Ej: Consulta Nutricional">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Monto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto" class="form-control" 
                                       value="<?= isset($plantilla) ? ($plantilla->monto ?? $plantilla['monto'] ?? '') : '' ?>"
                                       required placeholder="0.00" min="0.01">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label>Moneda</label>
                            <select name="moneda" class="form-control">
                                <option value="CLP" <?= (isset($plantilla) && ($plantilla->moneda ?? $plantilla['moneda'] ?? 'CLP') === 'CLP') ? 'selected' : 'selected' ?>>CLP (Peso Chileno)</option>
                                <option value="USD" <?= (isset($plantilla) && ($plantilla->moneda ?? $plantilla['moneda'] ?? '') === 'USD') ? 'selected' : '' ?>>USD (Dólar)</option>
                                <option value="ARS" <?= (isset($plantilla) && ($plantilla->moneda ?? $plantilla['moneda'] ?? '') === 'ARS') ? 'selected' : '' ?>>ARS (Peso Argentino)</option>
                                <option value="BRL" <?= (isset($plantilla) && ($plantilla->moneda ?? $plantilla['moneda'] ?? '') === 'BRL') ? 'selected' : '' ?>>BRL (Real Brasileño)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label>Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2" 
                                      placeholder="Descripción del pago (opcional)"><?= isset($plantilla) ? esc($plantilla->descripcion ?? $plantilla['descripcion'] ?? '') : (isset($cita) && $cita ? 'Pago de consulta nutricional' : '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="es_plantilla" id="es_plantilla" value="1" 
                                   <?= (isset($es_edicion) && $es_edicion) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="es_plantilla">
                                <strong>Guardar como plantilla reutilizable</strong>
                                <br>
                                <small class="text-muted">
                                    Si marcas esta opción, se guardará como plantilla genérica que podrás usar al agendar citas. 
                                    No se creará un pago específico, solo la plantilla. Los datos del paciente se completarán automáticamente al usar la plantilla.
                                </small>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="datosPagador">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>Email del Paciente (Pagador) <span class="text-danger" id="emailRequired">*</span></label>
                                <input type="email" name="email_pagador" id="email_pagador" class="form-control" 
                                       value="<?= isset($cita) && isset($cita->paciente) && $cita->paciente ? esc($cita->paciente->email ?? '') : '' ?>" 
                                       placeholder="email@ejemplo.com">
                                <small class="form-text text-muted">
                                    Email del paciente que realizará el pago. Mercado Pago enviará notificaciones a este correo.
                                    <span id="emailPlantillaNote" style="display: none;">(No requerido para plantillas)</span>
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Nombre del Paciente</label>
                                <input type="text" name="nombre_pagador" class="form-control" 
                                       value="<?= isset($cita) && isset($cita->paciente) && $cita->paciente ? esc($cita->paciente->nombre ?? '') : '' ?>" 
                                       placeholder="Nombre">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label>Apellido del Paciente</label>
                                <input type="text" name="apellido_pagador" class="form-control" 
                                       value="<?= isset($cita) && isset($cita->paciente) && $cita->paciente ? esc($cita->paciente->apellido ?? '') : '' ?>" 
                                       placeholder="Apellido">
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (isset($cita) && $cita && isset($cita->id)): ?>
                    <input type="hidden" name="detalle_agenda_id" value="<?= $cita->id ?>">
                <?php endif; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-generar" id="btnGenerar">
                        <i class="fas fa-magic me-2"></i> <span id="btnText">Generar Botón de Pago</span>
                    </button>
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </a>
                </div>
            </form>

            <!-- Contenedor para mostrar el botón generado -->
            <div id="botonGenerado" class="section-card" style="display: none;">
                <h4><i class="fas fa-check-circle text-success me-2"></i> Botón de Pago Generado</h4>
                <p class="text-muted">Copia el siguiente enlace y compártelo con el paciente:</p>
                
                <div class="boton-url-container">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>URL del Botón de Pago:</strong>
                        <button class="btn btn-sm btn-primary" onclick="copiarUrl()">
                            <i class="fas fa-copy me-1"></i> Copiar
                        </button>
                    </div>
                    <div class="boton-url" id="urlBoton"></div>
                </div>

                <div class="mt-3">
                    <a href="<?= base_url('dashboard/boton-pago/lista') ?>" class="btn btn-primary">
                        <i class="fas fa-list me-2"></i> Ver Todos los Botones
                    </a>
                    <a href="#" id="linkVerPago" class="btn btn-info" style="display: none;">
                        <i class="fas fa-eye me-2"></i> Ver Detalles del Pago
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Cambiar texto del botón y mostrar/ocultar campos según si es plantilla o no
    // Si es edición, marcar como plantilla y ocultar campos de pagador
    <?php if (isset($es_edicion) && $es_edicion): ?>
    $('#es_plantilla').prop('checked', true);
    $('#datosPagador').hide();
    $('#emailRequired').hide();
    $('#email_pagador').removeAttr('required');
    <?php endif; ?>
    
    $('#es_plantilla').on('change', function() {
        const esPlantilla = $(this).is(':checked');
        const emailField = $('#email_pagador');
        
        if (esPlantilla) {
            $('#btnText').text('Guardar Plantilla');
            // Hacer que el email no sea requerido
            emailField.prop('required', false);
            $('#emailRequired').hide();
            $('#emailPlantillaNote').show();
            // Opcional: ocultar completamente los datos del pagador para plantillas
            // $('#datosPagador').slideUp();
        } else {
            $('#btnText').text('Generar Botón de Pago');
            // Hacer que el email sea requerido
            emailField.prop('required', true);
            $('#emailRequired').show();
            $('#emailPlantillaNote').hide();
            // Mostrar los datos del pagador
            // $('#datosPagador').slideDown();
        }
    });
    
    $('#formCrearBoton').on('submit', function(e) {
        e.preventDefault();
        
        const btnGenerar = $('#btnGenerar');
        const originalText = btnGenerar.html();
        btnGenerar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Generando...');
        
        // Obtener token CSRF del formulario
        var csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
        if (!csrfToken) {
            toastr.error('Token de seguridad no encontrado. Por favor, recarga la página.', 'Error');
            return;
        }
        
        const formData = $(this).serialize();
        
        // Asegurar que el token CSRF esté en los datos
        if (formData.indexOf('<?= csrf_token() ?>') === -1) {
            formData += '&<?= csrf_token() ?>=' + encodeURIComponent(csrfToken);
        }
        
        $.ajax({
            url: '<?= base_url('dashboard/boton-pago/generar') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {
                // Enviar token en header también
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar token CSRF
                    if (response.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                    }
                    
                    // Si es plantilla, mostrar mensaje diferente
                    if (response.es_plantilla) {
                        toastr.success(response.message || 'Plantilla creada exitosamente', '¡Éxito!');
                        setTimeout(function() {
                            window.location.href = '<?= base_url('dashboard/boton-pago/lista') ?>';
                        }, 2000);
                    } else {
                        // Mostrar el botón generado
                        $('#urlBoton').text(response.boton_url);
                        $('#linkVerPago').attr('href', '<?= base_url('dashboard/boton-pago/ver') ?>/' + response.pago_id).show();
                        $('#botonGenerado').slideDown();
                        $('#formCrearBoton').slideUp();
                        
                        toastr.success('Botón de pago generado exitosamente', '¡Éxito!');
                    }
                } else {
                    toastr.error(response.error || 'Error al generar el botón', 'Error');
                    btnGenerar.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al generar el botón de pago';
                
                // Manejar error CSRF específicamente
                if (xhr.status === 403) {
                    errorMsg = 'Error de seguridad (CSRF). Por favor, recarga la página e intenta de nuevo.';
                    // Recargar la página para obtener un nuevo token
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData.message) {
                            errorMsg = errorData.message;
                        }
                    } catch (e) {
                        // Si no es JSON, usar el texto de respuesta
                        errorMsg = 'Error: ' + xhr.responseText.substring(0, 200);
                    }
                }
                
                toastr.error(errorMsg, 'Error');
                btnGenerar.prop('disabled', false).html(originalText);
                
                // Actualizar token CSRF
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) {
                    $('input[name="<?= csrf_token() ?>"]').val(headerToken);
                } else if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
                    $('input[name="<?= csrf_token() ?>"]').val(xhr.responseJSON.csrf_hash);
                }
            }
        });
    });
});

function copiarUrl() {
    const url = $('#urlBoton').text();
    navigator.clipboard.writeText(url).then(function() {
        toastr.success('URL copiada al portapapeles', '¡Copiado!');
    }, function() {
        // Fallback para navegadores antiguos
        const textarea = document.createElement('textarea');
        textarea.value = url;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        toastr.success('URL copiada al portapapeles', '¡Copiado!');
    });
}
</script>

<?= $this->endSection() ?>

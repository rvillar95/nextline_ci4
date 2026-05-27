<?= $this->extend('layout/dashboard') ?>

<?= $this->section('configuracion/index') ?>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/k10uo8qhvhuxj1ho5z73jcbhzpwlspewyrz3lkbu5b99faon/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<!-- Toastr para notificaciones -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-cog me-2"></i> Configuraciones del Sistema</h2>
                        <p style="color: white;">
                            <?php if (isset($empresa) && $empresa): ?>
                                <?= esc($empresa->nombre) ?> · Notificaciones, calendario, cancelaciones y Mercado Pago
                            <?php else: ?>
                                Notificaciones, integración con calendario, mensajes de cancelación y Mercado Pago
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form id="formConfiguracion">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">

                        <!-- Sección: Notificaciones -->
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-bell mr-2"></i>
                                    Notificaciones
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="enviar_email" 
                                               name="enviar_email" <?= ($configuracion['enviar_email'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enviar_email">
                                            <strong>Enviar correo electrónico</strong>
                                            <br>
                                            <small class="text-muted">Enviar email al paciente cuando se agenda una cita</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="enviar_whatsapp" 
                                               name="enviar_whatsapp" <?= ($configuracion['enviar_whatsapp'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enviar_whatsapp">
                                            <strong>Enviar WhatsApp</strong>
                                            <br>
                                            <small class="text-muted">Enviar mensaje de WhatsApp cuando el paciente confirma la cita desde el email</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="enviar_recordatorios_whatsapp" 
                                               name="enviar_recordatorios_whatsapp" <?= ($configuracion['enviar_recordatorios_whatsapp'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enviar_recordatorios_whatsapp">
                                            <strong>Enviar recordatorios por WhatsApp</strong>
                                            <br>
                                            <small class="text-muted">Enviar recordatorios automáticos de citas próximas por WhatsApp</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group" id="horasRecordatorioGroup" style="<?= ($configuracion['enviar_recordatorios_whatsapp'] ?? 1) ? '' : 'display: none;' ?>">
                                    <label for="horas_antes_recordatorio">
                                        <strong>Horas antes del recordatorio</strong>
                                    </label>
                                    <input type="number" class="form-control" id="horas_antes_recordatorio" 
                                           name="horas_antes_recordatorio" 
                                           value="<?= $configuracion['horas_antes_recordatorio'] ?? 24 ?>" 
                                           min="1" max="168" required>
                                    <small class="form-text text-muted">
                                        Número de horas antes de la cita para enviar el recordatorio (1-168 horas / 1-7 días)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Calendario -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Integración con Calendario
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong> Para usar la integración con calendario, primero debes conectar tu cuenta de Google Calendar o Outlook desde el módulo de Agenda.
                                </div>

                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="crear_evento_calendario" 
                                               name="crear_evento_calendario" <?= ($configuracion['crear_evento_calendario'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="crear_evento_calendario">
                                            <strong>Crear evento en calendario</strong>
                                            <br>
                                            <small class="text-muted">Crear automáticamente un evento en tu calendario (Google Calendar/Outlook) cuando el paciente confirma la cita desde el email</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group" id="agregarInvitadoGroup" style="<?= ($configuracion['crear_evento_calendario'] ?? 1) ? '' : 'display: none;' ?>">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="agregar_paciente_como_invitado" 
                                               name="agregar_paciente_como_invitado" <?= ($configuracion['agregar_paciente_como_invitado'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="agregar_paciente_como_invitado">
                                            <strong>Agregar paciente como invitado</strong>
                                            <br>
                                            <small class="text-muted">Agregar el email del paciente como invitado al evento del calendario (solo si el paciente tiene email)</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Importante:</strong> El evento se crea en <strong>tu calendario</strong> (el del nutricionista). 
                                    Las notificaciones y recordatorios del calendario llegarán a <strong>tu correo</strong>, no al del paciente.
                                    El paciente solo recibirá una invitación si está configurado como invitado.
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Mensajes de Cancelación Masiva -->
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-comment-alt mr-2"></i>
                                    Mensajes de Cancelación Masiva
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Variables disponibles:</strong> [NOMBRE_PACIENTE], [FECHA], [HORA], [NOMBRE_NUTRICIONISTA]
                                </div>

                                <div class="form-group">
                                    <label for="mensaje_cancelacion_pendiente">
                                        <strong>Mensaje para citas en estado Pendiente</strong>
                                    </label>
                                    <textarea class="form-control tinymce-editor" id="mensaje_cancelacion_pendiente" 
                                              name="mensaje_cancelacion_pendiente" rows="6"><?= esc($configuracion['mensaje_cancelacion_pendiente'] ?? 'Estimado/a [NOMBRE_PACIENTE],\n\nLamentamos informarle que su cita programada para el [FECHA] a las [HORA] ha sido cancelada.\n\nPor favor, contáctenos para reagendar su consulta.\n\nSaludos,\n[NOMBRE_NUTRICIONISTA]') ?></textarea>
                                    <small class="form-text text-muted">
                                        Mensaje que se enviará cuando se cancele una cita en estado "Pendiente"
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="mensaje_cancelacion_confirmada">
                                        <strong>Mensaje para citas en estado Confirmada</strong>
                                    </label>
                                    <textarea class="form-control tinymce-editor" id="mensaje_cancelacion_confirmada" 
                                              name="mensaje_cancelacion_confirmada" rows="6"><?= esc($configuracion['mensaje_cancelacion_confirmada'] ?? 'Estimado/a [NOMBRE_PACIENTE],\n\nLamentamos informarle que su cita confirmada para el [FECHA] a las [HORA] ha sido cancelada.\n\nPor favor, contáctenos para reagendar su consulta.\n\nSaludos,\n[NOMBRE_NUTRICIONISTA]') ?></textarea>
                                    <small class="form-text text-muted">
                                        Mensaje que se enviará cuando se cancele una cita en estado "Confirmada"
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="mensaje_cancelacion_en_proceso">
                                        <strong>Mensaje para citas en estado En Proceso</strong>
                                    </label>
                                    <textarea class="form-control tinymce-editor" id="mensaje_cancelacion_en_proceso" 
                                              name="mensaje_cancelacion_en_proceso" rows="6"><?= esc($configuracion['mensaje_cancelacion_en_proceso'] ?? 'Estimado/a [NOMBRE_PACIENTE],\n\nLamentamos informarle que su cita programada para el [FECHA] a las [HORA] ha sido cancelada.\n\nPor favor, contáctenos para reagendar su consulta.\n\nSaludos,\n[NOMBRE_NUTRICIONISTA]') ?></textarea>
                                    <small class="form-text text-muted">
                                        Mensaje que se enviará cuando se cancele una cita en estado "En Proceso"
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Mercado Pago (cobros a pacientes) -->
                        <?php if (isset($tieneAccesoBotonesPago) && $tieneAccesoBotonesPago): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Mercado Pago
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Nota:</strong> Configura Mercado Pago para cobrar consultas con las tarifas que definas en el menú «Tarifas de consulta». 
                                    Puedes obtener tus credenciales desde tu <a href="https://www.mercadopago.com.mx/developers/panel" target="_blank" class="alert-link">panel de desarrolladores</a>.
                                </div>

                                <div class="form-group">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" id="mp_habilitado" 
                                               name="mp_habilitado" <?= ($configuracion['mp_habilitado'] ?? 0) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="mp_habilitado">
                                            <strong>Habilitar Mercado Pago</strong>
                                            <br>
                                            <small class="text-muted">Activar la integración con Mercado Pago para esta empresa</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group" id="mpConfigGroup" style="<?= ($configuracion['mp_habilitado'] ?? 0) ? '' : 'display: none;' ?>">
                                    <label for="mp_mode">
                                        <strong>Modo Activo</strong>
                                    </label>
                                    <select class="form-control" id="mp_mode" name="mp_mode" required>
                                        <option value="sandbox" <?= ($configuracion['mp_mode'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' ?>>Sandbox (Pruebas)</option>
                                        <option value="production" <?= ($configuracion['mp_mode'] ?? 'sandbox') === 'production' ? 'selected' : '' ?>>Producción</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Selecciona qué credenciales usar. Puedes configurar ambas y cambiar entre ellas fácilmente.
                                    </small>
                                </div>

                                <!-- Credenciales Sandbox -->
                                <div id="mpSandboxGroup" style="<?= ($configuracion['mp_habilitado'] ?? 0) ? '' : 'display: none;' ?>">
                                    <h5 class="mt-4 mb-3" style="color: #f0841a;">
                                        <i class="fas fa-flask mr-2"></i>Credenciales Sandbox (Pruebas)
                                    </h5>
                                    
                                    <div class="form-group">
                                        <label for="mp_access_token_sandbox">
                                            <strong>Access Token (Sandbox)</strong>
                                        </label>
                                        <input type="text" class="form-control" id="mp_access_token_sandbox" 
                                               name="mp_access_token_sandbox" 
                                               value="<?= esc($configuracion['mp_access_token_sandbox'] ?? $configuracion['mp_access_token'] ?? '') ?>" 
                                               placeholder="TEST-XXXXXXXXXXXXXXX"
                                               autocomplete="off">
                                        <small class="form-text text-muted">
                                            Token de acceso privado de Mercado Pago para pruebas (Sandbox). Comienza con "TEST-"
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="mp_public_key_sandbox">
                                            <strong>Public Key (Sandbox)</strong>
                                        </label>
                                        <input type="text" class="form-control" id="mp_public_key_sandbox" 
                                               name="mp_public_key_sandbox" 
                                               value="<?= esc($configuracion['mp_public_key_sandbox'] ?? $configuracion['mp_public_key'] ?? '') ?>" 
                                               placeholder="TEST-XXXXXXXXXXXXXXX"
                                               autocomplete="off">
                                        <small class="form-text text-muted">
                                            Clave pública de Mercado Pago para pruebas (Sandbox). Comienza con "TEST-"
                                        </small>
                                    </div>
                                </div>

                                <!-- Credenciales Production -->
                                <div id="mpProductionGroup" style="<?= ($configuracion['mp_habilitado'] ?? 0) ? '' : 'display: none;' ?>">
                                    <h5 class="mt-4 mb-3" style="color: #28a745;">
                                        <i class="fas fa-check-circle mr-2"></i>Credenciales Producción
                                    </h5>
                                    
                                    <div class="form-group">
                                        <label for="mp_access_token_production">
                                            <strong>Access Token (Producción)</strong>
                                        </label>
                                        <input type="text" class="form-control" id="mp_access_token_production" 
                                               name="mp_access_token_production" 
                                               value="<?= esc($configuracion['mp_access_token_production'] ?? '') ?>" 
                                               placeholder="APP_USR-XXXXXXXXXXXXXXX"
                                               autocomplete="off">
                                        <small class="form-text text-muted">
                                            Token de acceso privado de Mercado Pago para producción. Comienza con "APP_USR-"
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="mp_public_key_production">
                                            <strong>Public Key (Producción)</strong>
                                        </label>
                                        <input type="text" class="form-control" id="mp_public_key_production" 
                                               name="mp_public_key_production" 
                                               value="<?= esc($configuracion['mp_public_key_production'] ?? '') ?>" 
                                               placeholder="APP_USR-XXXXXXXXXXXXXXX"
                                               autocomplete="off">
                                        <small class="form-text text-muted">
                                            Clave pública de Mercado Pago para producción. Comienza con "APP_USR-"
                                        </small>
                                    </div>
                                </div>

                                <div class="alert alert-warning" id="mpWarningGroup" style="<?= ($configuracion['mp_habilitado'] ?? 0) ? '' : 'display: none;' ?>">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <strong>Importante:</strong> 
                                    <ul class="mb-0 mt-2">
                                        <li>Las credenciales son específicas por empresa y se almacenan de forma segura</li>
                                        <li>No compartas tus credenciales con nadie</li>
                                        <li>En modo Producción, asegúrate de configurar el webhook en tu panel de Mercado Pago</li>
                                        <li>El webhook debe apuntar a: <code><?= base_url('api/mercadopago/webhook') ?></code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Configuraciones
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="restaurarDefaults()">
                                <i class="fas fa-undo mr-2"></i>
                                Restaurar Valores por Defecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mostrar/ocultar campos dependientes
    $('#enviar_recordatorios_whatsapp').change(function() {
        if ($(this).is(':checked')) {
            $('#horasRecordatorioGroup').show();
        } else {
            $('#horasRecordatorioGroup').hide();
        }
    });

    $('#crear_evento_calendario').change(function() {
        if ($(this).is(':checked')) {
            $('#agregarInvitadoGroup').show();
        } else {
            $('#agregarInvitadoGroup').hide();
        }
    });

    // Mostrar/ocultar campos de Mercado Pago
    $('#mp_habilitado').change(function() {
        if ($(this).is(':checked')) {
            $('#mpConfigGroup').show();
            $('#mpSandboxGroup').show();
            $('#mpProductionGroup').show();
            $('#mpWarningGroup').show();
        } else {
            $('#mpConfigGroup').hide();
            $('#mpSandboxGroup').hide();
            $('#mpProductionGroup').hide();
            $('#mpWarningGroup').hide();
        }
    });

    // Inicializar TinyMCE para los editores de mensajes
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '.tinymce-editor',
            height: 200,
            menubar: false,
            plugins: ['lists', 'link', 'code'],
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
            language: 'es',
            branding: false,
            promotion: false
        });
    }

    // Enviar formulario
    $('#formConfiguracion').on('submit', function(e) {
        e.preventDefault();

        // Obtener contenido de TinyMCE antes de serializar
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }

        const formData = $(this).serialize();
        
        console.log('Enviando datos:', formData);
        console.log('URL:', '<?= base_url('dashboard/configuracion/guardar') ?>');
        
        $.ajax({
            url: '<?= base_url('dashboard/configuracion/guardar') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function(xhr) {
                // Asegurar que el token CSRF se envíe en el header también
                var csrfToken = $('input[name="<?= csrf_token() ?>"]').val();
                if (csrfToken) {
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                }
            },
            success: function(response, textStatus, xhr) {
                console.log('Respuesta recibida:', response);
                console.log('Status:', textStatus);
                
                // Actualizar token CSRF después de la respuesta
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) {
                    $('input[name="<?= csrf_token() ?>"]').val(headerToken);
                }
                
                if (response && response.success) {
                    // Actualizar CSRF token del response también
                    if (response.csrf_token) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token);
                    }
                    
                    // Mostrar mensaje de éxito con toastr
                    toastr.success(response.message || 'Configuraciones guardadas exitosamente', '¡Éxito!', {
                        timeOut: 3000,
                        progressBar: true
                    });
                } else {
                    var errorMsg = (response && response.error) ? response.error : 'Error al guardar las configuraciones';
                    toastr.error(errorMsg, 'Error', {
                        timeOut: 5000,
                        progressBar: true
                    });
                }
            },
            error: function(xhr) {
                console.error('Error al guardar:', xhr);
                console.error('Status:', xhr.status);
                console.error('Response:', xhr.responseJSON);
                
                let errorMsg = 'Error al guardar las configuraciones';
                if (xhr.status === 403) {
                    errorMsg = 'Error 403: No tiene permisos para guardar configuraciones o el token CSRF es inválido. Por favor, recarga la página e intenta de nuevo.';
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.responseText) {
                    errorMsg = 'Error: ' + xhr.responseText.substring(0, 200);
                }
                
                // Actualizar token CSRF incluso en caso de error
                var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (headerToken) {
                    $('input[name="<?= csrf_token() ?>"]').val(headerToken);
                }
                
                toastr.error(errorMsg, 'Error', {
                    timeOut: 5000,
                    progressBar: true
                });
            }
        });
    });
});

function restaurarDefaults() {
    Swal.fire({
        title: '¿Restaurar valores por defecto?',
        text: 'Esto restablecerá todas las configuraciones a sus valores predeterminados',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, restaurar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Restaurar valores por defecto
            $('#enviar_email').prop('checked', true);
            $('#enviar_whatsapp').prop('checked', true);
            $('#crear_evento_calendario').prop('checked', true);
            $('#agregar_paciente_como_invitado').prop('checked', true);
            $('#enviar_recordatorios_whatsapp').prop('checked', true);
            $('#horas_antes_recordatorio').val(24);
            
            $('#horasRecordatorioGroup').show();
            $('#agregarInvitadoGroup').show();
            
            Swal.fire('Restaurado', 'Valores por defecto restaurados. No olvides guardar.', 'info');
        }
    });
}
</script>
<?= $this->endSection() ?>

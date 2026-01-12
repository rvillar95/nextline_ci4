<?= $this->extend('layout/dashboard') ?>

<?= $this->section('configuracion/index') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-2"></i>
                        Configuraciones del Sistema
                    </h3>
                </div>
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

    // Enviar formulario
    $('#formConfiguracion').on('submit', function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        
        $.ajax({
            url: '<?= base_url('dashboard/configuracion/guardar') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message || 'Configuraciones guardadas exitosamente',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Actualizar CSRF token
                    if (response.csrf_token) {
                        $('input[name="<?= csrf_token() ?>"]').val(response.csrf_token);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error || 'Error al guardar las configuraciones'
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error al guardar las configuraciones';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMsg
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

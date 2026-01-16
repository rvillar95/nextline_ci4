<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/cancelar_horas') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/l10n/es.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        border-left: 4px solid #f5576c;
    }

    .cita-item {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
        transition: all 0.3s ease;
    }

    .cita-item:hover {
        border-color: #f5576c;
        box-shadow: 0 2px 8px rgba(245, 87, 108, 0.2);
    }

    .cita-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .cita-info {
        flex: 1;
    }

    .cita-estado {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    .estado-pendiente {
        background-color: #FFF3CD;
        color: #856404;
    }

    .estado-confirmada {
        background-color: #D1ECF1;
        color: #0C5460;
    }

    .estado-en_proceso {
        background-color: #D4EDDA;
        color: #155724;
    }

    .mensaje-personalizado {
        margin-top: 10px;
    }

    .mensaje-personalizado textarea {
        min-height: 80px;
        resize: vertical;
    }

    .resumen-cancelacion {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-calendar-times me-2"></i> Cancelar Horas Masivamente</h2>
                        <p style="color: white;">Cancela todas las citas en un rango de fechas por emergencia o enfermedad</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Calendario
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h4 class="mb-4"><i class="fas fa-calendar-alt me-2"></i> Seleccionar Rango de Fechas</h4>
                
                <form id="formCancelarHoras">
                    <!-- Token CSRF hidden -->
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_token_input">
                    
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Fecha Inicio <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                <small class="form-text text-muted">Fecha desde la cual cancelar citas</small>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Fecha Fin <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                <small class="form-text text-muted">Fecha hasta la cual cancelar citas</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100" id="btnVerCitas">
                                    <i class="fas fa-search me-2"></i> Ver Citas
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Resumen de citas -->
            <div id="resumenCitas" style="display: none;">
                <div class="section-card">
                    <div class="resumen-cancelacion">
                        <h5><i class="fas fa-info-circle me-2"></i> Resumen</h5>
                        <p class="mb-0">
                            <strong id="totalConPaciente">0</strong> citas con pacientes asignados
                            <span class="mx-2">|</span>
                            <strong id="totalSinPaciente">0</strong> citas sin pacientes (se cancelarán automáticamente)
                            <span class="mx-2">|</span>
                            <strong id="totalCitas">0</strong> citas en total
                        </p>
                    </div>

                    <h5 class="mt-4 mb-3"><i class="fas fa-users me-2"></i> Citas con Pacientes (Personalizar Mensajes)</h5>
                    <div id="listaCitas">
                        <!-- Se llenará dinámicamente -->
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-danger btn-lg" id="btnConfirmarCancelacion" disabled>
                            <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Cancelación Masiva
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg ms-2" id="btnCancelar">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="modalConfirmarCancelacion" tabindex="-1" aria-labelledby="modalConfirmarCancelacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmarCancelacionLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Cancelación Masiva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-3">¿Está seguro de cancelar todas estas citas?</h5>
                <p class="text-center text-muted">
                    Esta acción cancelará <strong id="modalTotalCitas">0</strong> citas y no se puede deshacer.
                </p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Advertencia:</strong> Se enviarán notificaciones por WhatsApp y Email a todos los pacientes afectados.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarModal">
                    <i class="fas fa-check me-2"></i> Sí, Cancelar Todas las Citas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var citasData = [];
var mensajesPersonalizados = {};

// Función para obtener el token CSRF
function obtenerTokenCSRF() {
    // Intentar obtener del input hidden primero
    var inputToken = $('#csrf_token_input').val();
    if (inputToken) {
        return inputToken;
    }
    // Si no está en el input, intentar del meta tag
    var metaToken = $('meta[name="csrf-token"]').attr('content');
    if (metaToken) {
        return metaToken;
    }
    // Último recurso: usar el hash del servidor
    return '<?= csrf_hash() ?>';
}

// Función para actualizar el token CSRF después de cada petición
function actualizarTokenCSRF(xhr) {
    // Intentar obtener del header
    var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
    if (headerToken) {
        $('#csrf_token_input').val(headerToken);
        $('meta[name="csrf-token"]').attr('content', headerToken);
        return;
    }
    // O de la respuesta JSON si está disponible
    if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
        var jsonToken = xhr.responseJSON.csrf_token;
        $('#csrf_token_input').val(jsonToken);
        $('meta[name="csrf-token"]').attr('content', jsonToken);
        return;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Flatpickr para fechas
    flatpickr("#fecha_inicio", {
        dateFormat: "d-m-Y",
        locale: "es",
        minDate: "today",
        allowInput: true
    });

    flatpickr("#fecha_fin", {
        dateFormat: "d-m-Y",
        locale: "es",
        minDate: "today",
        allowInput: true
    });

    // Botón ver citas
    $('#btnVerCitas').on('click', function() {
        var fechaInicio = $('#fecha_inicio').val();
        var fechaFin = $('#fecha_fin').val();

        if (!fechaInicio || !fechaFin) {
            toastr.error('Por favor seleccione ambas fechas');
            return;
        }

        // Validar que fecha fin sea mayor o igual a fecha inicio
        var fechaInicioDate = new Date(fechaInicio.split('-').reverse().join('-'));
        var fechaFinDate = new Date(fechaFin.split('-').reverse().join('-'));
        
        if (fechaFinDate < fechaInicioDate) {
            toastr.error('La fecha fin debe ser mayor o igual a la fecha inicio');
            return;
        }

        obtenerCitas(fechaInicio, fechaFin);
    });

    // Botón confirmar cancelación - Abrir modal
    $('#btnConfirmarCancelacion').on('click', function() {
        var totalCitas = $('#totalCitas').text() || '0';
        $('#modalTotalCitas').text(totalCitas);
        $('#modalConfirmarCancelacion').modal('show');
    });

    // Botón confirmar en el modal
    $('#btnConfirmarModal').on('click', function() {
        $('#modalConfirmarCancelacion').modal('hide');
        procesarCancelacion();
    });

    // Botón cancelar
    $('#btnCancelar').on('click', function() {
        window.location.href = '<?= base_url("dashboard/agenda/calendario") ?>';
    });
});

function obtenerCitas(fechaInicio, fechaFin) {
    var csrfToken = obtenerTokenCSRF();
    var csrfName = '<?= csrf_token() ?>';
    
    $.ajax({
        url: '<?= base_url("dashboard/agenda/cancelar-horas/obtener-citas") ?>',
        type: 'POST',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            [csrfName]: csrfToken
        },
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de la respuesta exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                citasData = response.citas_con_paciente || [];
                
                $('#totalConPaciente').text(response.total_con_paciente || 0);
                $('#totalSinPaciente').text(response.total_sin_paciente || 0);
                $('#totalCitas').text(response.total || 0);
                
                mostrarCitas(citasData);
                $('#resumenCitas').show();
                $('#btnConfirmarCancelacion').prop('disabled', false);
                
                // Actualizar token CSRF desde la respuesta
                if (response.csrf_token) {
                    $('#csrf_token_input').val(response.csrf_token);
                }
            } else {
                toastr.error(response.error || 'Error al obtener citas');
            }
        },
        error: function(xhr) {
            // Actualizar token CSRF incluso en caso de error
            actualizarTokenCSRF(xhr);
            
            var error = 'Error al obtener citas';
            if (xhr.status === 403) {
                error = 'Error 403: Token CSRF inválido. Por favor, recarga la página e intenta de nuevo.';
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                error = xhr.responseJSON.error;
            }
            toastr.error(error);
        }
    });
}

function mostrarCitas(citas) {
    var html = '';
    
    if (citas.length === 0) {
        html = '<div class="alert alert-info">No hay citas con pacientes en el rango seleccionado.</div>';
    } else {
        citas.forEach(function(cita) {
            var estadoClass = 'estado-' + (cita.estado_cita || 'pendiente').replace('_', '-');
            var estadoTexto = (cita.estado_cita || 'pendiente').charAt(0).toUpperCase() + (cita.estado_cita || 'pendiente').slice(1).replace('_', ' ');
            
            html += '<div class="cita-item" data-cita-id="' + cita.id + '">';
            html += '<div class="cita-header">';
            html += '<div class="cita-info">';
            html += '<h6 class="mb-1"><strong>' + (cita.paciente_nombre || '') + ' ' + (cita.paciente_apellido || '') + '</strong></h6>';
            html += '<p class="mb-0 text-muted">';
            html += '<i class="fas fa-calendar me-1"></i> ' + cita.fecha + ' ';
            html += '<i class="fas fa-clock me-1"></i> ' + cita.hora_inicio + ' - ' + cita.hora_fin;
            html += '</p>';
            html += '</div>';
            html += '<span class="cita-estado ' + estadoClass + '">' + estadoTexto + '</span>';
            html += '</div>';
            html += '<div class="mensaje-personalizado">';
            html += '<label class="form-label"><small>Mensaje personalizado adicional (opcional)</small></label>';
            html += '<textarea class="form-control mensaje-textarea" data-cita-id="' + cita.id + '" placeholder="Agregue un mensaje personalizado para este paciente..."></textarea>';
            html += '</div>';
            html += '</div>';
        });
    }
    
    $('#listaCitas').html(html);
    
    // Guardar mensajes personalizados cuando cambian
    $('.mensaje-textarea').on('input', function() {
        var citaId = $(this).data('cita-id');
        var mensaje = $(this).val();
        if (mensaje.trim()) {
            mensajesPersonalizados[citaId] = mensaje;
        } else {
            delete mensajesPersonalizados[citaId];
        }
    });
}

function procesarCancelacion() {
    var fechaInicio = $('#fecha_inicio').val();
    var fechaFin = $('#fecha_fin').val();
    var csrfToken = obtenerTokenCSRF();
    var csrfName = '<?= csrf_token() ?>';

    $('#btnConfirmarCancelacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Procesando...');

    $.ajax({
        url: '<?= base_url("dashboard/agenda/cancelar-horas/procesar") ?>',
        type: 'POST',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            mensajes_personalizados: JSON.stringify(mensajesPersonalizados),
            [csrfName]: csrfToken
        },
        success: function(response, textStatus, xhr) {
            // Actualizar token CSRF después de la respuesta exitosa
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Citas canceladas exitosamente');
                // Redirigir al calendario y forzar recarga
                setTimeout(function() {
                    window.location.href = '<?= base_url("dashboard/agenda/calendario") ?>?refresh=' + new Date().getTime();
                }, 1500);
            } else {
                toastr.error(response.error || 'Error al cancelar citas');
                $('#btnConfirmarCancelacion').prop('disabled', false).html('<i class="fas fa-exclamation-triangle me-2"></i> Confirmar Cancelación Masiva');
            }
            
            // Actualizar token CSRF desde la respuesta
            if (response.csrf_token) {
                $('#csrf_token_input').val(response.csrf_token);
            }
        },
        error: function(xhr) {
            // Actualizar token CSRF incluso en caso de error
            actualizarTokenCSRF(xhr);
            
            var error = 'Error al procesar cancelación';
            if (xhr.status === 403) {
                error = 'Error 403: Token CSRF inválido. Por favor, recarga la página e intenta de nuevo.';
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                error = xhr.responseJSON.error;
            }
            toastr.error(error);
            $('#btnConfirmarCancelacion').prop('disabled', false).html('<i class="fas fa-exclamation-triangle me-2"></i> Confirmar Cancelación Masiva');
        }
    });
}
</script>

<?= $this->endSection() ?>

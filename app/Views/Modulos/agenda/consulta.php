<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/consulta') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Flatpickr Date Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/k10uo8qhvhuxj1ho5z73jcbhzpwlspewyrz3lkbu5b99faon/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>

<style>
    .main-header {
        background: linear-gradient(135deg, #4A90E2 0%, #6BCB77 100%);
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        color: white;
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #4A90E2;
    }
    
    .timer-display {
        font-size: 3rem;
        font-weight: bold;
        color: #4A90E2;
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        margin: 20px 0;
    }
    
    .btn-action-large {
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 10px;
        transition: all 0.3s;
    }
    
    .btn-action-large:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9rem;
    }
    
    .status-iniciada {
        background: #FFA726;
        color: white;
    }
    
    .status-pendiente {
        background: #4A90E2;
        color: white;
    }
    
    .status-completada {
        background: #6BCB77;
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-md me-2"></i> Consulta en Curso</h2>
                        <p style="color: white;">Seguimiento y registro de la consulta con el paciente</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Calendario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Información del Paciente y Cita -->
            <div class="section-card">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary"><i class="fas fa-user me-2"></i> Información del Paciente</h5>
                        <hr>
                        <p><strong>Nombre:</strong> <?= esc($cita->nombre . ' ' . $cita->apellido) ?></p>
                        <?php if ($cita->rut_dni): ?>
                        <p><strong>RUT/DNI:</strong> <?= esc($cita->rut_dni) ?></p>
                        <?php endif; ?>
                        <?php if ($cita->telefono): ?>
                        <p><strong>Teléfono:</strong> <a href="tel:<?= esc($cita->telefono) ?>"><?= esc($cita->telefono) ?></a></p>
                        <?php endif; ?>
                        <?php if ($cita->email): ?>
                        <p><strong>Email:</strong> <a href="mailto:<?= esc($cita->email) ?>"><?= esc($cita->email) ?></a></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-info"><i class="fas fa-calendar-alt me-2"></i> Información de la Cita</h5>
                        <hr>
                        <p><strong>Fecha:</strong> <?= esc($fecha) ?></p>
                        <p><strong>Hora Programada:</strong> <?= date('H:i', strtotime($cita->hora_inicio)) ?> - <?= date('H:i', strtotime($cita->hora_fin)) ?></p>
                        <p><strong>Modalidad:</strong> <?= esc($cita->modalidad_nombre ?? 'No definida') ?></p>
                        <p><strong>Tipo de Consulta:</strong> <?= ucfirst(str_replace('_', ' ', $cita->tipo_consulta ?? 'control')) ?></p>
                        <?php if ($cita->motivo): ?>
                        <p><strong>Motivo:</strong> <?= esc($cita->motivo) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Notas y Recordatorios del Nutricionista (Destacado) -->
            <?php if (!empty($cita->notas_nutricionista)): ?>
            <div class="section-card" style="border-left: 4px solid #FFA726 !important; background: linear-gradient(135deg, #FFF8E1 0%, #FFFFFF 100%);">
                <div class="d-flex align-items-start">
                    <div class="me-3">
                        <i class="fas fa-sticky-note fa-2x text-warning"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="text-warning mb-3">
                            <i class="fas fa-lightbulb me-2"></i> Notas y Recordatorios
                        </h5>
                        <div class="alert alert-warning mb-0" style="background-color: rgba(255, 167, 38, 0.1); border: 1px solid rgba(255, 167, 38, 0.3);">
                            <div style="white-space: pre-wrap; font-size: 1.05em; line-height: 1.6;"><?= esc($cita->notas_nutricionista) ?></div>
                        </div>
                        <p class="text-muted small mt-2 mb-0">
                            <i class="fas fa-info-circle me-1"></i> Estas notas fueron agregadas durante la planificación de la cita
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Control de Consulta -->
            <div class="section-card">
                <div class="text-center">
                    <h5 class="mb-4"><i class="fas fa-clock me-2"></i> Control de Consulta</h5>
                    
                    <!-- Estado de la consulta -->
                    <div class="mb-4">
                        <?php 
                        $estadoConsulta = 'pendiente';
                        if ($cita->fecha_inicio_real && !$cita->fecha_fin_real) {
                            $estadoConsulta = 'iniciada';
                        } elseif ($cita->fecha_fin_real) {
                            $estadoConsulta = 'completada';
                        }
                        ?>
                        <span class="status-badge status-<?= $estadoConsulta ?>">
                            <?php if ($estadoConsulta === 'pendiente'): ?>
                                <i class="fas fa-hourglass-half me-2"></i> Pendiente de Iniciar
                            <?php elseif ($estadoConsulta === 'iniciada'): ?>
                                <i class="fas fa-play-circle me-2"></i> Consulta en Curso
                            <?php else: ?>
                                <i class="fas fa-check-circle me-2"></i> Consulta Completada
                            <?php endif; ?>
                        </span>
                    </div>

                    <!-- Timer (si está iniciada) -->
                    <?php if ($estadoConsulta === 'iniciada'): ?>
                    <div class="timer-display" id="timerDisplay">
                        00:00:00
                    </div>
                    <?php endif; ?>

                    <!-- Botones de Control -->
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <?php if ($estadoConsulta === 'pendiente'): ?>
                            <button class="btn btn-success btn-action-large" onclick="iniciarConsulta(<?= $cita->id ?>)">
                                <i class="fas fa-play-circle me-2"></i> Iniciar Consulta
                            </button>
                        <?php elseif ($estadoConsulta === 'iniciada'): ?>
                            <button class="btn btn-danger btn-action-large" onclick="terminarConsulta(<?= $cita->id ?>)">
                                <i class="fas fa-stop-circle me-2"></i> Terminar Consulta
                            </button>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Consulta Finalizada</strong><br>
                                <?php if ($cita->fecha_inicio_real && $cita->fecha_fin_real): ?>
                                    Duración: <?= $cita->duracion_real ?? 'N/A' ?> minutos
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Formulario de Notas y Seguimiento -->
            <form id="formConsulta">
                <input type="hidden" name="detalle_agenda_id" value="<?= $cita->id ?>">
                
                <div class="section-card">
                    <h5 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i> Notas de la Consulta</h5>
                    <p class="text-muted small">Anota todo lo que se habla durante la consulta. Puedes usar formato de texto (negrita, cursiva, subrayado, listas, etc.). Puedes guardar en cualquier momento.</p>
                    <textarea 
                        name="notas_consulta" 
                        id="notas_consulta" 
                        class="form-control" 
                        rows="8" 
                        placeholder="Ej: Paciente refiere mejoría en síntomas digestivos. Se ajustó plan alimentario eliminando lácteos. Se solicitó análisis de sangre para próxima visita..."
                    ><?= $cita->notas_consulta ?? '' ?></textarea>
                    <div class="mt-2 text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="guardarNotasConsulta()">
                            <i class="fas fa-save me-2"></i> Guardar Notas
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="section-card">
                            <h5 class="text-success mb-3"><i class="fas fa-bullseye me-2"></i> Objetivos Establecidos</h5>
                            <p class="text-muted small mb-2">Define los objetivos que se establecieron durante la consulta. Puedes usar formato de texto.</p>
                            <textarea 
                                name="objetivos" 
                                id="objetivos" 
                                class="form-control" 
                                rows="5" 
                                placeholder="Ej: Reducir 5kg en 3 meses, Mejorar niveles de colesterol, Implementar rutina de ejercicios 3 veces por semana..."
                            ><?= $cita->objetivos ?? '' ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-card">
                            <h5 class="text-info mb-3"><i class="fas fa-utensils me-2"></i> Plan de Alimentación</h5>
                            <p class="text-muted small mb-2">Describe el plan de alimentación acordado. Puedes usar formato de texto.</p>
                            <textarea 
                                name="plan_alimentacion" 
                                id="plan_alimentacion" 
                                class="form-control" 
                                rows="5" 
                                placeholder="Ej: Dieta mediterránea, 5 comidas al día, Eliminar azúcares refinados, Aumentar consumo de vegetales..."
                            ><?= $cita->plan_alimentacion ?? '' ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <h5 class="text-warning mb-3"><i class="fas fa-lightbulb me-2"></i> Recomendaciones</h5>
                    <p class="text-muted small mb-2">Agrega recomendaciones adicionales para el paciente. Puedes usar formato de texto.</p>
                    <textarea 
                        name="recomendaciones" 
                        id="recomendaciones" 
                        class="form-control" 
                        rows="4" 
                        placeholder="Ej: Realizar ejercicio cardiovascular 30 min 3 veces por semana, Tomar suplemento de vitamina D, Agendar próxima cita en 1 mes..."
                    ><?= $cita->recomendaciones ?? '' ?></textarea>
                </div>

                <div class="section-card">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-secondary mb-3"><i class="fas fa-calendar-check me-2"></i> Próxima Cita Recomendada</h5>
                            <div class="alert alert-info d-flex align-items-start mb-3" role="alert">
                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                <div>
                                    <strong>Recomendación:</strong> Indica la fecha sugerida para la próxima consulta de seguimiento. Esta fecha es una recomendación y puede ser modificada al agendar la próxima cita.
                                </div>
                            </div>
                            <input 
                                type="text" 
                                name="proxima_cita_recomendada" 
                                id="proxima_cita_recomendada" 
                                class="form-control" 
                                placeholder="DD-MM-YYYY"
                                value="<?= $cita->proxima_cita_recomendada ? date('d-m-Y', strtotime($cita->proxima_cita_recomendada)) : '' ?>"
                                readonly
                            >
                            <small class="form-text text-muted">Haz clic en el campo para seleccionar la fecha</small>
                        </div>
                    </div>
                </div>

                <div class="section-card">
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="guardarTodo()">
                                <i class="fas fa-save me-2"></i> Guardar Todo
                            </button>
                            <button type="button" class="btn btn-primary" onclick="guardarTodoYFinalizar()">
                                <i class="fas fa-check-double me-2"></i> Guardar y Finalizar Consulta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Terminar Consulta -->
<div class="modal fade" id="modalTerminarConsulta" tabindex="-1" aria-labelledby="modalTerminarConsultaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #FFA726; border-bottom: none;">
                <h5 class="modal-title" id="modalTerminarConsultaLabel">
                    <i class="fas fa-check-circle me-2"></i> Confirmar Terminación de Consulta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="text-center mb-4">
                    <div style="width: 80px; height: 80px; margin: 0 auto; background-color: #90A4AE; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check" style="font-size: 2.5rem; color: white;"></i>
                    </div>
                </div>
                <p class="text-center mb-2" style="font-size: 1.1rem; color: #333;">
                    <strong>¿Está seguro de que desea terminar la consulta?</strong>
                </p>
                <p class="text-muted text-center" style="font-size: 0.95rem;">
                    Esta acción no se puede deshacer. Se calculará la duración total de la consulta y se marcará como <span style="color: #90A4AE; font-weight: 600;">completada</span>.
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 1rem 1.5rem;">
                <button type="button" class="btn" style="background-color: #BDBDBD; color: white; border: none;" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="button" class="btn text-white" style="background-color: #90A4AE; border: none;" id="btnConfirmarTerminar">
                    <i class="fas fa-check-circle me-2"></i> Sí, Terminar Consulta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Configurar toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right"
};

var consultaIniciada = <?= $cita->fecha_inicio_real ? 'true' : 'false' ?>;
var fechaInicio = <?= $cita->fecha_inicio_real ? "'" . date('Y-m-d H:i:s', strtotime($cita->fecha_inicio_real)) . "'" : 'null' ?>;
var timerInterval = null;

// Función para obtener token CSRF
function obtenerTokenCSRF() {
    var name = 'csrf_cookie_name';
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf(name + '=') === 0) {
            return cookie.substring(name.length + 1);
        }
    }
    return null;
}

// Función para actualizar token CSRF después de peticiones AJAX
function actualizarTokenCSRF(xhr) {
    var newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
    if (newToken) {
        $('meta[name="csrf-token"]').attr('content', newToken);
        $('input[name="csrf_test_name"]').val(newToken);
    } else if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
        $('meta[name="csrf-token"]').attr('content', xhr.responseJSON.csrf_token);
        $('input[name="csrf_test_name"]').val(xhr.responseJSON.csrf_token);
    }
}

// Timer para consulta en curso
function iniciarTimer() {
    if (!fechaInicio) return;
    
    var inicio = new Date(fechaInicio.replace(' ', 'T'));
    
    timerInterval = setInterval(function() {
        var ahora = new Date();
        var diff = ahora - inicio;
        
        var horas = Math.floor(diff / (1000 * 60 * 60));
        var minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var segundos = Math.floor((diff % (1000 * 60)) / 1000);
        
        var tiempo = String(horas).padStart(2, '0') + ':' + 
                     String(minutos).padStart(2, '0') + ':' + 
                     String(segundos).padStart(2, '0');
        
        $('#timerDisplay').text(tiempo);
    }, 1000);
}

// Iniciar consulta
function iniciarConsulta(detalleAgendaId) {
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/iniciarConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            detalle_agenda_id: detalleAgendaId,
            [csrfName]: csrfToken
        },
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Consulta iniciada', 'Éxito');
                consultaIniciada = true;
                fechaInicio = response.fecha_inicio;
                location.reload(); // Recargar para mostrar timer
            } else {
                toastr.error(response.error || 'Error al iniciar la consulta', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var errorMsg = 'Error al iniciar la consulta';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Variable global para almacenar el ID de la consulta a terminar
var detalleAgendaIdParaTerminar = null;

// Terminar consulta - Abre el modal de confirmación
function terminarConsulta(detalleAgendaId) {
    detalleAgendaIdParaTerminar = detalleAgendaId;
    var modal = new bootstrap.Modal(document.getElementById('modalTerminarConsulta'));
    modal.show();
}

// Confirmar terminación de consulta (se ejecuta desde el modal)
function confirmarTerminarConsulta() {
    if (!detalleAgendaIdParaTerminar) {
        return;
    }
    
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Deshabilitar botón mientras se procesa
    var $btnConfirmar = $('#btnConfirmarTerminar');
    var textoOriginal = $btnConfirmar.html();
    $btnConfirmar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Procesando...');
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/terminarConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            detalle_agenda_id: detalleAgendaIdParaTerminar,
            [csrfName]: csrfToken
        },
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                // Cerrar modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalTerminarConsulta'));
                modal.hide();
                
                toastr.success('Consulta finalizada. Duración: ' + response.duracion_minutos + ' minutos', 'Éxito', {
                    timeOut: 3000
                });
                
                if (timerInterval) {
                    clearInterval(timerInterval);
                }
                
                // Recargar después de un breve delay
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                $btnConfirmar.prop('disabled', false).html(textoOriginal);
                toastr.error(response.error || 'Error al terminar la consulta', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            $btnConfirmar.prop('disabled', false).html(textoOriginal);
            
            var errorMsg = 'Error al terminar la consulta';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Guardar notas de consulta
function guardarNotasConsulta() {
    var csrfToken = obtenerTokenCSRF() || $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Obtener el contenido HTML del editor TinyMCE
    var notasHTML = '';
    if (tinymce.get('notas_consulta')) {
        notasHTML = tinymce.get('notas_consulta').getContent();
    } else {
        notasHTML = $('#notas_consulta').val();
    }
    
    // Obtener el contenido HTML de todos los editores TinyMCE
    var objetivosHTML = '';
    var planHTML = '';
    var recomendacionesHTML = '';
    
    if (tinymce.get('objetivos')) {
        objetivosHTML = tinymce.get('objetivos').getContent();
    } else {
        objetivosHTML = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        planHTML = tinymce.get('plan_alimentacion').getContent();
    } else {
        planHTML = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        recomendacionesHTML = tinymce.get('recomendaciones').getContent();
    } else {
        recomendacionesHTML = $('#recomendaciones').val();
    }
    
    var formData = {
        detalle_agenda_id: $('input[name="detalle_agenda_id"]').val(),
        notas_consulta: notasHTML,
        objetivos: objetivosHTML,
        plan_alimentacion: planHTML,
        recomendaciones: recomendacionesHTML,
        proxima_cita_recomendada: $('#proxima_cita_recomendada').val(),
        [csrfName]: csrfToken
    };
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/guardarNotasConsulta') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: formData,
        dataType: 'json',
        success: function(response, textStatus, xhr) {
            actualizarTokenCSRF(xhr);
            
            if (response.success) {
                toastr.success(response.message || 'Información guardada correctamente', 'Éxito', {
                    timeOut: 2000
                });
            } else {
                toastr.error(response.error || 'Error al guardar', 'Error');
            }
        },
        error: function(xhr) {
            actualizarTokenCSRF(xhr);
            var errorMsg = 'Error al guardar la información';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            toastr.error(errorMsg, 'Error');
        }
    });
}

// Guardar todo
function guardarTodo() {
    guardarNotasConsulta();
}

// Guardar y finalizar
function guardarTodoYFinalizar() {
    guardarNotasConsulta();
    setTimeout(function() {
        if (consultaIniciada && !<?= $cita->fecha_fin_real ? 'true' : 'false' ?>) {
            terminarConsulta(<?= $cita->id ?>);
        } else {
            toastr.info('La consulta ya está finalizada o no ha sido iniciada', 'Información');
        }
    }, 500);
}

// Auto-guardar cada 2 minutos si hay cambios
var ultimoContenido = {
    notas: '',
    objetivos: $('#objetivos').val(),
    plan: $('#plan_alimentacion').val(),
    recomendaciones: $('#recomendaciones').val()
};

// Inicializar contenido de todos los editores después de que TinyMCE esté listo
setTimeout(function() {
    if (tinymce.get('notas_consulta')) {
        ultimoContenido.notas = tinymce.get('notas_consulta').getContent();
    } else {
        ultimoContenido.notas = $('#notas_consulta').val();
    }
    
    if (tinymce.get('objetivos')) {
        ultimoContenido.objetivos = tinymce.get('objetivos').getContent();
    } else {
        ultimoContenido.objetivos = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        ultimoContenido.plan = tinymce.get('plan_alimentacion').getContent();
    } else {
        ultimoContenido.plan = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        ultimoContenido.recomendaciones = tinymce.get('recomendaciones').getContent();
    } else {
        ultimoContenido.recomendaciones = $('#recomendaciones').val();
    }
}, 1500);

setInterval(function() {
    var notasActuales = '';
    var objetivosActuales = '';
    var planActual = '';
    var recomendacionesActuales = '';
    
    if (tinymce.get('notas_consulta')) {
        notasActuales = tinymce.get('notas_consulta').getContent();
    } else {
        notasActuales = $('#notas_consulta').val();
    }
    
    if (tinymce.get('objetivos')) {
        objetivosActuales = tinymce.get('objetivos').getContent();
    } else {
        objetivosActuales = $('#objetivos').val();
    }
    
    if (tinymce.get('plan_alimentacion')) {
        planActual = tinymce.get('plan_alimentacion').getContent();
    } else {
        planActual = $('#plan_alimentacion').val();
    }
    
    if (tinymce.get('recomendaciones')) {
        recomendacionesActuales = tinymce.get('recomendaciones').getContent();
    } else {
        recomendacionesActuales = $('#recomendaciones').val();
    }
    
    var hayCambios = 
        notasActuales !== ultimoContenido.notas ||
        objetivosActuales !== ultimoContenido.objetivos ||
        planActual !== ultimoContenido.plan ||
        recomendacionesActuales !== ultimoContenido.recomendaciones;
    
    if (hayCambios && consultaIniciada) {
        guardarNotasConsulta();
        ultimoContenido = {
            notas: notasActuales,
            objetivos: objetivosActuales,
            plan: planActual,
            recomendaciones: recomendacionesActuales
        };
    }
}, 120000); // 2 minutos

// Iniciar timer si la consulta ya está iniciada
$(document).ready(function() {
    if (consultaIniciada && fechaInicio) {
        iniciarTimer();
    }
    
    // Configuración común de TinyMCE
    var tinymceConfig = {
        height: 300,
        menubar: false,
        plugins: [
            // Core editing features
            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
            // Premium features (trial hasta Jan 24, 2026)
            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate', 'ai', 'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword', 'exportpdf'
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
        language: 'es',
        branding: false,
        promotion: false,
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Nutricionista',
        mergetags_list: [
            { value: 'Paciente.Nombre', title: 'Nombre del Paciente' },
            { value: 'Paciente.Email', title: 'Email del Paciente' },
            { value: 'Fecha.Consulta', title: 'Fecha de Consulta' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
        uploadcare_public_key: '4aa5df577992e10cc5b1',
        setup: function(editor) {
            // Auto-guardar cuando se hace un cambio (después de 2 segundos de inactividad)
            var timeout;
            editor.on('keyup', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    if (consultaIniciada) {
                        guardarNotasConsulta();
                    }
                }, 2000); // 2 segundos después de dejar de escribir
            });
        }
    };

    // Inicializar TinyMCE para el editor de notas (más alto)
    var notasConfig = Object.assign({}, tinymceConfig, {
        selector: '#notas_consulta',
        height: 400
    });
    tinymce.init(notasConfig);

    // Inicializar TinyMCE para Objetivos
    var objetivosConfig = Object.assign({}, tinymceConfig, {
        selector: '#objetivos',
        height: 250
    });
    tinymce.init(objetivosConfig);

    // Inicializar TinyMCE para Plan de Alimentación
    var planConfig = Object.assign({}, tinymceConfig, {
        selector: '#plan_alimentacion',
        height: 250
    });
    tinymce.init(planConfig);

    // Inicializar TinyMCE para Recomendaciones
    var recomendacionesConfig = Object.assign({}, tinymceConfig, {
        selector: '#recomendaciones',
        height: 200
    });
    tinymce.init(recomendacionesConfig);
    
    // Inicializar Flatpickr para el selector de fecha
    var fechaProximaCita = <?= $cita->proxima_cita_recomendada ? "'" . date('d-m-Y', strtotime($cita->proxima_cita_recomendada)) . "'" : 'null' ?>;
    
    flatpickr("#proxima_cita_recomendada", {
        dateFormat: "d-m-Y",
        locale: "es",
        minDate: "today",
        allowInput: false,
        clickOpens: true,
        defaultDate: fechaProximaCita,
        onChange: function(selectedDates, dateStr, instance) {
            // Opcional: auto-guardar cuando se cambia la fecha
            if (consultaIniciada && dateStr) {
                setTimeout(function() {
                    guardarNotasConsulta();
                }, 500);
            }
        }
    });
    
    // Event listener para el botón de confirmar terminar consulta
    $('#btnConfirmarTerminar').on('click', function() {
        confirmarTerminarConsulta();
    });
});
</script>

<?= $this->endSection() ?>

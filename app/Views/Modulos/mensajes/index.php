<?= $this->extend('layout/dashboard') ?>

<?= $this->section('mensajes/index') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .mensajes-layout { display: flex; gap: 0; min-height: 520px; border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; background: #fff; }
    .mensajes-lista { width: 320px; max-width: 38%; border-right: 1px solid #e9ecef; overflow-y: auto; }
    .mensajes-hilo { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .mensajes-lista .item-conv { cursor: pointer; padding: 12px 14px; border-bottom: 1px solid #f0f0f0; transition: background .15s; }
    .mensajes-lista .item-conv:hover, .mensajes-lista .item-conv.active { background: #f0f4ff; }
    .mensajes-lista .item-conv .nombre { font-weight: 600; color: #2c3e50; }
    .mensajes-lista .item-conv .preview { font-size: .85rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mensajes-hilo-header { padding: 14px 18px; border-bottom: 1px solid #e9ecef; background: #fafbff; }
    .mensajes-hilo-body { flex: 1; overflow-y: auto; padding: 16px; background: #f8f9fa; }
    .burbuja { max-width: 78%; margin-bottom: 10px; padding: 10px 14px; border-radius: 12px; font-size: .95rem; line-height: 1.4; word-break: break-word; }
    .burbuja.enviado { margin-left: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-bottom-right-radius: 4px; }
    .burbuja.recibido { margin-right: auto; background: #fff; border: 1px solid #dee2e6; border-bottom-left-radius: 4px; }
    .burbuja .meta { font-size: .75rem; opacity: .85; margin-top: 4px; }
    .mensajes-hilo-footer { padding: 12px 16px; border-top: 1px solid #e9ecef; background: #fff; }
    .empty-hilo { color: #6c757d; text-align: center; padding: 48px 24px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div>
                    <h2 style="color: white;"><i class="fab fa-whatsapp me-2"></i> Mensajes WhatsApp</h2>
                    <p style="color: white;" class="mb-0">Historial con sus pacientes y respuestas (ventana 24 h para mensaje libre)</p>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #25D366; padding: 0;">
                <div class="mensajes-layout">
                    <div class="mensajes-lista" id="listaConversaciones">
                        <?php if (empty($conversaciones)) : ?>
                            <div class="p-4 text-muted text-center small">
                                Aún no hay mensajes registrados.<br>
                                Los envíos (recordatorios, confirmaciones) y las respuestas de pacientes aparecerán aquí cuando el webhook esté activo.
                            </div>
                        <?php else : ?>
                            <?php foreach ($conversaciones as $c) : ?>
                                <div class="item-conv" data-paciente-id="<?= (int) $c['paciente_id'] ?>">
                                    <div class="nombre"><?= esc($c['nombre_completo']) ?></div>
                                    <div class="preview"><?= esc($c['ultimo_mensaje'] ?? '') ?></div>
                                    <small class="text-muted"><?= $c['ultima_fecha'] ? date('d-m-Y H:i', strtotime($c['ultima_fecha'])) : '' ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="mensajes-hilo">
                        <div class="mensajes-hilo-header" id="hiloHeader">
                            <span class="text-muted">Seleccione un paciente</span>
                        </div>
                        <div class="mensajes-hilo-body" id="hiloBody">
                            <div class="empty-hilo">
                                <i class="fab fa-whatsapp fa-3x mb-3" style="color:#25D366;"></i>
                                <p>Elija una conversación para ver el historial.</p>
                            </div>
                        </div>
                        <div class="mensajes-hilo-footer" id="hiloFooter" style="display:none;">
                            <div id="avisoVentana" class="alert alert-warning py-2 small mb-2" style="display:none;"></div>
                            <form id="formEnviarMensaje">
                                <div class="input-group">
                                    <textarea class="form-control" id="textoMensaje" rows="2" placeholder="Escriba su mensaje..." maxlength="4096"></textarea>
                                    <button type="submit" class="btn btn-success" id="btnEnviarMensaje">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var pacienteActivoId = null;

function obtenerTokenCSRF() {
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf('csrf_cookie_name=') !== -1) {
            var parts = cookie.split('=');
            if (parts.length >= 2) return decodeURIComponent(parts.slice(1).join('='));
        }
    }
    return $('meta[name="csrf-token"]').attr('content') || $('input[name="csrf_test_name"]').val() || '';
}

function escapeHtml(t) {
    if (t == null) return '';
    return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function formatearFecha(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f;
    return d.toLocaleString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function cargarHilo(pacienteId) {
    pacienteActivoId = pacienteId;
    $('#hiloBody').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#hiloFooter').hide();

    $.get('<?= base_url('dashboard/mensajes/hilo') ?>/' + pacienteId, function(res) {
        if (!res.success) {
            toastr.error(res.message || 'Error al cargar');
            return;
        }
        $('#hiloHeader').html(
            '<strong>' + escapeHtml(res.paciente.nombre) + '</strong>' +
            (res.paciente.telefono ? ' <small class="text-muted">· ' + escapeHtml(res.paciente.telefono) + '</small>' : '')
        );
        var html = '';
        if (!res.mensajes.length) {
            html = '<div class="empty-hilo">Sin mensajes para este paciente.</div>';
        } else {
            res.mensajes.forEach(function(m) {
                var cls = m.direccion === 'enviado' ? 'enviado' : 'recibido';
                var estado = m.direccion === 'enviado' && m.estado_envio ? ' · ' + m.estado_envio : '';
                html += '<div class="burbuja ' + cls + '">' + escapeHtml(m.mensaje) +
                    '<div class="meta">' + formatearFecha(m.fecha) + estado + '</div></div>';
            });
        }
        $('#hiloBody').html(html);
        var el = document.getElementById('hiloBody');
        el.scrollTop = el.scrollHeight;
        $('#hiloFooter').show();
        if (res.puede_responder_libre) {
            $('#avisoVentana').hide();
            $('#textoMensaje, #btnEnviarMensaje').prop('disabled', false);
        } else {
            $('#avisoVentana').text('Sin ventana de 24 h: el paciente debe haber escrito recientemente para responder con texto libre. Use plantillas desde Agenda si hace falta.').show();
            $('#textoMensaje, #btnEnviarMensaje').prop('disabled', true);
        }
    }).fail(function() {
        toastr.error('No se pudo cargar la conversación');
    });
}

$(document).on('click', '.item-conv', function() {
    $('.item-conv').removeClass('active');
    $(this).addClass('active');
    cargarHilo($(this).data('paciente-id'));
});

$('#formEnviarMensaje').on('submit', function(e) {
    e.preventDefault();
    if (!pacienteActivoId) return;
    var texto = $('#textoMensaje').val().trim();
    if (!texto) return;
    var csrf = obtenerTokenCSRF();
    $('#btnEnviarMensaje').prop('disabled', true);
    $.ajax({
        url: '<?= base_url('dashboard/mensajes/enviar') ?>',
        type: 'POST',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': csrf },
        data: { csrf_test_name: csrf, paciente_id: pacienteActivoId, mensaje: texto },
        success: function(res) {
            $('#btnEnviarMensaje').prop('disabled', false);
            if (res.success) {
                $('#textoMensaje').val('');
                toastr.success(res.message);
                cargarHilo(pacienteActivoId);
            } else {
                toastr.error(res.message || 'Error');
            }
        },
        error: function(xhr) {
            $('#btnEnviarMensaje').prop('disabled', false);
            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al enviar';
            toastr.error(msg);
        }
    });
});

$(function() {
    var inicial = <?= (int) ($paciente_id_inicial ?? 0) ?>;
    if (inicial) {
        $('.item-conv[data-paciente-id="' + inicial + '"]').addClass('active').click();
    }
});
</script>

<?= $this->endSection() ?>

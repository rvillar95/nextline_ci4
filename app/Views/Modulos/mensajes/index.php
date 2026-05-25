<?= $this->extend('layout/dashboard') ?>

<?= $this->section('titulo') ?>Mensajes<?= $this->endSection() ?>

<?= $this->section('mensajes/index') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .mensajes-layout { display: flex; gap: 0; min-height: 520px; border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; background: #fff; }
    .mensajes-lista { width: 320px; max-width: 38%; border-right: 1px solid #e9ecef; display: flex; flex-direction: column; min-height: 0; }
    .mensajes-buscar { padding: 10px 12px; border-bottom: 1px solid #e9ecef; background: #fafbff; flex-shrink: 0; }
    .mensajes-buscar input { font-size: .9rem; }
    .mensajes-lista-scroll { flex: 1; overflow-y: auto; min-height: 0; }
    .mensajes-hilo { flex: 1; display: flex; flex-direction: column; min-width: 0; }
    .mensajes-lista .item-conv { cursor: pointer; padding: 12px 14px; border-bottom: 1px solid #f0f0f0; transition: background .15s; }
    .mensajes-lista .item-conv:hover, .mensajes-lista .item-conv.active { background: #f0f4ff; }
    .mensajes-lista .item-conv.no-leido { background: #f8fffe; }
    .mensajes-lista .item-conv.no-leido .nombre { font-weight: 700; }
    .mensajes-lista .item-conv .conv-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
    .mensajes-lista .item-conv .badge-no-leidos {
        background: #25D366; color: #fff; font-size: .7rem; font-weight: 700;
        min-width: 20px; height: 20px; line-height: 20px; text-align: center;
        border-radius: 10px; padding: 0 6px; flex-shrink: 0;
    }
    .mensajes-lista .item-conv .nombre { font-weight: 600; color: #2c3e50; line-height: 1.25; flex: 1; min-width: 0; }
    .mensajes-lista .item-conv .telefono-conv { font-size: .8rem; color: #25D366; margin-top: 2px; }
    .mensajes-lista .item-conv .preview { font-size: .85rem; color: #6c757d; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 4px; }
    .mensajes-hilo-header { padding: 14px 18px; border-bottom: 1px solid #e9ecef; background: #fafbff; }
    .hilo-contacto-nombre { font-size: 1.05rem; font-weight: 600; color: #2c3e50; }
    .hilo-contacto-tel { font-size: .875rem; color: #25D366; margin-top: 4px; }
    .hilo-contacto-tel i { font-size: .75rem; margin-right: 4px; opacity: .85; }
    .mensajes-hilo-body { flex: 1; overflow-y: auto; padding: 16px; background: #f8f9fa; }
    .burbuja { max-width: 78%; margin-bottom: 10px; padding: 10px 14px; border-radius: 12px; font-size: .95rem; line-height: 1.4; word-break: break-word; }
    .burbuja.enviado { margin-left: auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border-bottom-right-radius: 4px; }
    .burbuja.recibido { margin-right: auto; background: #fff; border: 1px solid #dee2e6; border-bottom-left-radius: 4px; }
    .burbuja .meta { font-size: .75rem; opacity: .85; margin-top: 4px; }
    .mensajes-hilo-footer { padding: 12px 16px; border-top: 1px solid #e9ecef; background: #fff; }
    .empty-hilo { color: #6c757d; text-align: center; padding: 48px 24px; }
    #hiloLoaderAnteriores { text-align: center; padding: 8px; font-size: .85rem; color: #6c757d; }
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
                    <div class="mensajes-lista">
                        <div class="mensajes-buscar">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                <input type="search" class="form-control" id="buscarConversaciones"
                                    placeholder="Buscar nombre, teléfono o RUT…" autocomplete="off">
                            </div>
                        </div>
                        <div class="mensajes-lista-scroll" id="listaConversaciones">
                        <?php if (empty($conversaciones)) : ?>
                            <div class="p-4 text-muted text-center small" id="listaConvVacia">
                                Aún no hay mensajes registrados.<br>
                                Los envíos (recordatorios, confirmaciones) y las respuestas de pacientes aparecerán aquí cuando el webhook esté activo.
                            </div>
                        <?php else : ?>
                            <div class="p-3 text-muted text-center small d-none" id="sinResultadosBuscar">
                                No hay conversaciones que coincidan con la búsqueda.
                            </div>
                            <?php foreach ($conversaciones as $c) :
                                $tel = trim((string) ($c['telefono'] ?? ''));
                                $buscarTexto = mb_strtolower(
                                    ($c['nombre_completo'] ?? '') . ' '
                                    . $tel . ' '
                                    . ($c['rut_dni'] ?? '') . ' '
                                    . ($c['ultimo_mensaje'] ?? '')
                                );
                                if ($tel !== '') {
                                    $buscarTexto .= ' ' . preg_replace('/\D+/', '', $tel);
                                }
                                ?>
                                <div class="item-conv<?= ((int) ($c['no_leidos'] ?? 0)) > 0 ? ' no-leido' : '' ?>"
                                    data-paciente-id="<?= (int) $c['paciente_id'] ?>"
                                    data-no-leidos="<?= (int) ($c['no_leidos'] ?? 0) ?>"
                                    data-ultima-fecha="<?= esc($c['ultima_fecha'] ?? '', 'attr') ?>"
                                    data-buscar="<?= esc($buscarTexto, 'attr') ?>">
                                    <div class="conv-top">
                                        <div class="nombre"><?= esc($c['nombre_completo']) ?></div>
                                        <?php if ((int) ($c['no_leidos'] ?? 0) > 0) : ?>
                                            <span class="badge-no-leidos"><?= (int) $c['no_leidos'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($tel !== '') : ?>
                                        <div class="telefono-conv"><i class="fab fa-whatsapp"></i> <?= esc($tel) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($c['ultimo_mensaje'])) : ?>
                                        <div class="preview"><?= esc($c['ultimo_mensaje']) ?></div>
                                    <?php endif; ?>
                                    <small class="text-muted"><?= $c['ultima_fecha'] ? date('d-m-Y H:i', strtotime($c['ultima_fecha'])) : '' ?></small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </div>
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
                                    <small class="text-muted d-block mt-1">Se enviará con su nombre al inicio (ej. Nutricionista: su texto).</small>
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
var hiloOldestId = null;
var hiloUltimoId = 0;
var hiloHasMoreOlder = false;
var hiloCargandoAnteriores = false;
var syncTimer = null;
var SYNC_INTERVAL_MS = 5000;
var MENSAJES_POR_LOTE = <?= (int) \App\Models\WhatsAppMensaje::MENSAJES_POR_LOTE ?>;
var URL_SYNC = '<?= base_url('dashboard/mensajes/sync') ?>';

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

function normalizarBusqueda(q) {
    return String(q || '').toLowerCase().trim();
}

function htmlHeaderContacto(paciente) {
    var html = '<div class="hilo-contacto">';
    html += '<div class="hilo-contacto-nombre">' + escapeHtml(paciente.nombre) + '</div>';
    if (paciente.telefono) {
        html += '<div class="hilo-contacto-tel"><i class="fab fa-whatsapp"></i> ' + escapeHtml(paciente.telefono) + '</div>';
    }
    html += '</div>';
    return html;
}

function maxIdEnHilo() {
    var max = 0;
    $('#hiloBody .burbuja[data-msg-id]').each(function() {
        var id = parseInt($(this).attr('data-msg-id'), 10);
        if (id > max) max = id;
    });
    return max;
}

function htmlItemConversacion(c) {
    var tel = (c.telefono || '').trim();
    var buscar = (c.nombre_completo || '') + ' ' + tel + ' ' + (c.rut_dni || '') + ' ' + (c.ultimo_mensaje || '');
    if (tel) buscar += ' ' + tel.replace(/\D/g, '');
    var noLeidos = parseInt(c.no_leidos, 10) || 0;
    var cls = 'item-conv' + (pacienteActivoId == c.paciente_id ? ' active' : '') + (noLeidos > 0 ? ' no-leido' : '');
    var fechaTxt = '';
    if (c.ultima_fecha) {
        var d = new Date(String(c.ultima_fecha).replace(' ', 'T'));
        if (!isNaN(d.getTime())) {
            fechaTxt = d.toLocaleString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }
    var html = '<div class="' + cls + '" data-paciente-id="' + c.paciente_id + '" data-no-leidos="' + noLeidos + '"';
    html += ' data-ultima-fecha="' + escapeHtml(c.ultima_fecha || '') + '" data-buscar="' + escapeHtml(buscar.toLowerCase()) + '">';
    html += '<div class="conv-top"><div class="nombre">' + escapeHtml(c.nombre_completo) + '</div>';
    if (noLeidos > 0) html += '<span class="badge-no-leidos">' + noLeidos + '</span>';
    html += '</div>';
    if (tel) html += '<div class="telefono-conv"><i class="fab fa-whatsapp"></i> ' + escapeHtml(tel) + '</div>';
    if (c.ultimo_mensaje) html += '<div class="preview">' + escapeHtml(c.ultimo_mensaje) + '</div>';
    html += '<small class="text-muted">' + fechaTxt + '</small></div>';
    return html;
}

function renderListaConversaciones(conversaciones) {
    if (!conversaciones || !conversaciones.length) {
        $('#listaConversaciones').html('<div class="p-4 text-muted text-center small" id="listaConvVacia">Aún no hay mensajes registrados.</div>');
        return;
    }
    var html = '<div class="p-3 text-muted text-center small d-none" id="sinResultadosBuscar">No hay conversaciones que coincidan con la búsqueda.</div>';
    conversaciones.forEach(function(c) { html += htmlItemConversacion(c); });
    $('#listaConversaciones').html(html);
    filtrarConversaciones();
}

function filtrarConversaciones() {
    var q = normalizarBusqueda($('#buscarConversaciones').val());
    var qDigitos = q.replace(/\D/g, '');
    var visibles = 0;
    $('.item-conv').each(function() {
        var raw = ($(this).attr('data-buscar') || '').toLowerCase();
        var coincide = q === ''
            || raw.indexOf(q) !== -1
            || (qDigitos.length >= 3 && raw.indexOf(qDigitos) !== -1);
        $(this).toggle(coincide);
        if (coincide) visibles++;
    });
    var total = $('.item-conv').length;
    $('#sinResultadosBuscar').toggleClass('d-none', q === '' || visibles > 0 || total === 0);
}

function formatearFecha(f) {
    if (!f) return '';
    var d = new Date(f.replace(' ', 'T'));
    if (isNaN(d.getTime())) return f;
    return d.toLocaleString('es-CL', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function etiquetaEstado(m) {
    if (m.direccion !== 'enviado' || !m.estado_envio) return '';
    var e = m.estado_envio;
    if (e === 'enviado' || e === 'sent') return ' · enviado';
    if (e === 'pendiente') return ' · enviado';
    return ' · ' + e;
}

function htmlBurbuja(m) {
    var cls = m.direccion === 'enviado' ? 'enviado' : 'recibido';
    return '<div class="burbuja ' + cls + '" data-msg-id="' + m.id + '">' + escapeHtml(m.mensaje).replace(/\n/g, '<br>') +
        '<div class="meta">' + formatearFecha(m.fecha) + etiquetaEstado(m) + '</div></div>';
}

function aplicarEstadoVentana(res) {
    $('#hiloFooter').show();
    if (res.puede_responder_libre) {
        $('#avisoVentana').hide();
        $('#textoMensaje, #btnEnviarMensaje').prop('disabled', false);
    } else {
        $('#avisoVentana').text('Sin ventana de 24 h: el paciente debe haber escrito recientemente para responder con texto libre. Use plantillas desde Agenda si hace falta.').show();
        $('#textoMensaje, #btnEnviarMensaje').prop('disabled', true);
    }
}

function actualizarPaginacionHilo(res) {
    hiloHasMoreOlder = !!res.has_more_older;
    hiloOldestId = res.oldest_id || (res.mensajes.length ? res.mensajes[0].id : null);
}

function renderHiloInicial(res) {
    var html = '';
    if (!res.mensajes.length) {
        html = '<div class="empty-hilo">Sin mensajes para este paciente.</div>';
        hiloHasMoreOlder = false;
        hiloOldestId = null;
        hiloUltimoId = 0;
    } else {
        if (res.has_more_older) {
            html += '<div id="hiloLoaderAnteriores"><a href="#" class="text-muted" id="btnCargarAnteriores">Cargar mensajes anteriores</a></div>';
        }
        res.mensajes.forEach(function(m) { html += htmlBurbuja(m); });
        actualizarPaginacionHilo(res);
        hiloUltimoId = res.ultimo_id || maxIdEnHilo();
    }
    $('#hiloBody').html(html);
    var el = document.getElementById('hiloBody');
    el.scrollTop = el.scrollHeight;
}

function appendMensajesNuevos(mensajes) {
    if (!mensajes || !mensajes.length) return;
    var el = document.getElementById('hiloBody');
    var cercaDelFinal = el.scrollHeight - el.scrollTop - el.clientHeight < 120;
    mensajes.forEach(function(m) {
        if ($('#hiloBody .burbuja[data-msg-id="' + m.id + '"]').length) return;
        $('#hiloBody').append(htmlBurbuja(m));
        if (m.id > hiloUltimoId) hiloUltimoId = m.id;
    });
    if (cercaDelFinal) el.scrollTop = el.scrollHeight;
}

function sincronizarMensajes() {
    var params = {};
    if (pacienteActivoId) {
        params.paciente_id = pacienteActivoId;
        params.after_id = hiloUltimoId || maxIdEnHilo();
    }
    $.get(URL_SYNC, params, function(res) {
        if (!res.success) return;
        if (res.conversaciones) renderListaConversaciones(res.conversaciones);
        if (pacienteActivoId && res.nuevos_mensajes && res.nuevos_mensajes.length) {
            var vacio = $('#hiloBody .empty-hilo').length;
            if (vacio) $('#hiloBody').empty();
            appendMensajesNuevos(res.nuevos_mensajes);
            if (res.ultimo_id) hiloUltimoId = res.ultimo_id;
            if (typeof res.puede_responder_libre !== 'undefined') {
                aplicarEstadoVentana({ puede_responder_libre: res.puede_responder_libre });
            }
        }
    });
}

function iniciarSincronizacion() {
    if (syncTimer) clearInterval(syncTimer);
    sincronizarMensajes();
    syncTimer = setInterval(sincronizarMensajes, SYNC_INTERVAL_MS);
}

function cargarHilo(pacienteId) {
    pacienteActivoId = pacienteId;
    hiloOldestId = null;
    hiloHasMoreOlder = false;
    hiloCargandoAnteriores = false;
    $('#hiloBody').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin"></i></div>');
    $('#hiloFooter').hide();

    $.get('<?= base_url('dashboard/mensajes/hilo') ?>/' + pacienteId, { limit: MENSAJES_POR_LOTE }, function(res) {
        if (!res.success) {
            toastr.error(res.message || 'Error al cargar');
            return;
        }
        $('#hiloHeader').html(htmlHeaderContacto(res.paciente));
        renderHiloInicial(res);
        aplicarEstadoVentana(res);
        sincronizarMensajes();
    }).fail(function() {
        toastr.error('No se pudo cargar la conversación');
    });
}

function cargarMensajesAnteriores() {
    if (!pacienteActivoId || !hiloHasMoreOlder || hiloCargandoAnteriores || !hiloOldestId) return;
    hiloCargandoAnteriores = true;
    var $loader = $('#hiloLoaderAnteriores');
    if ($loader.length) {
        $loader.html('<i class="fas fa-spinner fa-spin"></i> Cargando anteriores…');
    }

    var el = document.getElementById('hiloBody');
    var scrollAntes = el.scrollHeight;

    $.get('<?= base_url('dashboard/mensajes/hilo') ?>/' + pacienteActivoId, {
        limit: MENSAJES_POR_LOTE,
        before_id: hiloOldestId
    }, function(res) {
        hiloCargandoAnteriores = false;
        if (!res.success || !res.mensajes.length) {
            if ($loader.length) $loader.remove();
            hiloHasMoreOlder = false;
            return;
        }
        var htmlNuevo = '';
        res.mensajes.forEach(function(m) { htmlNuevo += htmlBurbuja(m); });
        actualizarPaginacionHilo(res);

        if (res.has_more_older) {
            htmlNuevo += '<div id="hiloLoaderAnteriores"><a href="#" class="text-muted" id="btnCargarAnteriores">Cargar mensajes anteriores</a></div>';
        }
        if ($loader.length) {
            $loader.replaceWith(htmlNuevo);
        } else {
            $('#hiloBody').prepend(htmlNuevo);
        }
        el.scrollTop = el.scrollHeight - scrollAntes;
    }).fail(function() {
        hiloCargandoAnteriores = false;
        if ($loader.length) {
            $loader.html('<a href="#" class="text-muted" id="btnCargarAnteriores">Reintentar cargar anteriores</a>');
        }
        toastr.error('No se pudieron cargar mensajes anteriores');
    });
}

$('#hiloBody').on('scroll', function() {
    if (this.scrollTop < 80) {
        cargarMensajesAnteriores();
    }
});

$(document).on('click', '#btnCargarAnteriores', function(e) {
    e.preventDefault();
    cargarMensajesAnteriores();
});

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

$('#buscarConversaciones').on('input', filtrarConversaciones);

$(function() {
    iniciarSincronizacion();
    var inicial = <?= (int) ($paciente_id_inicial ?? 0) ?>;
    if (inicial) {
        var $item = $('.item-conv[data-paciente-id="' + inicial + '"]');
        if ($item.length) {
            $('#buscarConversaciones').val('');
            filtrarConversaciones();
            $item.addClass('active').click();
        }
    }
});

$(window).on('beforeunload', function() {
    if (syncTimer) clearInterval(syncTimer);
});
</script>

<?= $this->endSection() ?>

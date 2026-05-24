<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/gestionar') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid #667eea;
    }
    .section-title { font-size: 1.15rem; font-weight: 600; margin-bottom: 8px; }
    .section-subtitle { color: #6c757d; margin-bottom: 16px; }
    #tablaAgendas tbody tr.fila-bloqueada { background-color: #fff8f8; }
    #tablaAgendas .col-check { width: 40px; }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-edit me-2"></i> Editar agenda</h2>
                        <p style="color: white;" class="mb-0">Cree, edite o elimine días de atención y sus bloques horarios</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light">
                            <i class="fas fa-calendar-alt me-1"></i> Calendario
                        </a>
                        <a href="<?= base_url('dashboard/agenda/lista') ?>" class="btn btn-light">
                            <i class="fas fa-list me-1"></i> Lista de citas
                        </a>
                        <a href="<?= base_url('dashboard/agenda/cancelar-horas') ?>" class="btn btn-warning">
                            <i class="fas fa-calendar-times me-1"></i> Cancelar horas
                        </a>
                        <button type="button" class="btn btn-light" onclick="abrirModalCrear()">
                            <i class="fas fa-plus me-1"></i> Crear horarios
                        </button>
                        <button type="button" class="btn btn-primary" id="btnEditarSeleccionados" disabled onclick="prepararEditarSeleccionados()">
                            <i class="fas fa-edit me-1"></i> Editar seleccionados
                        </button>
                        <button type="button" class="btn btn-danger" id="btnEliminarSeleccionados" disabled onclick="confirmarEliminarSeleccionados()">
                            <i class="fas fa-trash me-1"></i> Eliminar seleccionados
                        </button>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title"><i class="fas fa-filter me-2"></i> Filtros</div>
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Desde</label>
                        <input type="date" class="form-control" id="filtro_desde">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Hasta</label>
                        <input type="date" class="form-control" id="filtro_hasta">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="filtro_solo_futuros" checked>
                            <label class="form-check-label" for="filtro_solo_futuros">Solo días futuros</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="cargarAgendasDias()">
                            <i class="fas fa-sync me-1"></i> Actualizar lista
                        </button>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-title"><i class="fas fa-table me-2"></i> Días de agenda</div>
                <p class="section-subtitle">
                    Cada fila es un <strong>día</strong> con su horario laboral. Seleccione uno o varios para editar (mismo horario) o eliminar.
                    Si el día tiene citas con paciente, primero use <a href="<?= base_url('dashboard/agenda/cancelar-horas') ?>">Cancelar horas</a>.
                </p>
                <div class="table-responsive">
                    <table id="tablaAgendas" class="table table-bordered table-striped table-hover" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="col-check">
                                    <input type="checkbox" id="check_todos" title="Seleccionar todos">
                                </th>
                                <th>Fecha</th>
                                <th>Horario día</th>
                                <th>Almuerzo</th>
                                <th>Duración cita</th>
                                <th>Bloques</th>
                                <th>Resumen</th>
                                <th>Disponibles</th>
                                <th>Ocupados</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAgendas">
                            <tr><td colspan="10" class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal configurar horarios -->
<div class="modal fade" id="modalConfirmarCrearHorarios" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="modalHorariosTitulo">
                    <i class="fas fa-clock me-2"></i> Configurar horarios
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCrearHorarios">
                <div class="modal-body">
                    <?= view('Modulos/agenda/_form_configurar_horarios', ['modalidades' => $modalidades, 'modoFormHorarios' => 'crear']) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitHorarios">
                        <i class="fas fa-check me-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal confirmar eliminación de días -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmarEliminarLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt text-danger" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="text-center mb-3">¿Eliminar los días seleccionados?</h5>
                <p class="text-center text-muted mb-2">
                    Se eliminarán <strong id="modalEliminarCantidad">0</strong> día(s) de agenda y todos sus bloques horarios.
                </p>
                <p class="text-center small text-muted mb-3" id="modalEliminarFechas"></p>
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Esta acción no se puede deshacer.</strong> Solo se eliminan días sin citas con paciente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> No, volver
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminarAgendas">
                    <i class="fas fa-trash me-2"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
var modoFormHorarios = 'crear';
var agendasDiasCache = [];
var agendaIdsEditar = [];

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 5000
};

function obtenerTokenCSRF() {
    var cookies = document.cookie.split(';');
    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        if (cookie.indexOf('csrf_cookie_name=') !== -1) {
            var parts = cookie.split('=');
            if (parts.length >= 2) {
                var token = decodeURIComponent(parts.slice(1).join('='));
                if (token) return token;
            }
        }
    }
    var metaToken = $('meta[name="csrf-token"]').attr('content');
    if (metaToken) return metaToken;
    var inputToken = $('input[name="csrf_test_name"]').val();
    if (inputToken) return inputToken;
    return '<?= csrf_hash() ?>';
}

function actualizarTokenCSRF(xhr, jsonData) {
    if (jsonData && jsonData.csrf_token) {
        $('meta[name="csrf-token"]').attr('content', jsonData.csrf_token);
        $('input[name="csrf_test_name"]').val(jsonData.csrf_token);
    }
    if (xhr && xhr.getResponseHeader) {
        var headerToken = xhr.getResponseHeader('X-CSRF-TOKEN');
        if (headerToken) {
            $('meta[name="csrf-token"]').attr('content', headerToken);
            $('input[name="csrf_test_name"]').val(headerToken);
        }
    }
    var cookieToken = obtenerTokenCSRF();
    if (cookieToken) {
        $('meta[name="csrf-token"]').attr('content', cookieToken);
    }
}

function tokenCsrfParaPost(respuestaJson, jqXHR) {
    actualizarTokenCSRF(jqXHR || null, respuestaJson || null);
    return obtenerTokenCSRF();
}

function getSeleccionados() {
    var ids = [];
    $('.check-agenda:checked:not(:disabled)').each(function() {
        ids.push(parseInt($(this).val(), 10));
    });
    return ids;
}

function actualizarBotonesSeleccion() {
    var n = getSeleccionados().length;
    $('#btnEditarSeleccionados').prop('disabled', n === 0);
    $('#btnEliminarSeleccionados').prop('disabled', n === 0);
}

function setModoFormulario(modo) {
    modoFormHorarios = modo;
    if (modo === 'editar') {
        $('#bloque_form_crear').hide();
        $('#bloque_form_editar_info').show();
        $('#modalHorariosTitulo').html('<i class="fas fa-edit me-2"></i> Editar días seleccionados');
        $('#btnSubmitHorarios').html('<i class="fas fa-save me-2"></i> Aplicar cambios');
        $('#texto_nota_horarios').text('Se actualizará el horario del día y se regenerarán solo los bloques disponibles (sin paciente).');
        $('#fecha_inicio, #dias_crear').prop('required', false);
    } else {
        $('#bloque_form_crear').show();
        $('#bloque_form_editar_info').hide();
        $('#modalHorariosTitulo').html('<i class="fas fa-clock me-2"></i> Crear horarios disponibles');
        $('#btnSubmitHorarios').html('<i class="fas fa-check me-2"></i> Crear horarios');
        $('#texto_nota_horarios').text('Cree bloques en los días laborables indicados.');
        $('#fecha_inicio, #dias_crear').prop('required', true);
    }
}

function abrirModalCrear() {
    setModoFormulario('crear');
    agendaIdsEditar = [];
    $('#formCrearHorarios')[0].reset();
    $('#dia_lunes, #dia_martes, #dia_miercoles, #dia_jueves, #dia_viernes').prop('checked', true);
    $('#dia_sabado, #dia_domingo').prop('checked', false);
    $('#duracion_cita').val(30);
    $('#hora_inicio').val('09:00');
    $('#hora_fin').val('18:00');
    $('#horario_almuerzo').hide();
    $('#modalConfirmarCrearHorarios').modal('show');
}

function cargarAgendasDias() {
    var params = {};
    if ($('#filtro_desde').val()) params.fecha_desde = $('#filtro_desde').val();
    if ($('#filtro_hasta').val()) params.fecha_hasta = $('#filtro_hasta').val();
    if ($('#filtro_solo_futuros').is(':checked')) params.solo_futuros = '1';

    $('#tbodyAgendas').html('<tr><td colspan="10" class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');

    params._ = Date.now();

    $.ajax({
        url: '<?= base_url('dashboard/agenda/listarAgendasDias') ?>',
        type: 'GET',
        data: params,
        cache: false,
        dataType: 'json',
        success: function(res) {
            actualizarTokenCSRF(null, res);
            if (!res.success) {
                toastr.error(res.message || 'Error al cargar');
                return;
            }
            agendasDiasCache = ordenarDiasAsc(res.dias || []);
            renderTablaAgendas();
        },
        error: function() {
            toastr.error('No se pudo cargar la lista de agendas');
            $('#tbodyAgendas').html('<tr><td colspan="10" class="text-danger text-center">Error al cargar</td></tr>');
        }
    });
}

function renderTablaAgendas() {
    var html = '';
    var filas = ordenarDiasAsc(agendasDiasCache);
    if (!filas.length) {
        html = '<tr><td colspan="10" class="text-center text-muted py-4">No hay días de agenda. Use «Crear horarios».</td></tr>';
    } else {
        filas.forEach(function(d) {
            var ordenFecha = parseInt(d.fecha_ymd, 10) || fechaAgendaYmd(d.fecha);
            var bloqueada = d.tiene_citas;
            var estado = bloqueada
                ? '<span class="badge bg-warning text-dark">Tiene citas</span> <a href="' + (d.url_cancelar || '<?= base_url('dashboard/agenda/cancelar-horas') ?>') + '" class="btn btn-xs btn-outline-danger btn-sm ms-1">Ir a cancelar</a>'
                : '<span class="badge bg-success">Editable</span>';
            html += '<tr class="' + (bloqueada ? 'fila-bloqueada' : '') + '">' +
                '<td><input type="checkbox" class="check-agenda" value="' + d.agenda_id + '" ' + (bloqueada ? 'disabled title="Cancele las citas primero"' : '') + '></td>' +
                '<td data-order="' + ordenFecha + '"><strong>' + escapeHtml(d.fecha) + '</strong></td>' +
                '<td>' + escapeHtml(d.hora_inicio) + ' – ' + escapeHtml(d.hora_fin) + '</td>' +
                '<td>' + escapeHtml(d.almuerzo) + '</td>' +
                '<td>' + d.duracion_minutos + ' min</td>' +
                '<td>' + d.total_bloques + '</td>' +
                '<td><small>' + escapeHtml(d.resumen) + '</small></td>' +
                '<td><span class="badge bg-success">' + d.disponibles + '</span></td>' +
                '<td><span class="badge bg-warning text-dark">' + d.ocupados + '</span></td>' +
                '<td>' + estado + '</td></tr>';
        });
    }
    $('#tbodyAgendas').html(html);
    $('#check_todos').prop('checked', false);
    actualizarBotonesSeleccion();
}

function escapeHtml(t) {
    if (t == null) return '';
    return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

/** Convierte DD-MM-YYYY a entero YYYYMMDD (no usar sort alfabético sobre la fecha) */
function fechaAgendaYmd(fecha) {
    if (!fecha) return 0;
    if (typeof fecha === 'number' && fecha > 20000101) return fecha;
    var p = String(fecha).trim().split('-');
    if (p.length !== 3) return 0;
    var d = parseInt(p[0], 10);
    var m = parseInt(p[1], 10);
    var y = parseInt(p[2], 10);
    if (!d || !m || !y) return 0;
    return y * 10000 + m * 100 + d;
}

function ordenarDiasAsc(dias) {
    return (dias || []).slice().sort(function(a, b) {
        var ya = parseInt(a.fecha_ymd, 10) || fechaAgendaYmd(a.fecha);
        var yb = parseInt(b.fecha_ymd, 10) || fechaAgendaYmd(b.fecha);
        if (ya !== yb) return ya - yb;
        return String(a.hora_inicio || '').localeCompare(String(b.hora_inicio || ''));
    });
}

function prepararEditarSeleccionados() {
    var ids = getSeleccionados();
    if (!ids.length) return;

    var csrf = obtenerTokenCSRF();
    $.ajax({
        url: '<?= base_url('dashboard/agenda/validarAgendasSeleccionadas') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
        traditional: true,
        data: { csrf_test_name: csrf, 'agenda_ids[]': ids },
        success: function(res, textStatus, jqXHR) {
            actualizarTokenCSRF(jqXHR, res);
            if (!res.puede_proceder && res.bloqueadas && res.bloqueadas.length) {
                var msg = res.bloqueadas.map(function(b) { return b.mensaje; }).join('\n');
                toastr.error(msg, 'Días con citas', { timeOut: 8000 });
                return;
            }
            agendaIdsEditar = (res.validas || []).map(function(v) { return v.agenda_id; });
            if (!agendaIdsEditar.length) {
                toastr.warning('No hay días válidos para editar');
                return;
            }
            var fechas = (res.validas || []).map(function(v) { return v.fecha; });
            var first = agendasDiasCache.find(function(d) { return d.agenda_id === agendaIdsEditar[0]; });
            setModoFormulario('editar');
            $('#editar_cantidad_dias').text(agendaIdsEditar.length);
            $('#editar_fechas_lista').text(fechas.join(', '));
            if (first) {
                $('#hora_inicio').val(first.hora_inicio);
                $('#hora_fin').val(first.hora_fin);
                $('#duracion_cita').val(first.duracion_minutos);
                var tieneAlmuerzo = first.almuerzo !== 'Sin almuerzo';
                $('#incluir_almuerzo').prop('checked', tieneAlmuerzo);
                if (tieneAlmuerzo && first.almuerzo.indexOf(' - ') > 0) {
                    var parts = first.almuerzo.split(' - ');
                    $('#almuerzo_inicio').val(parts[0]);
                    $('#almuerzo_fin').val(parts[1]);
                    $('#horario_almuerzo').show();
                } else {
                    $('#horario_almuerzo').hide();
                }
            }
            $('#modalConfirmarCrearHorarios').modal('show');
        },
        error: function() { toastr.error('Error al validar selección'); }
    });
}

function confirmarEliminarSeleccionados() {
    var ids = getSeleccionados();
    if (!ids.length) return;

    var fechas = [];
    ids.forEach(function(id) {
        var d = agendasDiasCache.find(function(x) { return x.agenda_id === id; });
        if (d && d.fecha) fechas.push(d.fecha);
    });

    $('#modalEliminarCantidad').text(ids.length);
    $('#modalEliminarFechas').text(fechas.length ? 'Fechas: ' + fechas.join(', ') : '');
    $('#modalConfirmarEliminar').modal('show');
}

function ejecutarEliminarSeleccionados() {
    var ids = getSeleccionados();
    if (!ids.length) return;

    var csrf = obtenerTokenCSRF();
    var $btn = $('#btnConfirmarEliminarAgendas');
    $btn.prop('disabled', true);

    $.ajax({
        url: '<?= base_url('dashboard/agenda/validarAgendasSeleccionadas') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
        traditional: true,
        data: { csrf_test_name: csrf, 'agenda_ids[]': ids },
        success: function(res, textStatus, jqXHR) {
            var csrfNuevo = tokenCsrfParaPost(res, jqXHR);
            if (!res.puede_proceder) {
                $btn.prop('disabled', false);
                $('#modalConfirmarEliminar').modal('hide');
                toastr.error('Algunos días tienen citas. Cancélelas primero en Cancelar horas.', '', { timeOut: 7000 });
                if (res.bloqueadas && res.bloqueadas.length) {
                    res.bloqueadas.forEach(function(b) { toastr.warning(b.mensaje); });
                }
                return;
            }
            var idsEliminar = (res.validas || []).map(function(v) { return v.agenda_id; });
            $.ajax({
                url: '<?= base_url('dashboard/agenda/eliminarAgendasDias') ?>',
                type: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfNuevo },
                traditional: true,
                data: {
                    csrf_test_name: csrfNuevo,
                    'agenda_ids[]': idsEliminar
                },
                success: function(r2, ts2, xhr2) {
                    $btn.prop('disabled', false);
                    actualizarTokenCSRF(xhr2, r2);
                    $('#modalConfirmarEliminar').modal('hide');
                    if (r2.success) {
                        toastr.success(r2.message);
                        $('#check_todos').prop('checked', false);
                        cargarAgendasDias();
                    } else {
                        toastr.error(r2.message || 'Error al eliminar');
                    }
                },
                error: function(xhr2) {
                    $btn.prop('disabled', false);
                    if (xhr2.status === 403) {
                        toastr.error('Sesión CSRF expirada. Recargue la página e intente de nuevo.');
                    } else {
                        toastr.error('Error al eliminar los días');
                    }
                }
            });
        },
        error: function(xhr) {
            $btn.prop('disabled', false);
            if (xhr.status === 403) {
                toastr.error('Sesión CSRF expirada. Recargue la página e intente de nuevo.');
            } else {
                toastr.error('Error al validar la selección');
            }
        }
    });
}

function recolectarDatosHorarios() {
    var data = {
        hora_inicio: $('#hora_inicio').val(),
        hora_fin: $('#hora_fin').val(),
        duracion: $('#duracion_cita').val(),
        incluir_almuerzo: $('#incluir_almuerzo').is(':checked') ? 'on' : '',
        almuerzo_inicio: $('#almuerzo_inicio').val(),
        almuerzo_fin: $('#almuerzo_fin').val(),
        modalidad_id: $('#modalidad_id').val() || 3
    };
    if (modoFormHorarios === 'crear') {
        data.fecha_inicio = $('#fecha_inicio').val();
        data.dias = $('#dias_crear').val();
        data['dias_semana'] = [];
        $('input[name="dias_semana[]"]:checked').each(function() {
            data['dias_semana'].push($(this).val());
        });
    } else {
        data['agenda_ids[]'] = agendaIdsEditar;
    }
    return data;
}

$(document).ready(function() {
    cargarAgendasDias();

    $('#check_todos').on('change', function() {
        var checked = $(this).is(':checked');
        $('.check-agenda:not(:disabled)').prop('checked', checked);
        actualizarBotonesSeleccion();
    });

    $(document).on('change', '.check-agenda', actualizarBotonesSeleccion);

    $('#incluir_almuerzo').on('change', function() {
        if ($(this).is(':checked')) $('#horario_almuerzo').slideDown();
        else $('#horario_almuerzo').slideUp();
    });

    $('#btnConfirmarEliminarAgendas').on('click', ejecutarEliminarSeleccionados);

    $('#formCrearHorarios').on('submit', function(e) {
        e.preventDefault();
        if (modoFormHorarios === 'crear') {
            if (!$('input[name="dias_semana[]"]:checked').length) {
                toastr.error('Seleccione al menos un día de la semana');
                return;
            }
            if ($('#hora_fin').val() <= $('#hora_inicio').val()) {
                toastr.error('La hora de fin debe ser mayor que la de inicio');
                return;
            }
        }

        var csrf = obtenerTokenCSRF();
        var url = modoFormHorarios === 'crear'
            ? '<?= base_url('dashboard/agenda/crearHorarios') ?>'
            : '<?= base_url('dashboard/agenda/actualizarAgendas') ?>';
        var payload = recolectarDatosHorarios();
        payload.csrf_test_name = csrf;

        $('#btnSubmitHorarios').prop('disabled', true);
        $.ajax({
            url: url,
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
            traditional: true,
            data: payload,
            success: function(res, textStatus, jqXHR) {
                actualizarTokenCSRF(jqXHR, res);
                $('#btnSubmitHorarios').prop('disabled', false);
                if (res.success) {
                    toastr.success(res.message);
                    $('#modalConfirmarCrearHorarios').modal('hide');
                    cargarAgendasDias();
                } else {
                    toastr.error(res.message || 'Error');
                    if (res.bloqueadas) {
                        res.bloqueadas.forEach(function(b) { toastr.warning(b.mensaje); });
                    }
                }
            },
            error: function(xhr) {
                $('#btnSubmitHorarios').prop('disabled', false);
                if (xhr.status === 403) {
                    toastr.error('Sesión CSRF expirada. Recargue la página e intente de nuevo.');
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al guardar';
                    toastr.error(msg);
                }
            }
        });
    });
});
</script>

<?= $this->endSection() ?>

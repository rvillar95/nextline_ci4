<?= $this->extend('layout/dashboard') ?>

<?= $this->section('documento/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-file-medical me-2"></i> Gestión de Documentos</h2>
                        <p style="color: white;">Administre documentos y envíelos por correo al paciente</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-success" id="btnEnviarPaciente" title="Seleccione varios archivos de un paciente">
                            <i class="fas fa-envelope me-2"></i> Enviar por correo
                        </button>
                        <a href="<?= base_url('dashboard/documento/registro') ?>" class="btn btn-light">
                            <i class="fas fa-file-upload me-2"></i> Cargar documentos
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-card mb-3" style="border-left-color: #4facfe;">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label mb-0">Filtrar por paciente</label>
                        <select id="filtroPaciente" class="form-control">
                            <option value="">— Todos los pacientes —</option>
                            <?php foreach ($pacientes as $paciente) : ?>
                                <option value="<?= (int) $paciente->id ?>">
                                    <?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?>
                                    <?php if (!empty($paciente->rut_dni)) : ?> (<?= esc($paciente->rut_dni) ?>)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-0">Buscar título</label>
                        <input type="text" id="busquedaDocumento" class="form-control" placeholder="Buscar...">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros">Limpiar filtros</button>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #4facfe;">
                <style>
                    #tablaDocumentos th:last-child,
                    #tablaDocumentos td:last-child { min-width: 300px; }
                </style>
                <div class="table-responsive">
                    <table id="tablaDocumentos" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Enviado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEnviarCorreo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-envelope me-2"></i> Enviar documentos por correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Paciente</label>
                    <select id="modalPacienteId" class="form-control">
                        <option value="">— Seleccione paciente —</option>
                        <?php foreach ($pacientes as $paciente) : ?>
                            <option value="<?= (int) $paciente->id ?>" data-email="<?= esc($paciente->email ?? '', 'attr') ?>">
                                <?= esc($paciente->nombre_completo ?? ($paciente->nombre . ' ' . $paciente->apellido)) ?>
                                <?php if (!empty($paciente->rut_dni)) : ?> (<?= esc($paciente->rut_dni) ?>)<?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small id="modalPacienteEmail" class="text-muted"></small>
                </div>
                <div id="listaDocsEnviar" class="border rounded p-2 mb-3" style="max-height: 280px; overflow-y: auto;">
                    <p class="text-muted mb-0 p-2">Seleccione un paciente para ver sus documentos.</p>
                </div>
                <div class="mb-0">
                    <label for="mensajeEnvioModal" class="form-label">Mensaje para el paciente <span class="text-muted">(opcional)</span></label>
                    <textarea id="mensajeEnvioModal" class="form-control" rows="3" maxlength="2000" placeholder="Ej.: Hola, le envío sus documentos de la consulta. Cualquier duda, con gusto la resolvemos."></textarea>
                    <small class="text-muted">Aparecerá destacado al inicio del correo.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnSeleccionarTodos">Seleccionar todos</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarEnvio" disabled>
                    <i class="fas fa-paper-plane me-1"></i> Enviar seleccionados
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    var table = $('#tablaDocumentos').DataTable({
        processing: true,
        serverSide: false,
        searching: false,
        ajax: {
            url: '<?= base_url('dashboard/documento/getDocumentos') ?>',
            type: 'GET',
            dataSrc: 'data',
            data: function(d) {
                d.paciente_id = $('#filtroPaciente').val();
                d.busqueda = $('#busquedaDocumento').val();
            },
            error: function(xhr) {
                console.error('Error al cargar documentos:', xhr.status, xhr.responseText);
                if (typeof mostrarModalError === 'function') {
                    mostrarModalError('No se pudo cargar la lista de documentos. Verifique su sesión y permisos.');
                }
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false }
        ],
        columnDefs: [
            { orderable: false, targets: 6 },
            { className: 'text-nowrap', targets: [3, 6] }
        ],
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' },
        scrollX: true,
        autoWidth: false,
        pageLength: 25,
        order: [[3, 'desc']]
    });

    $('#filtroPaciente, #busquedaDocumento').on('change keyup', function() {
        if (this.id === 'busquedaDocumento') {
            clearTimeout(window._docSearchTimer);
            window._docSearchTimer = setTimeout(function() { table.ajax.reload(); }, 400);
        } else {
            table.ajax.reload();
        }
    });

    $('#btnLimpiarFiltros').on('click', function() {
        $('#filtroPaciente').val('');
        $('#busquedaDocumento').val('');
        table.ajax.reload();
    });

    window.editarDocumento = function(id) {
        window.location.href = '<?= base_url('dashboard/documento/editar') ?>/' + id;
    };

    window.verDocumento = function(id) {
        window.location.href = '<?= base_url('dashboard/documento/detalle') ?>/' + id;
    };

    window.eliminarDocumento = function(id) {
        mostrarModalConfirmarAccion({
            titulo: '<i class="fas fa-trash text-danger me-2"></i> Eliminar documento',
            mensaje: '¿Eliminar este documento?',
            subtitulo: 'Esta acción no se puede deshacer.',
            textoBtn: 'Eliminar',
            claseBtn: 'btn-danger',
            iconoBtn: 'fas fa-trash',
            onConfirm: function() {
                var fd = new FormData();
                fd.append(csrfName, csrfHash);
                fetch('<?= base_url('dashboard/documento/eliminar') ?>/' + id, {
                    method: 'POST',
                    body: fd,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        mostrarModalExito(data.message || 'Documento eliminado');
                        table.ajax.reload();
                    } else {
                        mostrarModalError(data.error || 'No se pudo eliminar');
                    }
                })
                .catch(function() { mostrarModalError('Error de conexión'); });
            }
        });
    };

    window.enviarDocumento = function(id) {
        var mensajePrevio = $('#mensajeEnvioModal').val() || '';
        mostrarModalConfirmarAccion({
            titulo: '<i class="fas fa-envelope text-success me-2"></i> Enviar por correo',
            mensaje: '¿Enviar este documento por correo al paciente?',
            mostrarMensajePaciente: true,
            mensajeInicial: mensajePrevio,
            textoBtn: 'Enviar',
            claseBtn: 'btn-success',
            iconoBtn: 'fas fa-paper-plane',
            onConfirm: function(mensaje) {
                enviarIdsPorCorreo([id], mensaje, function(ok, msg) {
                    if (ok) {
                        mostrarModalExito(msg);
                        table.ajax.reload();
                    } else {
                        mostrarModalError(msg);
                    }
                });
            }
        });
    };

    function enviarIdsPorCorreo(ids, mensajePersonal, callback) {
        var fd = new FormData();
        fd.append(csrfName, csrfHash);
        if (mensajePersonal) {
            fd.append('mensaje_personal', mensajePersonal);
        }
        ids.forEach(function(id) { fd.append('documento_ids[]', id); });
        fetch('<?= base_url('dashboard/documento/enviar-correo') ?>', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            callback(data.success, data.success ? data.message : (data.error || 'Error al enviar'));
        })
        .catch(function() { callback(false, 'Error de conexión'); });
    }

    $('#btnEnviarPaciente').on('click', function() {
        var pid = $('#filtroPaciente').val();
        if (pid) $('#modalPacienteId').val(pid);
        cargarDocumentosModal();
        new bootstrap.Modal(document.getElementById('modalEnviarCorreo')).show();
    });

    $('#modalPacienteId').on('change', cargarDocumentosModal);

    function cargarDocumentosModal() {
        var pid = $('#modalPacienteId').val();
        var $lista = $('#listaDocsEnviar');
        var $email = $('#modalPacienteEmail');
        var $btn = $('#btnConfirmarEnvio');
        $btn.prop('disabled', true);

        if (!pid) {
            $lista.html('<p class="text-muted mb-0 p-2">Seleccione un paciente.</p>');
            $email.text('');
            return;
        }

        var opt = $('#modalPacienteId option:selected');
        var em = opt.data('email') || '';
        $email.text(em ? 'Correo: ' + em : '⚠ Este paciente no tiene correo registrado');

        $lista.html('<p class="text-muted p-2">Cargando...</p>');
        var urlEnvio = '<?= base_url('dashboard/documento/getDocumentos') ?>?para_envio=1&paciente_id=' + encodeURIComponent(pid);
        fetch(urlEnvio, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) {
                if (!r.ok) {
                    throw new Error('No se pudo cargar la lista (código ' + r.status + '). ¿Tiene permiso en Documentos?');
                }
                return r.json();
            })
            .then(function(data) {
                if (!data.success) {
                    $lista.html('<p class="text-danger mb-0 p-2">' + escapeHtml(data.error || 'Error al cargar documentos') + '</p>');
                    return;
                }
                if (data.paciente && data.paciente.email) {
                    $email.text('Correo: ' + data.paciente.email);
                }
                if (!data.documentos || !data.documentos.length) {
                    $lista.html('<p class="text-muted mb-0 p-2">No hay documentos activos para este paciente. Verifique que eligió el paciente correcto (mismo RUT que en la tabla).</p>');
                    return;
                }
                var html = '<div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="chkTodosDocs"><label class="form-check-label" for="chkTodosDocs"><strong>Marcar todos</strong></label></div>';
                data.documentos.forEach(function(doc) {
                    var badge = doc.enviado ? '<span class="badge bg-success ms-1">enviado</span>' : '<span class="badge bg-warning ms-1">pendiente</span>';
                    var archivo = doc.tiene_archivo ? '<i class="fas fa-paperclip text-muted ms-1" title="Tiene archivo"></i>' : '';
                    html += '<div class="form-check border-bottom py-2">' +
                        '<input class="form-check-input doc-enviar-chk" type="checkbox" value="' + doc.id + '" id="doc_' + doc.id + '">' +
                        '<label class="form-check-label" for="doc_' + doc.id + '">' +
                        '<strong>' + escapeHtml(doc.titulo) + '</strong> · ' + escapeHtml(doc.fecha) + badge + archivo +
                        '</label></div>';
                });
                $lista.html(html);
                $btn.prop('disabled', false);

                $('#chkTodosDocs').on('change', function() {
                    $('.doc-enviar-chk').prop('checked', this.checked);
                });
            })
            .catch(function(err) {
                $lista.html('<p class="text-danger mb-0 p-2">' + escapeHtml(err.message || 'Error de conexión') + '</p>');
            });
    }

    $('#btnSeleccionarTodos').on('click', function() {
        $('.doc-enviar-chk').prop('checked', true);
        $('#chkTodosDocs').prop('checked', true);
    });

    $('#btnConfirmarEnvio').on('click', function() {
        if (typeof mostrarModalConfirmarAccion !== 'function') {
            mostrarModalError('No se cargó lib/js/modals.js. Suba la carpeta lib/ al hosting.');
            return;
        }
        var ids = [];
        $('.doc-enviar-chk:checked').each(function() { ids.push(parseInt(this.value, 10)); });
        if (!ids.length) {
            mostrarModalInformacion('Seleccione al menos un documento de la lista.');
            return;
        }
        var $btnEnvio = $(this);
        var n = ids.length;
        var correoTxt = $('#modalPacienteEmail').text().replace(/^Correo:\s*/i, '').trim();
        var mensajePersonal = $('#mensajeEnvioModal').val().trim();
        mostrarModalConfirmarAccion({
            titulo: '<i class="fas fa-envelope text-success me-2"></i> Confirmar envío',
            mensaje: '¿Enviar ' + n + ' documento' + (n === 1 ? '' : 's') + ' por correo al paciente?',
            subtitulo: correoTxt ? ('Se enviará a: ' + correoTxt) : '',
            textoBtn: 'Enviar ahora',
            claseBtn: 'btn-success',
            iconoBtn: 'fas fa-paper-plane',
            onConfirm: function() {
                $btnEnvio.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');
                enviarIdsPorCorreo(ids, mensajePersonal, function(ok, msg) {
                    $btnEnvio.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Enviar seleccionados');
                    if (ok) {
                        bootstrap.Modal.getInstance(document.getElementById('modalEnviarCorreo'))?.hide();
                        mostrarModalExito(msg);
                        table.ajax.reload();
                    } else {
                        mostrarModalError(msg);
                    }
                });
            }
        });
    });

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
});
</script>

<?= $this->endSection() ?>

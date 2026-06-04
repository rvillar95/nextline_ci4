/**
 * Modales reutilizables (Bootstrap 5). Reemplaza alert() y confirm() nativos.
 */

function obtenerModalBs(elementId) {
    var el = document.getElementById(elementId);
    if (!el || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        return null;
    }
    return bootstrap.Modal.getOrCreateInstance(el);
}

function mostrarModalEliminar(mensaje, callback) {
    var modalEl = document.getElementById('modalConfirmarEliminar');
    if (!modalEl) {
        if (window.confirm(mensaje || '¿Eliminar?') && typeof callback === 'function') {
            callback();
        }
        return;
    }

    $('#modalMensaje').text(mensaje || '¿Estás seguro de que deseas eliminar este elemento?');
    $('#btnConfirmarEliminar').off('click').on('click', function() {
        obtenerModalBs('modalConfirmarEliminar')?.hide();
        if (typeof callback === 'function') {
            callback();
        }
    });
    obtenerModalBs('modalConfirmarEliminar')?.show();
}

/**
 * Confirmación personalizable (envío por correo, etc.)
 * @param {object} opts - titulo, mensaje, subtitulo, textoBtn, claseBtn, iconoBtn, onConfirm
 */
function mostrarModalConfirmarAccion(opts) {
    opts = opts || {};
    var modalEl = document.getElementById('modalConfirmarAccion');
    if (!modalEl) {
        if (window.confirm(opts.mensaje || '¿Continuar?') && typeof opts.onConfirm === 'function') {
            opts.onConfirm();
        }
        return;
    }

    var titulo = opts.titulo || '<i class="fas fa-question-circle text-primary me-2"></i> Confirmar';
    $('#modalConfirmarAccionTitulo').html(titulo);
    $('#modalConfirmarAccionMensaje').text(opts.mensaje || '¿Desea continuar?');

    var $sub = $('#modalConfirmarAccionSub');
    if (opts.subtitulo) {
        $sub.text(opts.subtitulo).removeClass('d-none');
    } else {
        $sub.addClass('d-none').text('');
    }

    var $campoMensaje = $('#modalConfirmarAccionCampoMensaje');
    var $txtMensaje = $('#modalConfirmarAccionMensajeExtra');
    if (opts.mostrarMensajePaciente) {
        $campoMensaje.removeClass('d-none');
        $txtMensaje.val(opts.mensajeInicial || '');
    } else {
        $campoMensaje.addClass('d-none');
        $txtMensaje.val('');
    }

    var textoBtn = opts.textoBtn || 'Confirmar';
    var $btn = $('#btnConfirmarAccion');
    $btn.off('click');
    $btn.attr('class', 'btn ' + (opts.claseBtn || 'btn-success'));
    if (opts.iconoBtn) {
        $btn.html('<i class="' + opts.iconoBtn + ' me-1"></i> ' + textoBtn);
    } else {
        $btn.text(textoBtn);
    }
    $btn.on('click', function() {
        var mensajePaciente = opts.mostrarMensajePaciente ? $txtMensaje.val().trim() : '';
        obtenerModalBs('modalConfirmarAccion')?.hide();
        if (typeof opts.onConfirm === 'function') {
            if (opts.mostrarMensajePaciente) {
                opts.onConfirm(mensajePaciente);
            } else {
                opts.onConfirm();
            }
        }
    });

    obtenerModalBs('modalConfirmarAccion')?.show();
}

function mostrarModalInformacion(mensaje) {
    $('#modalMensajeInfo').text(mensaje);
    obtenerModalBs('modalInformacion')?.show();
}

function mostrarModalExito(mensaje) {
    $('#modalMensajeExito').text(mensaje);
    obtenerModalBs('modalExito')?.show();
}

function mostrarModalError(mensaje) {
    $('#modalMensajeError').text(mensaje);
    obtenerModalBs('modalError')?.show();
}

function eliminarConConfirmacion(url, mensaje) {
    mostrarModalEliminar(mensaje, function() {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_test_name';
            csrfInput.value = csrfMeta.getAttribute('content');
            form.appendChild(csrfInput);
        }

        document.body.appendChild(form);
        form.submit();
    });
}

function mostrarErroresValidacion(errors) {
    var mensaje = 'Se encontraron los siguientes errores:\n\n';
    for (var campo in errors) {
        if (Object.prototype.hasOwnProperty.call(errors, campo)) {
            mensaje += '• ' + errors[campo] + '\n';
        }
    }
    mostrarModalError(mensaje);
}

function confirmarAccion(mensaje, callback) {
    mostrarModalEliminar(mensaje, callback);
}

function confirmar(mensaje, callback) {
    mostrarModalConfirmarAccion({ mensaje: mensaje, onConfirm: callback });
}

function alertar(mensaje, tipo) {
    tipo = tipo || 'info';
    switch (tipo) {
        case 'success':
            mostrarModalExito(mensaje);
            break;
        case 'error':
            mostrarModalError(mensaje);
            break;
        default:
            mostrarModalInformacion(mensaje);
    }
}

function convertirCotizacionConConfirmacion(url, mensaje) {
    var modalHtml =
        '<div class="modal fade" id="modalConvertirCotizacion" tabindex="-1">' +
        '<div class="modal-dialog modal-dialog-centered"><div class="modal-content">' +
        '<div class="modal-header"><h5 class="modal-title"><i class="fas fa-project-diagram text-success me-2"></i> Convertir cotización</h5>' +
        '<button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>' +
        '<div class="modal-body"><p>' + mensaje + '</p><p class="text-muted small mb-0">Esta acción no se puede deshacer.</p></div>' +
        '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>' +
        '<button type="button" class="btn btn-success" id="btnConfirmarConvertir">Convertir</button></div>' +
        '</div></div></div>';

    $('#modalConvertirCotizacion').remove();
    $('body').append(modalHtml);

    $('#btnConfirmarConvertir').on('click', function() {
        obtenerModalBs('modalConvertirCotizacion')?.hide();
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_test_name';
            csrfInput.value = csrfMeta.getAttribute('content');
            form.appendChild(csrfInput);
        }
        document.body.appendChild(form);
        form.submit();
    });

    obtenerModalBs('modalConvertirCotizacion')?.show();
}

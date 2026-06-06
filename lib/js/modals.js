/**
 * Modales Bootstrap 5 (sin alert/confirm nativos)
 */
(function (window) {
    'use strict';

    function getModal(id) {
        var el = document.getElementById(id);
        if (!el || typeof bootstrap === 'undefined') {
            return null;
        }
        return bootstrap.Modal.getOrCreateInstance(el);
    }

    function showModal(id) {
        var m = getModal(id);
        if (m) {
            m.show();
        }
    }

    function hideModal(id) {
        var el = document.getElementById(id);
        if (el && typeof bootstrap !== 'undefined') {
            var inst = bootstrap.Modal.getInstance(el);
            if (inst) {
                inst.hide();
            }
        }
    }

    function bindConfirmOnce(btnId, callback) {
        var btn = document.getElementById(btnId);
        if (!btn) {
            return;
        }
        var clone = btn.cloneNode(true);
        btn.parentNode.replaceChild(clone, btn);
        clone.addEventListener('click', function () {
            hideModal('modalConfirmarEliminar');
            if (typeof callback === 'function') {
                callback();
            }
        });
    }

    function mostrarModalEliminar(mensaje, callback, opts) {
        opts = opts || {};
        var msgEl = document.getElementById('modalMensaje');
        if (msgEl) {
            msgEl.textContent = mensaje || '¿Estás seguro de que deseas continuar?';
        }
        var titleEl = document.getElementById('modalConfirmarEliminarLabel');
        if (titleEl) {
            titleEl.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> ' + (opts.titulo || 'Confirmar');
        }
        var btnOk = document.getElementById('btnConfirmarEliminar');
        if (btnOk) {
            btnOk.innerHTML = '<i class="fas fa-check"></i> ' + (opts.botonOk || 'Confirmar');
            btnOk.className = 'btn ' + (opts.botonClase || 'btn-primary');
        }
        var hint = document.querySelector('#modalConfirmarEliminar .text-muted.small');
        if (hint) {
            hint.style.display = opts.mostrarHint === false ? 'none' : '';
        }
        bindConfirmOnce('btnConfirmarEliminar', callback);
        showModal('modalConfirmarEliminar');
    }

    function mostrarModalInformacion(mensaje) {
        var el = document.getElementById('modalMensajeInfo');
        if (el) {
            el.textContent = mensaje;
        }
        showModal('modalInformacion');
    }

    function mostrarModalExito(mensaje) {
        var el = document.getElementById('modalMensajeExito');
        if (el) {
            el.textContent = mensaje;
        }
        showModal('modalExito');
    }

    function mostrarModalError(mensaje) {
        var el = document.getElementById('modalMensajeError');
        if (el) {
            el.textContent = mensaje;
        }
        showModal('modalError');
    }

    function eliminarConConfirmacion(url, mensaje) {
        mostrarModalEliminar(mensaje, function () {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = window.NutriNextCsrf ? NutriNextCsrf.getName() : 'csrf_test_name';
            csrfInput.value = window.NutriNextCsrf ? NutriNextCsrf.getToken() : '';
            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }, { titulo: 'Confirmar eliminación', botonOk: 'Eliminar', botonClase: 'btn-danger' });
    }

    function confirmarAccion(mensaje, callback, opts) {
        mostrarModalEliminar(mensaje, callback, opts);
    }

    function alertar(mensaje, tipo) {
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

    function confirmar(mensaje, callback, opts) {
        confirmarAccion(mensaje, callback, opts);
    }

    /** Envía un formulario tras confirmación modal */
    function confirmarFormulario(form, mensaje, opts) {
        if (!form) {
            return;
        }
        form.addEventListener('submit', function (e) {
            if (form.dataset.nnConfirmed === '1') {
                form.dataset.nnConfirmed = '0';
                return;
            }
            e.preventDefault();
            confirmarAccion(mensaje, function () {
                form.dataset.nnConfirmed = '1';
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }, opts);
        });
    }

    function mostrarFlashModales() {
        var body = document.body;
        var err = body.getAttribute('data-flash-error');
        var ok = body.getAttribute('data-flash-success');
        if (err) {
            mostrarModalError(err);
        } else if (ok) {
            mostrarModalExito(ok);
        }
    }

    window.mostrarModalEliminar = mostrarModalEliminar;
    window.mostrarModalInformacion = mostrarModalInformacion;
    window.mostrarModalExito = mostrarModalExito;
    window.mostrarModalError = mostrarModalError;
    window.eliminarConConfirmacion = eliminarConConfirmacion;
    window.confirmarAccion = confirmarAccion;
    window.confirmar = confirmar;
    window.alertar = alertar;
    window.nnNotify = alertar;
    window.confirmarFormulario = confirmarFormulario;

    /** Sustituye alert() nativo del navegador por modales Bootstrap */
    window.alert = function (message) {
        var m = String(message || '');
        if (/guardad|correctamente|éxito|exito|creado|actualizado|completado/i.test(m)) {
            mostrarModalExito(m);
        } else if (/error|fallo|no se pudo|inválid|invalido/i.test(m)) {
            mostrarModalError(m);
        } else {
            mostrarModalInformacion(m);
        }
    };

    document.addEventListener('DOMContentLoaded', mostrarFlashModales);
})(window);

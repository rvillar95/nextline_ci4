/**
 * Toasts unificados para autoguardado en consulta (Mediciones, Registro, Calorimetría, Plan).
 */
(function (global) {
    'use strict';

    function applyDefaults() {
        if (typeof global.toastr === 'undefined') {
            return false;
        }
        global.toastr.options = Object.assign({
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 2000,
            extendedTimeOut: 1000
        }, global.toastr.options || {});
        return true;
    }

    global.toastGuardadoExito = function (mensaje) {
        if (!applyDefaults()) {
            return;
        }
        global.toastr.success(mensaje || 'Cambios guardados correctamente', 'Éxito');
    };

    global.toastGuardadoError = function (mensaje) {
        if (!applyDefaults()) {
            return;
        }
        global.toastr.error(mensaje || 'No se pudieron guardar los cambios', 'Error');
    };

    applyDefaults();
})(typeof window !== 'undefined' ? window : this);

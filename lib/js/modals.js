/**
 * Funciones reutilizables para modales
 * Reemplaza alert(), confirm() y otros diálogos nativos
 */

// Función para mostrar modal de confirmación de eliminación
function mostrarModalEliminar(mensaje, callback) {
    $('#modalMensaje').text(mensaje || '¿Estás seguro de que deseas eliminar este elemento?');
    
    // Remover eventos anteriores
    $('#btnConfirmarEliminar').off('click');
    
    // Agregar nuevo evento
    $('#btnConfirmarEliminar').on('click', function() {
        $('#modalConfirmarEliminar').modal('hide');
        if (typeof callback === 'function') {
            callback();
        }
    });
    
    $('#modalConfirmarEliminar').modal('show');
}

// Función para mostrar modal de información
function mostrarModalInformacion(mensaje) {
    $('#modalMensajeInfo').text(mensaje);
    $('#modalInformacion').modal('show');
}

// Función para mostrar modal de éxito
function mostrarModalExito(mensaje) {
    $('#modalMensajeExito').text(mensaje);
    $('#modalExito').modal('show');
}

// Función para mostrar modal de error
function mostrarModalError(mensaje) {
    $('#modalMensajeError').text(mensaje);
    $('#modalError').modal('show');
}

// Función para eliminar elemento con confirmación
function eliminarConConfirmacion(url, mensaje) {
    mostrarModalEliminar(mensaje, function() {
        // Crear un formulario temporal para enviar POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        // Agregar token CSRF
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_test_name';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    });
}

// Función para mostrar alertas de éxito/error desde PHP
function mostrarAlertaFlash() {
    // Esta función debe ser llamada desde PHP en la vista
    // Ejemplo de uso en la vista:
    // <script>
    // $(document).ready(function() {
    //     <?php if (session()->getFlashdata('success')): ?>
    //         mostrarModalExito('<?= addslashes(session()->getFlashdata('success')) ?>');
    //     <?php endif; ?>
    //     <?php if (session()->getFlashdata('error')): ?>
    //         mostrarModalError('<?= addslashes(session()->getFlashdata('error')) ?>');
    //     <?php endif; ?>
    // });
    // </script>
}

// Función para mostrar alertas de validación
function mostrarErroresValidacion(errors) {
    var mensaje = 'Se encontraron los siguientes errores:\n\n';
    for (var campo in errors) {
        mensaje += '• ' + errors[campo] + '\n';
    }
    mostrarModalError(mensaje);
}

// Función para confirmar acción personalizada
function confirmarAccion(mensaje, callback) {
    mostrarModalEliminar(mensaje, callback);
}

// Función específica para convertir cotización en proyecto
function convertirCotizacionConConfirmacion(url, mensaje) {
    // Crear modal dinámico para conversión
    var modalHtml = `
        <div class="modal fade" id="modalConvertirCotizacion" tabindex="-1" aria-labelledby="modalConvertirCotizacionLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalConvertirCotizacionLabel">
                            <i class="fas fa-project-diagram text-success"></i> Convertir Cotización
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>${mensaje}</p>
                        <p class="text-muted small">Esta acción no se puede deshacer.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-success" id="btnConfirmarConvertir">
                            <i class="fas fa-project-diagram"></i> Convertir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal anterior si existe
    $('#modalConvertirCotizacion').remove();
    
    // Agregar nuevo modal al body
    $('body').append(modalHtml);
    
    // Configurar evento del botón
    $('#btnConfirmarConvertir').on('click', function() {
        $('#modalConvertirCotizacion').modal('hide');
        
        // Crear formulario para enviar POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        // Agregar token CSRF
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_test_name';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    });
    
    // Mostrar modal
    $('#modalConvertirCotizacion').modal('show');
}

// Reemplazar confirm() nativo
function confirmar(mensaje, callback) {
    mostrarModalEliminar(mensaje, callback);
}

// Reemplazar alert() nativo
function alertar(mensaje, tipo = 'info') {
    switch(tipo) {
        case 'success':
            mostrarModalExito(mensaje);
            break;
        case 'error':
            mostrarModalError(mensaje);
            break;
        case 'warning':
            mostrarModalInformacion(mensaje);
            break;
        default:
            mostrarModalInformacion(mensaje);
    }
}

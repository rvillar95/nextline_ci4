<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paquete/gestionar_modulos') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Gestionar Módulos: <?= esc($paquete->nombre) ?>
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver a Lista
                        </a>
                        <a href="<?= base_url('dashboard/paquete/editar/' . $paquete->id) ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar Paquete
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Área para mensajes dinámicos -->
                    <div id="mensajeResultado" style="display: none;"></div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Selecciona los módulos que estarán incluidos en este paquete. 
                        Las empresas con este paquete solo podrán acceder a los módulos seleccionados.
                    </div>

                    <form id="formModulos" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" id="csrf_token_input" value="<?= csrf_hash() ?>">
                        <input type="hidden" name="paquete_id" value="<?= $paquete->id ?>">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-sm btn-success" onclick="seleccionarTodos()">
                                    <i class="fas fa-check-square"></i> Seleccionar Todos
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="deseleccionarTodos()">
                                    <i class="fas fa-square"></i> Deseleccionar Todos
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach ($modulos as $modulo): ?>
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   name="modulos[]" 
                                                   value="<?= $modulo['id'] ?>" 
                                                   id="modulo_<?= $modulo['id'] ?>"
                                                   <?= in_array($modulo['id'], $modulosAsignados) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="modulo_<?= $modulo['id'] ?>">
                                                <strong><?= esc($modulo['nombre']) ?></strong>
                                                <?php if (!empty($modulo['descripcion'])): ?>
                                                    <br><small class="text-muted"><?= esc($modulo['descripcion']) ?></small>
                                                <?php endif; ?>
                                                <br><small class="text-info"><i class="fas fa-route"></i> <?= esc($modulo['ruta']) ?></small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Módulos
                                </button>
                                <a href="<?= base_url('dashboard/paquete/lista') ?>" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
        // Actualizar también el campo del formulario
        $('input[name="<?= csrf_token() ?>"]').val(headerToken);
        return;
    }
    // O de la respuesta JSON si está disponible
    if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
        var jsonToken = xhr.responseJSON.csrf_token;
        $('#csrf_token_input').val(jsonToken);
        $('meta[name="csrf-token"]').attr('content', jsonToken);
        $('input[name="<?= csrf_token() ?>"]').val(jsonToken);
        return;
    }
}

function seleccionarTodos() {
    $('input[name="modulos[]"]').prop('checked', true);
}

function deseleccionarTodos() {
    $('input[name="modulos[]"]').prop('checked', false);
}

// Función para mostrar mensajes en la página
function mostrarMensaje(mensaje, tipo) {
    var alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
    var icon = tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    var html = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
               '<i class="fas ' + icon + ' me-2"></i>' + mensaje +
               '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
               '</div>';
    $('#mensajeResultado').html(html).slideDown();
    
    // Auto-ocultar después de 5 segundos si es éxito
    if (tipo === 'success') {
        setTimeout(function() {
            $('#mensajeResultado').slideUp();
        }, 5000);
    }
}

$('#formModulos').on('submit', function(e) {
    e.preventDefault();
    
    var csrfToken = obtenerTokenCSRF();
    var csrfTokenName = '<?= csrf_token() ?>';
    
    // Obtener los datos del formulario serializados
    var formDataString = $(this).serialize();
    
    // Actualizar el token CSRF en la cadena serializada
    var regex = new RegExp(csrfTokenName + '=[^&]*');
    if (regex.test(formDataString)) {
        formDataString = formDataString.replace(regex, csrfTokenName + '=' + encodeURIComponent(csrfToken));
    } else {
        formDataString += '&' + csrfTokenName + '=' + encodeURIComponent(csrfToken);
    }
    
    $.ajax({
        url: '<?= base_url('dashboard/paquete/guardar-modulos') ?>',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        },
        data: formDataString,
        success: function(response, textStatus, xhr) {
            console.log('Respuesta recibida:', response);
            
            // Actualizar el token CSRF después de la respuesta
            actualizarTokenCSRF(xhr);
            
            if (response && response.success) {
                var mensaje = response.message || 'Módulos actualizados con éxito';
                console.log('Éxito:', mensaje);
                
                // Mostrar mensaje en la página
                mostrarMensaje(mensaje, 'success');
                
                // Intentar usar toastr si está disponible
                if (typeof toastr !== 'undefined') {
                    toastr.success(mensaje);
                }
                
                setTimeout(function() {
                    window.location.href = '<?= base_url('dashboard/paquete/detalle/' . $paquete->id) ?>';
                }, 2000);
            } else {
                var errorMsg = (response && response.error) ? response.error : 'Error al actualizar los módulos';
                console.error('Error en respuesta:', errorMsg);
                
                // Mostrar mensaje en la página
                mostrarMensaje(errorMsg, 'danger');
                
                // Intentar usar toastr si está disponible
                if (typeof toastr !== 'undefined') {
                    toastr.error(errorMsg);
                } else {
                    alert(errorMsg);
                }
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            console.error('Error AJAX:', {
                status: xhr.status,
                statusText: xhr.statusText,
                responseText: xhr.responseText,
                error: errorThrown
            });
            
            // Actualizar el token CSRF incluso en caso de error
            actualizarTokenCSRF(xhr);
            
            // Intentar parsear la respuesta JSON
            var errorMsg = 'Error al actualizar los módulos';
            try {
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.status === 403) {
                    errorMsg = 'Error de autenticación. Por favor, recarga la página.';
                } else if (xhr.responseText) {
                    errorMsg = 'Error: ' + xhr.responseText.substring(0, 100);
                }
            } catch (e) {
                console.error('Error al parsear respuesta:', e);
            }
            
            // Mostrar mensaje en la página
            mostrarMensaje(errorMsg, 'danger');
            
            // Intentar usar toastr si está disponible
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMsg);
            } else {
                alert(errorMsg);
            }
        }
    });
});
</script>

<?= $this->endSection() ?>

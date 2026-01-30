<?php $this->extend('layout/dashboard') ?>

<?= $this->section("modulo_detalle/lista") ?>

<style>
    .card-modern {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: none;
        overflow: hidden;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: none;
    }
    
    .card-header-modern h3 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border-left: 3px solid #667eea;
    }
    
    .table-modern {
        margin: 0;
    }
    
    .table-modern thead {
        background: #f8f9fa;
    }
    
    .table-modern thead th {
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 15px;
    }
    
    .table-modern tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    .form-select-sm {
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        padding: 8px 12px;
    }
    
    .form-select-sm:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .editar-inline {
        border: 2px solid #e1e8ed;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .editar-inline:hover {
        border-color: #667eea;
        background-color: #f8f9fa;
    }
    
    .editar-inline:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
        outline: none;
    }
    
    .editar-inline:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #f8f9fa;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- HEADER -->
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white; margin: 0;">
                            <i class="fas fa-list-alt me-2"></i> Gestión de Detalles de Módulos
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Administra los detalles y submenús de los módulos
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/modulo-detalle/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nuevo Detalle
                    </a>
                </div>
            </div>

            <!-- CARD -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i> Lista de Detalles de Módulos
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Mensajes -->
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                <?= session()->getFlashdata('errors'); ?>
                            <?php else: ?>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro -->
                    <div class="filter-card">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="modulo_id" class="form-label mb-2">
                                    <i class="fas fa-filter me-2"></i>Filtro por Módulo:
                                </label>
                                <select name="modulo_id" id="modulo_id" class="form-select form-select-sm">
                                    <option value="">Todos los módulos</option>
                                    <?php foreach ($modulos as $m): ?>
                                        <option value="<?= (int)$m['id'] ?>" <?= (int)$selectedModuloId === (int)$m['id'] ? 'selected' : '' ?>>
                                            <?= esc($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-modern" id="tabla-modulo-detalle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th>Descripción</th>
                                    <th>Ruta</th>
                                    <th>Acción</th>
                                    <th>Mostrar</th>
                                    <th>Estado</th>
                                    <th>Orden</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Input hidden para CSRF token -->
<input type="hidden" id="csrf_token_input" value="<?= csrf_hash() ?>">

<!-- Modal de Eliminación -->
<div class="modal fade" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalEliminacionTitle">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    <i class="fas fa-info-circle text-warning me-2"></i>
                    ¿Estás seguro de que deseas eliminar este detalle de módulo? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <form method="POST" action="<?= base_url('dashboard/modulo-detalle/eliminar'); ?>" onsubmit="this.querySelector('button[type=submit]').disabled = true;" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="modulo_id_filter" name="modulo_id_filter" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Eliminar
                    </button>
                </form>
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
            return;
        }
        // O de la respuesta JSON si está disponible
        if (xhr.responseJSON && xhr.responseJSON.csrf_token) {
            var jsonToken = xhr.responseJSON.csrf_token;
            $('#csrf_token_input').val(jsonToken);
            $('meta[name="csrf-token"]').attr('content', jsonToken);
            return;
        }
    }

    const dt = $('#tabla-modulo-detalle').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '<?= base_url('dashboard/modulo-detalle/getModuloDetalle') ?>',
            type: 'GET',
            data: function(d) {
                d.modulo_id = document.getElementById('modulo_id').value || '';
            }
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columns: [{
                data: 'modulo_nombre'
            },
            {
                data: 'descripcion'
            },
            {
                data: 'ruta'
            },
            {
                data: 'accion'
            },
            {
                data: 'mostrar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'estado_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'orden'
            },
            {
                data: 'acciones_html',
                orderable: false,
                searchable: false
            }
        ]
    });

    document.getElementById('modulo_id').addEventListener('change', function() {
        dt.ajax.reload(null, true);
    });

    $('body').on('click', '#btnEliminar', function(e) {
        e.preventDefault();
        $('#id').val(this.value);
        // Capturar el filtro actual del select
        $('#modulo_id_filter').val(document.getElementById('modulo_id').value);
        $('#modalEliminacion').modal('show');
    });

    // Edición inline para "Mostrar" y "Estado"
    $(document).on('change', '.editar-inline', function() {
        var $select = $(this);
        var id = $select.data('id');
        var campo = $select.data('campo');
        var valor = $select.val();
        var valorOriginal = $select.data('valor-original') || $select.find('option:selected').val();
        var csrfToken = obtenerTokenCSRF();
        var csrfTokenName = '<?= csrf_token() ?>';

        // Deshabilitar el select mientras se procesa
        $select.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('dashboard/modulo-detalle/update-campo-inline') ?>',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                [csrfTokenName]: csrfToken,
                'id': id,
                'campo': campo,
                'valor': valor
            },
            success: function(response, textStatus, xhr) {
                // Actualizar el token CSRF después de la respuesta
                actualizarTokenCSRF(xhr);
                
                if (response.success) {
                    // Actualizar el valor original guardado
                    $select.data('valor-original', valor);
                    
                    // Mostrar notificación de éxito (si toastr está disponible)
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Campo actualizado correctamente');
                    }
                } else {
                    // Revertir al valor original
                    $select.val(valorOriginal);
                    
                    // Mostrar error
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.error || 'Error al actualizar el campo');
                    } else {
                        alert(response.error || 'Error al actualizar el campo');
                    }
                }
            },
            error: function(xhr) {
                // Actualizar el token CSRF incluso en caso de error
                actualizarTokenCSRF(xhr);
                
                // Revertir al valor original
                $select.val(valorOriginal);
                
                // Si es un error 403, puede ser un problema de CSRF
                if (xhr.status === 403) {
                    var error = 'Error de autenticación. Por favor, recarga la página.';
                } else {
                    var error = xhr.responseJSON?.error || 'Error al actualizar el campo';
                }
                
                if (typeof toastr !== 'undefined') {
                    toastr.error(error);
                } else {
                    alert(error);
                }
            },
            complete: function() {
                // Rehabilitar el select
                $select.prop('disabled', false);
            }
        });
    });
</script>

<?= $this->endSection() ?>

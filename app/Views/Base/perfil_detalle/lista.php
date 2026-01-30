<?php $this->extend('layout/dashboard') ?>

<?= $this->section("perfil_detalle/lista") ?>

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
    
    /* Estilos para celdas editables */
    .editable-orden {
        background-color: #f8f9fa;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }
    
    .editable-orden:hover {
        background-color: #e9ecef;
    }
    
    .editable-orden::after {
        content: ' ✎';
        color: #6c757d;
        font-size: 0.8em;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .editable-orden:hover::after {
        opacity: 1;
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
                            <i class="fas fa-user-cog me-2"></i> Gestión de Permisos por Perfil
                        </h2>
                        <p style="color: white; margin: 10px 0 0 0; opacity: 0.9;">
                            Administra los permisos y accesos de cada perfil a los módulos
                        </p>
                    </div>
                    <a href="<?= base_url('dashboard/perfil-detalle/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nuevo Permiso
                    </a>
                </div>
            </div>

            <!-- CARD -->
            <div class="card card-modern">
                <div class="card-header card-header-modern">
                    <h3 class="card-title">
                        <i class="fas fa-list me-2"></i> Lista de Permisos por Perfil
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
                                <label for="perfil_id" class="form-label mb-2">
                                    <i class="fas fa-filter me-2"></i>Filtro por Perfil:
                                </label>
                                <select name="perfil_id" id="perfil_id" class="form-select form-select-sm">
                                    <option value="">Todos los perfiles</option>
                                    <?php foreach ($perfiles as $p): ?>
                                        <option value="<?= (int)$p['id'] ?>" <?= (int)$selectedPerfilId === (int)$p['id'] ? 'selected' : '' ?>>
                                            <?= esc($p['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-modern" id="tabla-perfil-detalle" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Perfil</th>
                                    <th>Módulo</th>
                                    <th>Ver</th>
                                    <th>Registrar</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                    <th>Orden</th>
                                    <th>Estado</th>
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
                    ¿Estás seguro de que deseas eliminar este permiso? Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <form method="POST" action="<?= base_url('dashboard/perfil-detalle/eliminar'); ?>" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="perfil_id_filter" name="perfil_id_filter" value="">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Input hidden para CSRF token -->
<input type="hidden" id="csrf_token_input" value="<?= csrf_hash() ?>">

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

    const dt = $('#tabla-perfil-detalle').DataTable({
        serverSide: true,
        processing: true,
        searching: true,
        ordering: true,
        ajax: {
            url: '<?= base_url('dashboard/perfil-detalle/getPerfilDetalle') ?>',
            type: 'GET',
            data: function(d) {
                d.perfil_id = document.getElementById('perfil_id').value || '';
            }
        },
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columns: [{
                data: 'perfil_nombre'
            },
            {
                data: 'modulo_nombre'
            },
            {
                data: 'ver_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'registrar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'editar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'eliminar_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'orden',
                createdCell: function(td, cellData, rowData) {
                    // Agregar atributos para edición inline
                    $(td).attr('data-id', rowData.id);
                    $(td).attr('data-orden', cellData);
                    $(td).addClass('editable-orden');
                    $(td).attr('title', 'Doble clic para editar');
                    $(td).css('cursor', 'pointer');
                }
            },
            {
                data: 'estado_html',
                orderable: false,
                searchable: false
            },
            {
                data: 'acciones_html',
                orderable: false,
                searchable: false
            }
        ]
    });

    // Recargar solo la tabla cuando cambie el perfil
    document.getElementById('perfil_id').addEventListener('change', function() {
        dt.ajax.reload(null, true);
    });

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        $("#id").val(this.value);
        // Capturar el valor actual del filtro de perfil
        $("#perfil_id_filter").val(document.getElementById('perfil_id').value);
        $("#modalEliminacion").modal("show");
    });

    // ============= EDICIÓN INLINE DEL ORDEN =============
    // Variable para controlar si hay una edición en progreso
    let editingCell = null;

    // Doble clic para editar el orden
    $('body').on('dblclick', '.editable-orden', function() {
        // Si ya hay una celda en edición, ignorar
        if (editingCell !== null) {
            return;
        }

        const $cell = $(this);
        const id = $cell.data('id');
        const currentOrden = $cell.data('orden');
        
        // Guardar referencia a la celda en edición
        editingCell = $cell;
        
        // Reemplazar contenido con input
        const $input = $('<input>', {
            type: 'number',
            class: 'form-control form-control-sm',
            value: currentOrden,
            style: 'width: 80px; text-align: center;'
        });
        
        $cell.html($input);
        $input.focus().select();
        
        // Guardar al perder el foco
        $input.on('blur', function() {
            saveOrden($cell, id, $(this).val());
        });
        
        // Guardar al presionar Enter
        $input.on('keypress', function(e) {
            if (e.which === 13) { // Enter
                e.preventDefault();
                saveOrden($cell, id, $(this).val());
            }
        });
        
        // Cancelar con Escape
        $input.on('keydown', function(e) {
            if (e.which === 27) { // Escape
                $cell.text(currentOrden);
                editingCell = null;
            }
        });
    });

    // Función para guardar el nuevo orden
    function saveOrden($cell, id, newOrden) {
        // Si ya no hay celda en edición, salir
        if (editingCell === null) {
            return;
        }

        // Validar que sea un número
        if (newOrden === '' || isNaN(newOrden)) {
            alert('El orden debe ser un número válido');
            $cell.text($cell.data('orden'));
            editingCell = null;
            return;
        }
        
        // Mostrar indicador de carga
        $cell.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
        
        var csrfToken = obtenerTokenCSRF();
        var csrfTokenName = '<?= csrf_token() ?>';
        
        // Enviar AJAX
        $.ajax({
            url: '<?= base_url('dashboard/perfil-detalle/updateOrden') ?>',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: {
                [csrfTokenName]: csrfToken,
                id: id,
                orden: newOrden
            },
            success: function(response, textStatus, xhr) {
                // Actualizar el token CSRF después de la respuesta
                actualizarTokenCSRF(xhr);
                
                if (response.success) {
                    // Actualizar el valor en la celda
                    $cell.text(newOrden);
                    $cell.data('orden', newOrden);
                    
                    // Mostrar mensaje de éxito temporal
                    $cell.css('background-color', '#d4edda');
                    setTimeout(function() {
                        $cell.css('background-color', '');
                    }, 1000);
                } else {
                    alert('Error: ' + (response.message || 'No se pudo actualizar el orden'));
                    $cell.text($cell.data('orden'));
                }
                editingCell = null;
            },
            error: function(xhr, status, error) {
                // Actualizar el token CSRF incluso en caso de error
                actualizarTokenCSRF(xhr);
                
                console.error('Error AJAX:', error);
                
                // Si es un error 403, puede ser un problema de CSRF
                if (xhr.status === 403) {
                    alert('Error de autenticación. Por favor, recarga la página.');
                } else {
                    alert('Error al actualizar el orden. Por favor, intenta de nuevo.');
                }
                $cell.text($cell.data('orden'));
                editingCell = null;
            }
        });
    }
</script>

<?= $this->endSection() ?>

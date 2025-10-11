<?php $this->extend('layout/dashboard') ?>


<?= $this->section("perfil_detalle/lista") ?>
<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Lista de Perfil Detalle</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">
                    <div class="row">
                        <div class="col-lg-4 col-4 ">
                            <label for="perfil_id" class="me-2">Filtro por perfil:</label>
                            <select name="perfil_id" id="perfil_id" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <?php foreach ($perfiles as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>" <?= (int)$selectedPerfilId === (int)$p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <br>
                        </div>
                    </div>

                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                <?= session()->getFlashdata('errors'); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success my-3" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tabla-perfil-detalle">
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
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para celdas editables */
.editable-orden {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease;
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

<div class="modal fade" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminacionTitle">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">¿Estás seguro de que deseas eliminar este perfil detalle? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/perfil-detalle/eliminar'); ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="perfil_id_filter" name="perfil_id_filter" value="">
                    <button class="btn btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" data-original-title="Editar" aria-label="Editar" data-bs-original-title="Editar">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
        
        // Enviar AJAX
        $.ajax({
            url: '<?= base_url('dashboard/perfil-detalle/updateOrden') ?>',
            type: 'POST',
            data: {
                id: id,
                orden: newOrden,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
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
                console.error('Error AJAX:', error);
                alert('Error al actualizar el orden. Por favor, intenta de nuevo.');
                $cell.text($cell.data('orden'));
                editingCell = null;
            }
        });
    }
</script>

<script>
    /*getPerfil();

    function getPerfil() {
        $('.getPerfilDetalle').DataTable().clear().destroy();
        $('.getPerfilDetalle').DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Registros _MENU_ ",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla =(",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix": "",
                "sSearch": "Buscar:",
                "sUrl": "",
                "sInfoThousands": ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
            "ajax": {
                url: 'getPerfilDetalle',
                type: 'GET',
                data: {
                    perfil_id: document.getElementById('perfil_id').value || ''
                }
            }
        });
    }

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value", this.value);
        $("#modalEliminacion").modal("show");
    });*/
</script>

<?= $this->endSection() ?>
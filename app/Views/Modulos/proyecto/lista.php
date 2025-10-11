<?= $this->extend('layout/dashboard') ?>

<?= $this->section('proyecto/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram"></i> Gestión de Proyectos
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/proyecto/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Proyecto
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="tipo_filter">Tipo de Proyecto:</label>
                            <select id="tipo_filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="residencial">Residencial</option>
                                <option value="comercial">Comercial</option>
                                <option value="industrial">Industrial</option>
                                <option value="institucional">Institucional</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="estado_filter">Estado:</label>
                            <select id="estado_filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completado">Completado</option>
                                <option value="en_pausa">En Pausa</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cliente_filter">Cliente:</label>
                            <select id="cliente_filter" class="form-control">
                                <option value="">Todos los clientes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button id="aplicar_filtros" class="btn btn-info">
                                    <i class="fas fa-filter"></i> Aplicar
                                </button>
                                <button id="limpiar_filtros" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered getProyecto nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Ubicación</th>
                                    <th>Presupuesto</th>
                                    <th>Destacado</th>
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
                <p class="modal-text">¿Estás seguro de que deseas eliminar este proyecto? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/proyecto/eliminar'); ?>"> 
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                     <button type="submit"
                        class="btn btn-danger"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Eliminar">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos para mejorar la visualización responsive de la tabla */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Mejorar la visualización de botones en móvil */
@media screen and (max-width: 768px) {
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
        display: inline-block;
        width: auto;
    }
    
    /* Ajustar los filtros en móvil */
    .row.mb-3 .col-md-3 {
        margin-bottom: 1rem;
    }
    
    /* Mejorar la visualización de badges */
    .badge {
        display: inline-block;
        white-space: nowrap;
    }
}

/* Estilos para el control de expansión de DataTables Responsive */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.child,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.child,
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dataTables_empty {
    cursor: default !important;
}

table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child:before,
table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > th:first-child:before {
    top: 50%;
    left: 4px;
    height: 14px;
    width: 14px;
    margin-top: -7px;
    display: block;
    position: absolute;
    color: white;
    border: 2px solid white;
    border-radius: 14px;
    box-shadow: 0 0 3px #444;
    box-sizing: content-box;
    text-align: center;
    text-indent: 0 !important;
    font-family: 'Courier New', Courier, monospace;
    line-height: 14px;
    content: '+';
    background-color: #31b131;
}

table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td:first-child:before,
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th:first-child:before {
    content: '-';
    background-color: #d33333;
}

/* Ajustar el padding de las celdas cuando hay control de expansión */
table.dataTable.dtr-inline.collapsed > tbody > tr > td:first-child,
table.dataTable.dtr-inline.collapsed > tbody > tr > th:first-child {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
}

/* Mejorar espaciado de los detalles expandidos */
table.dataTable.dtr-inline.collapsed > tbody > tr.child ul {
    display: inline-block;
    list-style-type: none;
    margin: 0;
    padding: 0;
}

table.dataTable.dtr-inline.collapsed > tbody > tr.child ul li {
    border-bottom: 1px solid #efefef;
    padding: 0.5em 0;
}

table.dataTable.dtr-inline.collapsed > tbody > tr.child span.dtr-title {
    display: inline-block;
    min-width: 100px;
    font-weight: bold;
}
</style>

<script>
    getProyecto();

    function getProyecto() {
        $('.getProyecto').DataTable().clear().destroy();
        $('.getProyecto').DataTable({
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
                url: 'getProyecto',
                type: 'GET',
                data: function(d) {
                    d.tipo_proyecto = $('#tipo_filter').val();
                    d.estado = $('#estado_filter').val();
                    d.cliente = $('#cliente_filter').val();
                }
            },
            "responsive": true,
            "autoWidth": false,
            "columnDefs": [
                {
                    "targets": 1, // Columna de nombre - MÁXIMA PRIORIDAD
                    "responsivePriority": 1 // Siempre visible
                },
                {
                    "targets": -1, // Columna de acciones
                    "responsivePriority": 2 // Segunda prioridad
                },
                {
                    "targets": 0, // Columna de imagen
                    "responsivePriority": 3 // Tercera prioridad
                }
            ]
        });
    }

    $("body").on("click", "#btnEliminar", function(e) {
        e.preventDefault();
        console.log(this.value);
        $("#id").attr("value", this.value);
        $("#modalEliminacion").modal("show");
    });

    // Aplicar filtros
    $('#aplicar_filtros').click(function() {
        getProyecto();
    });

    // Limpiar filtros
    $('#limpiar_filtros').click(function() {
        $('#tipo_filter').val('');
        $('#estado_filter').val('');
        $('#cliente_filter').val('');
        getProyecto();
    });

    // Cargar clientes en el filtro
    $.get('<?= base_url('dashboard/proyecto/getClientesSelect') ?>', function(data) {
        var select = $('#cliente_filter');
        $.each(data, function(index, cliente) {
            select.append('<option value="' + cliente.nombre + '">' + cliente.nombre + '</option>');
        });
    });

    // Filtrar al cambiar el select de cliente
    $('#cliente_filter').on('change', function() {
        getProyecto();
    });
</script>

<?= $this->endSection() ?>
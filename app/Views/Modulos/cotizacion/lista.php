<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cotizacion/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> Gestión de Cotizaciones
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cotizacion/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Cotización
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Mensajes de éxito y error -->
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

                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="estado_filter">Estado:</label>
                            <select id="estado_filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="borrador">Borrador</option>
                                <option value="enviada">Enviada</option>
                                <option value="revisada">Revisada</option>
                                <option value="aprobada">Aprobada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="expirada">Expirada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cliente_filter">Cliente:</label>
                            <select id="cliente_filter" class="form-control">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="busqueda_filter">Búsqueda:</label>
                            <input type="text" id="busqueda_filter" class="form-control" placeholder="Número, título, cliente...">
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div>
                                <button id="aplicar_filtros" class="btn btn-info">
                                    <i class="fas fa-filter"></i> Aplicar Filtros
                                </button>
                                <button id="limpiar_filtros" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla con contenedor responsive -->
                    <div class="table-responsive">
                        <table id="tablaCotizaciones" class="table table-bordered table-striped nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Título</th>
                                    <th>Cliente</th>
                                    <th>Estado</th>
                                    <th>Prioridad</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
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

<?= $this->include('components/modals') ?>

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

<script src="<?= base_url('lib/js/modals.js') ?>"></script>
<script>
$(document).ready(function() {
    var table = $('#tablaCotizaciones').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/cotizacion/getCotizaciones') ?>",
            "type": "GET",
            "data": function(d) {
                d.estado = $('#estado_filter').val();
                d.cliente_id = $('#cliente_filter').val();
                d.busqueda = $('#busqueda_filter').val();
            }
        },
        "columns": [
            { "data": 0 },
            { "data": 1 },
            { "data": 2 },
            { "data": 3 },
            { "data": 4 },
            { "data": 5 },
            { "data": 6 },
            { "data": 7, "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[6, 'desc']], // Ordenar por fecha descendente
        "columnDefs": [
            {
                "targets": 1, // Columna de título - MÁXIMA PRIORIDAD
                "responsivePriority": 1 // Siempre visible
            },
            {
                "targets": 0, // Columna de número
                "responsivePriority": 2 // Segunda prioridad
            },
            {
                "targets": -1, // Columna de acciones
                "responsivePriority": 3 // Tercera prioridad
            }
        ]
    });

    // Cargar clientes en el filtro
    $.get('<?= base_url('dashboard/cotizacion/getClientesSelect') ?>', function(data) {
        var select = $('#cliente_filter');
        $.each(data, function(index, cliente) {
            select.append('<option value="' + cliente.id + '">' + cliente.text + '</option>');
        });
    });

    // Aplicar filtros
    $('#aplicar_filtros').click(function() {
        table.ajax.reload();
    });

    // Limpiar filtros
    $('#limpiar_filtros').click(function() {
        $('#estado_filter').val('');
        $('#cliente_filter').val('');
        $('#busqueda_filter').val('');
        table.ajax.reload();
    });

    // Búsqueda en tiempo real
    $('#busqueda_filter').on('keyup', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 500);
    });

    // Funciones globales para los botones
    window.editarCotizacion = function(id) {
        window.location.href = '<?= base_url('dashboard/cotizacion/editar') ?>/' + id;
    };

    window.verDetalleCotizacion = function(id) {
        window.location.href = '<?= base_url('dashboard/cotizacion/detalle') ?>/' + id;
    };

    window.generarPDF = function(id) {
        window.location.href = '<?= base_url('dashboard/cotizacion/generarPDF') ?>/' + id;
    };

    window.eliminarCotizacion = function(id) {
        eliminarConConfirmacion(
            '<?= base_url('dashboard/cotizacion/eliminar') ?>/' + id,
            '¿Estás seguro de que deseas eliminar esta cotización?'
        );
    };

    // Mostrar alertas flash de PHP
    <?php if (session()->getFlashdata('success')): ?>
        mostrarModalExito('<?= addslashes(session()->getFlashdata('success')) ?>');
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        mostrarModalError('<?= addslashes(session()->getFlashdata('error')) ?>');
    <?php endif; ?>
});
</script>

<?= $this->endSection() ?>

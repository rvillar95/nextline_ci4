<?= $this->extend('layout/dashboard') ?>

<?= $this->section('cliente/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Gestión de Clientes
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/cliente/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Cliente
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="estado_filter">Estado:</label>
                            <select id="estado_filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="A">Activos</option>
                                <option value="I">Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tipo_cliente_filter">Tipo de Cliente:</label>
                            <select id="tipo_cliente_filter" class="form-control">
                                <option value="">Todos</option>
                                <option value="particular">Particular</option>
                                <option value="empresa">Empresa</option>
                                <option value="organizacion">Organización</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="busqueda_filter">Búsqueda:</label>
                            <input type="text" id="busqueda_filter" class="form-control" placeholder="Nombre, RUT, email...">
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

                    <!-- Tabla -->
                    <table id="tablaClientes" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nombre/Razón Social</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Contacto</th>
                                <th>Dirección</th>
                                <th>Comuna</th>
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

<?= $this->include('components/modals') ?>

<script src="<?= base_url('lib/js/modals.js') ?>"></script>
<script>
$(document).ready(function() {
    var table = $('#tablaClientes').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/cliente/getClientes') ?>",
            "type": "GET",
            "data": function(d) {
                d.tipo_cliente = $('#tipo_cliente_filter').val();
                d.estado = $('#estado_filter').val();
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
            { "data": 6, "orderable": false }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "pageLength": 25
    });

    // Aplicar filtros
    $('#aplicar_filtros').click(function() {
        table.ajax.reload();
    });

    // Limpiar filtros
    $('#limpiar_filtros').click(function() {
        $('#estado_filter').val('');
        $('#tipo_cliente_filter').val('');
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
    window.editarCliente = function(id) {
        window.location.href = '<?= base_url('dashboard/cliente/editar') ?>/' + id;
    };

    window.verDetalleCliente = function(id) {
        window.location.href = '<?= base_url('dashboard/cliente/detalle') ?>/' + id;
    };

    // Función para activar cliente
    window.activarCliente = function(id) {
        // Guardar el ID para usar en el modal
        window.clienteIdAActivar = id;
        
        // Mostrar modal de confirmación
        $('#modalConfirmarActivacion').modal('show');
    };
    
    // Función para confirmar activación desde el modal
    window.confirmarActivacionCliente = function() {
        var id = window.clienteIdAActivar;
        
        // Crear un formulario temporal para enviar la petición POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('dashboard/cliente/activar') ?>';
        
        // Agregar token CSRF
        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '<?= csrf_token() ?>';
        csrfToken.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfToken);
        
        // Agregar ID del cliente
        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    };
    
    // Función para eliminar cliente
    window.eliminarCliente = function(id) {
        window.clienteIdAEliminar = id;
        $('#modalConfirmarEliminacion').modal('show');
    };

    // Función para confirmar eliminación desde el modal
    window.confirmarEliminacionCliente = function() {
        var id = window.clienteIdAEliminar;
        
        // Crear un formulario temporal para enviar la petición POST
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('dashboard/cliente/eliminar') ?>/' + id;
        
        // Agregar token CSRF
        var csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '<?= csrf_token() ?>';
        csrfToken.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfToken);
        
        // Agregar ID del cliente
        var idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    };

    // Funciones para mostrar modales de mensajes
    window.mostrarModalExito = function(mensaje) {
        $('#modalMensajeExito .modal-body p').text(mensaje);
        $('#modalMensajeExito').modal('show');
    };
    
    window.mostrarModalError = function(mensaje) {
        $('#modalMensajeError .modal-body p').text(mensaje);
        $('#modalMensajeError').modal('show');
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

<!-- Modal de confirmación para activar cliente -->
<div class="modal fade" id="modalConfirmarActivacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarActivacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarActivacionLabel">
                    <i class="fas fa-check-circle text-success"></i> Confirmar Activación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas activar este cliente?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle"></i> 
                    El cliente volverá a estar disponible para crear cotizaciones y otras operaciones.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="confirmarActivacionCliente()">
                    <i class="fas fa-check"></i> Activar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar cliente -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" role="dialog" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">
                    <i class="fas fa-exclamation-triangle text-warning"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este cliente?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle"></i> 
                    El cliente será marcado como inactivo y no se podrá eliminar si tiene cotizaciones asociadas.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacionCliente()">
                    <i class="fas fa-trash"></i> Eliminar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mensajes de éxito -->
<div class="modal fade" id="modalMensajeExito" tabindex="-1" role="dialog" aria-labelledby="modalMensajeExitoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalMensajeExitoLabel">
                    <i class="fas fa-check-circle"></i> Éxito
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">
                    <i class="fas fa-check"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mensajes de error -->
<div class="modal fade" id="modalMensajeError" tabindex="-1" role="dialog" aria-labelledby="modalMensajeErrorLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalMensajeErrorLabel">
                    <i class="fas fa-exclamation-circle"></i> Error
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="fas fa-times"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

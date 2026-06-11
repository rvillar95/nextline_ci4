<?= $this->extend('layout/dashboard') ?>

<?= $this->section('paciente/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-user-injured me-2"></i> Gestión de Pacientes</h2>
                        <p style="color: white;">Administre los pacientes y su información clínica.</p>
                    </div>
                    <a href="<?= base_url('dashboard/paciente/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nuevo Paciente
                    </a>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #667eea;">
                <div class="section-title">
                    <span class="step-number" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">1</span>
                    <span><i class="fas fa-list icon-label"></i> Listado de Pacientes</span>
                </div>
                <p class="section-subtitle">
                    Aquí puede ver todos los pacientes registrados en el sistema.
                </p>

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
                        <label for="tipo_paciente_filter">Tipo:</label>
                        <select id="tipo_paciente_filter" class="form-control">
                            <option value="">Todos</option>
                            <option value="particular">Particular</option>
                            <option value="convenio">Convenio</option>
                            <option value="seguro">Seguro</option>
                            <option value="fonasa">Fonasa</option>
                            <option value="isapre">Isapre</option>
                            <option value="otro">Otro</option>
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

                <div class="table-responsive">
                    <table id="tablaPacientes" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>RUT/DNI</th>
                                <th>Tipo</th>
                                <th>Contacto</th>
                                <th>IMC</th>
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

<?= $this->include('components/modals') ?>

<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" aria-labelledby="modalConfirmarEliminacionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminacionLabel">Confirmar desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Desea desactivar este paciente?</p>
                <p class="text-muted small mb-0">
                    No se borrará de la base de datos. Quedará como inactivo y dejará de aparecer en el listado habitual.
                    Podrá reactivarlo filtrando por estado «Inactivos».
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarEliminacionPaciente()">Desactivar</button>
            </div>
        </div>
    </div>
</div>

<style>
.section-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid #667eea;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
    font-size: 1.3rem;
    font-weight: 600;
    color: #2c3e50;
}

.step-number {
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1rem;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.section-subtitle {
    color: #7f8c8d;
    font-size: 0.95rem;
    margin-bottom: 20px;
}
</style>

<script>
$(document).ready(function() {
    var table = $('#tablaPacientes').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/paciente/getPacientes') ?>",
            "type": "GET",
            "data": function(d) {
                d.tipo_paciente = $('#tipo_paciente_filter').val();
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
        "autoWidth": false,
        "pageLength": 25
    });

    $('#aplicar_filtros').click(function() {
        table.ajax.reload();
    });

    $('#limpiar_filtros').click(function() {
        $('#estado_filter').val('');
        $('#tipo_paciente_filter').val('');
        $('#busqueda_filter').val('');
        table.ajax.reload();
    });

    $('#busqueda_filter').on('keyup', function() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 500);
    });

    window.editarPaciente = function(id) {
        window.location.href = '<?= base_url('dashboard/paciente/editar') ?>/' + id;
    };

    window.verDetallePaciente = function(id) {
        window.location.href = '<?= base_url('dashboard/paciente/detalle') ?>/' + id;
    };

    window.eliminarPaciente = function(id) {
        window.pacienteIdAEliminar = id;
        $('#modalConfirmarEliminacion').modal('show');
    };

    window.confirmarEliminacionPaciente = function() {
        if (!window.pacienteIdAEliminar) {
            return;
        }

        $.ajax({
            url: '<?= base_url('dashboard/paciente/eliminar') ?>/' + window.pacienteIdAEliminar,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Paciente desactivado con éxito');
                    }
                    $('#modalConfirmarEliminacion').modal('hide');
                    table.ajax.reload(null, false);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(response.error || 'Error al desactivar el paciente');
                } else {
                    alert(response.error || 'Error al desactivar el paciente');
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.error || 'Error al desactivar el paciente';
                if (typeof toastr !== 'undefined') {
                    toastr.error(error);
                } else {
                    alert(error);
                }
            }
        });
    };

    window.activarPaciente = function(id) {
        $.ajax({
            url: '<?= base_url('dashboard/paciente/activar') ?>/' + id,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Paciente activado con éxito');
                    }
                    table.ajax.reload(null, false);
                } else if (typeof toastr !== 'undefined') {
                    toastr.error(response.error || 'Error al activar el paciente');
                } else {
                    alert(response.error || 'Error al activar el paciente');
                }
            },
            error: function(xhr) {
                var error = xhr.responseJSON?.error || 'Error al activar el paciente';
                if (typeof toastr !== 'undefined') {
                    toastr.error(error);
                } else {
                    alert(error);
                }
            }
        });
    };
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/lista') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-calendar-check me-2"></i> Lista de Citas</h2>
                        <p style="color: white;">Gestione las citas agendadas de sus pacientes</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light me-2">
                            <i class="fas fa-calendar-alt me-2"></i> Vista Calendario
                        </a>
                        <button class="btn btn-light me-2" onclick="crearHorarios()" title="Crear horarios disponibles">
                            <i class="fas fa-clock me-2"></i> Crear Horarios
                        </button>
                        <button class="btn btn-danger" onclick="eliminarHorarios()" title="Eliminar horarios disponibles sin citas" style="background-color: #dc3545; border-color: #dc3545; box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4); font-weight: 600;">
                            <i class="fas fa-trash-alt me-2"></i> Eliminar Horarios
                        </button>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #f5576c;">
                <div class="table-responsive">
                    <table id="tablaAgenda" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Estado</th>
                                <th>Motivo</th>
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

<script>
// Configurar toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(document).ready(function() {
    var table = $('#tablaAgenda').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/agenda/getAgenda') ?>",
            "type": "GET"
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
});

function crearHorarios() {
    // Redirigir al calendario y abrir el modal de crear horarios
    sessionStorage.setItem('abrirModalCrearHorarios', 'true');
    window.location.href = '<?= base_url('dashboard/agenda/calendario') ?>';
}

function eliminarHorarios() {
    if (!confirm('¿Está seguro de que desea eliminar todos los horarios disponibles?\n\nEsto eliminará solo los horarios que NO tienen citas agendadas.')) {
        return;
    }
    
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    
    // Mostrar notificación de procesamiento
    if (typeof toastr !== 'undefined') {
        toastr.info('Eliminando horarios disponibles...', 'Procesando', {
            timeOut: 2000,
            progressBar: true
        });
    }
    
    $.ajax({
        url: '<?= base_url('dashboard/agenda/eliminarHorarios') ?>',
        type: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        data: {
            [csrfName]: csrfToken,
            solo_disponibles: true
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var mensaje = 'Se eliminaron ' + (response.horarios_eliminados || 0) + ' horarios disponibles exitosamente.';
                if (typeof toastr !== 'undefined') {
                    toastr.success(mensaje, 'Éxito', {
                        timeOut: 4000,
                        progressBar: true
                    });
                } else {
                    alert(mensaje);
                }
                // Recargar la tabla
                $('#tablaAgenda').DataTable().ajax.reload();
            } else {
                var mensaje = response.message || 'Error al eliminar horarios';
                if (typeof toastr !== 'undefined') {
                    toastr.error(mensaje, 'Error', {
                        timeOut: 5000,
                        progressBar: true
                    });
                } else {
                    alert(mensaje);
                }
            }
        },
        error: function(xhr) {
            var errorMsg = 'Error al eliminar horarios';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            if (typeof toastr !== 'undefined') {
                toastr.error(errorMsg, 'Error', {
                    timeOut: 5000,
                    progressBar: true
                });
            } else {
                alert(errorMsg);
            }
        }
    });
}
</script>

<?= $this->endSection() ?>

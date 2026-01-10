<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/gestionar') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-calendar-plus me-2"></i> Crear Agenda</h2>
                        <p style="color: white;">Gestione las agendas creadas y sus horarios disponibles</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-light me-2">
                            <i class="fas fa-calendar-alt me-2"></i> Vista Calendario
                        </a>
                        <a href="<?= base_url('dashboard/agenda/lista') ?>" class="btn btn-light me-2">
                            <i class="fas fa-list me-2"></i> Lista de Citas
                        </a>
                        <button class="btn btn-light" onclick="crearHorarios()" title="Crear horarios disponibles">
                            <i class="fas fa-clock me-2"></i> Crear Horarios
                        </button>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #667eea;">
                <div class="section-title">
                    <span class="step-number" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">1</span>
                    <span><i class="fas fa-table icon-label"></i> Agendas Creadas</span>
                </div>
                <p class="section-subtitle">
                    Aquí puede ver todos los <strong>días de agenda creados</strong>. Cada registro representa un día completo con su horario laboral. Haga clic en "Ver Calendario" para ver todos los horarios disponibles de ese día.
                </p>
                <div class="table-responsive">
                    <table id="tablaAgendas" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Almuerzo</th>
                                <th>Disponibles</th>
                                <th>Ocupados</th>
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
    var table = $('#tablaAgendas').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/agenda/getAgendas') ?>",
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
        "pageLength": 25,
        "order": [[0, "desc"]] // Ordenar por fecha descendente
    });
});

function crearHorarios() {
    // Redirigir al calendario y abrir el modal de crear horarios
    sessionStorage.setItem('abrirModalCrearHorarios', 'true');
    window.location.href = '<?= base_url('dashboard/agenda/calendario') ?>';
}
</script>

<?= $this->endSection() ?>

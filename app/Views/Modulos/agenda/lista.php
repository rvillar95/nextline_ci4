<?= $this->extend('layout/dashboard') ?>

<?= $this->section('agenda/lista') ?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<style>
/* Botones del header Lista de Citas: integrados al header, sin bloque blanco */
.main-header .agenda-header-actions .btn-agenda-ghost {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.85);
    color: #fff;
    font-weight: 500;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 8px;
    transition: background 0.2s, border-color 0.2s;
}
.main-header .agenda-header-actions .btn-agenda-ghost:hover {
    background: rgba(255,255,255,0.12);
    border-color: #fff;
    color: #fff;
}
.main-header .agenda-header-actions .btn-agenda-primary {
    background: rgba(255,255,255,0.22);
    border: 1px solid rgba(255,255,255,0.9);
    color: #fff;
    font-weight: 600;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    border-radius: 8px;
    transition: background 0.2s, border-color 0.2s;
}
.main-header .agenda-header-actions .btn-agenda-primary:hover {
    background: rgba(255,255,255,0.35);
    border-color: #fff;
    color: #fff;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-calendar-check me-2"></i> Lista de Citas</h2>
                        <p style="color: white;">Gestione las citas agendadas de sus pacientes</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 agenda-header-actions">
                        <a href="<?= base_url('dashboard/agenda/calendario') ?>" class="btn btn-agenda-ghost" title="Ver agenda en calendario">
                            <i class="fas fa-calendar-alt me-2"></i> Vista Calendario
                        </a>
                        <button type="button" class="btn btn-agenda-primary" onclick="crearHorarios()" title="Crear horarios disponibles">
                            <i class="fas fa-clock me-2"></i> Crear Horarios
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

<!-- Modal Cancelar Cita -->
<div class="modal fade" id="modalCancelarCita" tabindex="-1" aria-labelledby="modalCancelarCitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="modalCancelarCitaLabel"><i class="fas fa-calendar-times me-2 text-danger"></i>Cancelar cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">¿Cancelar esta cita? El horario quedará libre para agendar a otro paciente.</p>
                <label for="motivoCancelarCita" class="form-label small text-muted">Motivo de cancelación (opcional)</label>
                <textarea class="form-control" id="motivoCancelarCita" rows="2" placeholder="Ej.: Paciente reprogramó"></textarea>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarCancelarCita">
                    <i class="fas fa-times me-1"></i> Sí, cancelar cita
                </button>
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

function confirmarCita(id) {
    if (!id) return;
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    $.ajax({
        url: '<?= base_url('dashboard/agenda/confirmarCita') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: { [csrfName]: csrfToken, id: id },
        dataType: 'json',
        success: function(response) {
            if (response && response.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                $('input[name="' + csrfName + '"]').val(response.csrf_token);
            }
            if (response && response.error) {
                toastr.error(response.message || response.error || 'Error al confirmar', 'Error', { timeOut: 5000 });
                $('#tablaAgenda').DataTable().ajax.reload();
                return;
            }
            toastr.success(response && response.message ? response.message : 'Cita confirmada', 'Éxito', { timeOut: 4000 });
            $('#tablaAgenda').DataTable().ajax.reload();
        },
        error: function(xhr) {
            var r = (xhr && xhr.responseJSON) || {};
            if (r.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', r.csrf_token);
                $('input[name="csrf_test_name"]').val(r.csrf_token);
            }
            toastr.error(r.message || r.error || 'Error al confirmar la cita', 'Error', { timeOut: 5000 });
            $('#tablaAgenda').DataTable().ajax.reload();
        }
    });
}

var citaIdACancelar = null;

function cancelarCita(id) {
    if (!id) return;
    citaIdACancelar = id;
    $('#motivoCancelarCita').val('');
    var modal = new bootstrap.Modal(document.getElementById('modalCancelarCita'));
    modal.show();
}

$('#btnConfirmarCancelarCita').on('click', function() {
    if (!citaIdACancelar) return;
    var motivo = $('#motivoCancelarCita').val().trim();
    var id = citaIdACancelar;
    citaIdACancelar = null;
    bootstrap.Modal.getInstance(document.getElementById('modalCancelarCita')).hide();
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || '<?= csrf_hash() ?>';
    var csrfName = 'csrf_test_name';
    var data = { [csrfName]: csrfToken, id: id };
    if (motivo !== '') data.motivo = motivo;
    var $btn = $('#btnConfirmarCancelarCita');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Cancelando...');
    $.ajax({
        url: '<?= base_url('dashboard/agenda/cancelarCita') ?>',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        data: data,
        dataType: 'json',
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-times me-1"></i> Sí, cancelar cita');
            if (response && response.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', response.csrf_token);
                $('input[name="' + csrfName + '"]').val(response.csrf_token);
            }
            if (response && (response.error || !response.success)) {
                toastr.error(response.message || response.error || 'Error al cancelar', 'Error', { timeOut: 5000 });
                return;
            }
            toastr.success(response && response.message ? response.message : 'Cita cancelada y horario liberado', 'Éxito', { timeOut: 4000 });
            $('#tablaAgenda').DataTable().ajax.reload();
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-times me-1"></i> Sí, cancelar cita');
            var r = (xhr && xhr.responseJSON) || {};
            if (r.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', r.csrf_token);
                $('input[name="csrf_test_name"]').val(r.csrf_token);
            }
            toastr.error(r.message || r.error || 'Error al cancelar la cita', 'Error', { timeOut: 5000 });
            $('#tablaAgenda').DataTable().ajax.reload();
        }
    });
});

function verCita(id) {
    if (!id) return;
    window.location.href = '<?= base_url('dashboard/agenda/consulta?id=') ?>' + id;
}
</script>

<?= $this->endSection() ?>

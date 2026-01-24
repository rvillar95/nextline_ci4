<?= $this->extend('layout/dashboard') ?>

<?= $this->section('pago/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-credit-card me-2"></i> Gestión de Pagos</h2>
                        <p style="color: white;">Administre los pagos y suscripciones del sistema</p>
                    </div>
                    <a href="<?= base_url('dashboard/pago/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Registrar Pago
                    </a>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #30cfd0;">
                <div class="table-responsive">
                    <table id="tablaPagos" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha Pago</th>
                                <th>Referencia</th>
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
$(document).ready(function() {
    var table = $('#tablaPagos').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/pago/getPagos') ?>",
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
        "order": [[4, "desc"]]
    });
});

// Función para ver detalles de un pago
function verPago(pagoId) {
    if (!pagoId) {
        alert('Error: ID de pago no válido');
        return;
    }
    
    // Redirigir a la vista de detalles del pago
    // Si el pago tiene detalle_agenda_id, mostrar en botones de pago
    // Si no, mostrar en el módulo de pagos
    window.location.href = '<?= base_url('dashboard/pago/editar/') ?>' + pagoId;
}

// Función para procesar un pago pendiente
function procesarPago(pagoId) {
    if (!pagoId) {
        alert('Error: ID de pago no válido');
        return;
    }
    
    if (!confirm('¿Está seguro de que desea procesar este pago?')) {
        return;
    }
    
    var referencia = prompt('Ingrese la referencia del pago (opcional):', '');
    
    $.ajax({
        url: '<?= base_url('dashboard/pago/procesar') ?>',
        type: 'POST',
        data: {
            id: pagoId,
            referencia: referencia || '',
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Pago procesado exitosamente');
                $('#tablaPagos').DataTable().ajax.reload();
            } else {
                alert('Error: ' + (response.error || 'No se pudo procesar el pago'));
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr);
            alert('Error al procesar el pago. Por favor, intente nuevamente.');
        }
    });
}
</script>

<?= $this->endSection() ?>

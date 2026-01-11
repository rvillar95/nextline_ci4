<?= $this->extend('layout/dashboard') ?>

<?= $this->section('historial/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-history me-2"></i> Historial Clínico</h2>
                        <p style="color: white;">Registro de consultas y evolución de pacientes</p>
                    </div>
                    <div>
                        <a href="<?= base_url('dashboard/historial/comparar') ?>" class="btn btn-light me-2">
                            <i class="fas fa-chart-line me-2"></i> Comparar Historiales
                        </a>
                        <a href="<?= base_url('dashboard/historial/registro') ?>" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i> Nueva Consulta
                        </a>
                    </div>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #fa709a;">
                <div class="table-responsive">
                    <table id="tablaHistorial" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tipo</th>
                                <th>Medidas</th>
                                <th>Motivo</th>
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

<script>
$(document).ready(function() {
    var table = $('#tablaHistorial').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/historial/getHistorial') ?>",
            "type": "GET"
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
        "pageLength": 25,
        "order": [[1, "desc"]]
    });
});
</script>

<?= $this->endSection() ?>

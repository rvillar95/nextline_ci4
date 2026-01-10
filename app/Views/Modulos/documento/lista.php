<?= $this->extend('layout/dashboard') ?>

<?= $this->section('documento/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="main-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 style="color: white;"><i class="fas fa-file-medical me-2"></i> Gestión de Documentos</h2>
                        <p style="color: white;">Administre documentos, pautas nutricionales y recetas</p>
                    </div>
                    <a href="<?= base_url('dashboard/documento/registro') ?>" class="btn btn-light">
                        <i class="fas fa-plus me-2"></i> Nuevo Documento
                    </a>
                </div>
            </div>

            <div class="section-card" style="border-left-color: #4facfe;">
                <div class="table-responsive">
                    <table id="tablaDocumentos" class="table table-bordered table-striped nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Paciente</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Enviado</th>
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
    var table = $('#tablaDocumentos').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?= base_url('dashboard/documento/getDocumentos') ?>",
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
</script>

<?= $this->endSection() ?>

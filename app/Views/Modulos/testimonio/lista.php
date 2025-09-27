<?php $this->extend('layout/dashboard') ?>

<?= $this->section('content') ?>
<div class="row layout-top-spacing">
    <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
        <div class="widget-content widget-content-area br-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Lista de Testimonios</h4>
                <a href="<?= base_url('dashboard/testimonio/registro') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Testimonio
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table id="testimonios-table" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Empresa</th>
                            <th>Testimonio</th>
                            <th>Calificación</th>
                            <th>Proyecto</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>Destacado</th>
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

<script>
$(document).ready(function() {
    var table = $('#testimonios-table').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url('dashboard/testimonio/getTestimonios') ?>",
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
            { "data": 7 },
            { "data": 8, "orderable": false }
        ],
        "order": [[0, "desc"]],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        }
    });

    // Función para editar testimonio
    window.editarTestimonio = function(id) {
        window.location.href = '<?= base_url('dashboard/testimonio/editar') ?>/' + id;
    };

    // Función para eliminar testimonio
    window.eliminarTestimonio = function(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este testimonio?')) {
            window.location.href = '<?= base_url('dashboard/testimonio/eliminar') ?>/' + id;
        }
    };
});
</script>
<?= $this->endSection() ?>

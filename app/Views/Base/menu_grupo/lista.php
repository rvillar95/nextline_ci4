<?= $this->extend('layout/dashboard') ?>

<?= $this->section('menu_grupo/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fas fa-layer-group me-2"></i> Secciones del menú lateral</h3>
                    <a href="<?= base_url('dashboard/menu-grupo/registro') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva sección
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= esc(session()->getFlashdata('success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= esc(session()->getFlashdata('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <p class="text-muted">Define los títulos de grupo del sidebar (Día a día, Pacientes, Cobros, etc.). Los módulos se asignan desde <a href="<?= base_url('dashboard/modulo/lista') ?>">Módulos</a>.</p>

                    <table id="tablaMenuGrupos" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Etiqueta</th>
                                <th>Slug</th>
                                <th>Orden</th>
                                <th>Estado</th>
                                <th>Módulos</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="formEliminarGrupo" method="post" action="<?= base_url('dashboard/menu-grupo/eliminar') ?>" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="eliminarGrupoId" value="">
</form>

<script>
jQuery(function($) {
    var table = $('#tablaMenuGrupos').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('dashboard/menu-grupo/getMenuGrupos') ?>',
        order: [[2, 'asc']],
        columnDefs: [{ orderable: false, targets: [4, 5] }]
    });

    $(document).on('click', '.btn-eliminar-grupo', function() {
        if (!confirm('¿Eliminar esta sección? Los módulos quedarán sin sección asignada.')) return;
        $('#eliminarGrupoId').val($(this).val());
        $('#formEliminarGrupo').submit();
    });
});
</script>

<?= $this->endSection() ?>

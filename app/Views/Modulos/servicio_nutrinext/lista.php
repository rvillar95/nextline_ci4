<?= $this->extend('layout/dashboard') ?>

<?= $this->section('servicio-nutrinext/lista') ?>

<div class="container-fluid">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="card-title mb-0"><i class="fas fa-layer-group text-success"></i> Catalogo NutriNext</h3>
            <a href="<?= base_url('dashboard/servicio-nutrinext/registro') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo servicio
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Funcionalidades de la plataforma agrupadas por area.</p>
            <?php if (empty($por_categoria)): ?>
                <p class="text-center text-muted py-4">No hay servicios activos. Ejecuta el SQL o crea el primero.</p>
            <?php else: ?>
                <?php foreach ($categorias as $catKey => $catLabel): ?>
                    <?php if (empty($por_categoria[$catKey])) continue; ?>
                    <h5 class="text-uppercase text-muted small fw-bold mb-3"><?= esc($catLabel) ?></h5>
                    <div class="row g-3 mb-4">
                        <?php foreach ($por_categoria[$catKey] as $s): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                 style="width:48px;height:48px;background:<?= esc($s->color ?: '#7bc143') ?>22;">
                                                <i class="<?= esc($s->icono ?: 'fas fa-circle') ?>" style="color:<?= esc($s->color ?: '#7bc143') ?>;"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold"><?= esc($s->nombre) ?></h6>
                                                <p class="small text-muted"><?= esc($s->descripcion_corta) ?></p>
                                                <a href="<?= base_url('dashboard/servicio-nutrinext/editar/' . $s->id) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                <?php if ($s->ruta_dashboard): ?>
                                                    <a href="<?= base_url(ltrim($s->ruta_dashboard, '/')) ?>" class="btn btn-sm btn-outline-success" target="_blank">Ir</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title mb-0">Administracion</h3></div>
        <div class="card-body">
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <select id="filtro_categoria" class="form-select">
                        <option value="">Todas las categorias</option>
                        <?php foreach ($categorias as $k => $l): ?>
                            <option value="<?= esc($k) ?>"><?= esc($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select id="filtro_estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="A">Activo</option>
                        <option value="I">Inactivo</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" id="filtro_busqueda" class="form-control" placeholder="Buscar...">
                </div>
                <div class="col-md-2">
                    <button type="button" id="btn_filtrar" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
            <table id="tablaServiciosNutrinext" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoria</th>
                        <th>Resumen</th>
                        <th>Etiquetas</th>
                        <th>Estado</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarSN" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Eliminar</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">¿Eliminar este servicio?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="post" action="<?= base_url('dashboard/servicio-nutrinext/eliminar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="eliminar_sn_id" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabla = $('#tablaServiciosNutrinext').DataTable({
        ajax: {
            url: '<?= base_url('dashboard/servicio-nutrinext/getServicios') ?>',
            data: function(d) {
                d.categoria = $('#filtro_categoria').val();
                d.estado = $('#filtro_estado').val();
                d.busqueda = $('#filtro_busqueda').val();
            }
        },
        order: [[5, 'asc']],
        pageLength: 25
    });
    $('#btn_filtrar').on('click', function() { tabla.ajax.reload(); });
    $(document).on('click', '.btn-eliminar-sn', function() {
        $('#eliminar_sn_id').val($(this).data('id'));
        new bootstrap.Modal(document.getElementById('modalEliminarSN')).show();
    });
});
</script>

<?= $this->endSection() ?>

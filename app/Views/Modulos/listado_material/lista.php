<?= $this->extend('layout/dashboard') ?>

<?= $this->section('listado_material/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Listado de Materiales
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/listado-material/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Listado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Mensajes Flash -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= esc(session()->getFlashdata('success')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= esc(session()->getFlashdata('error')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filtro por Estado -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="filtroEstado">Filtrar por Estado:</label>
                            <select id="filtroEstado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="borrador">Borrador</option>
                                <option value="finalizado">Finalizado</option>
                                <option value="enviado">Enviado</option>
                                <option value="archivado">Archivado</option>
                            </select>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="tablaListadoMateriales" class="table table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Título</th>
                                    <th>Cliente</th>
                                    <th>Proyecto</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminarTitle">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <p class="modal-text">¿Estás seguro de que deseas eliminar este listado de materiales? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/listado-material/eliminar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="eliminarId" name="id" value="">
                    <button type="submit" class="btn btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // DataTable
    let table;

    document.addEventListener('DOMContentLoaded', function() {
        table = $('#tablaListadoMateriales').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '<?= base_url('dashboard/listado-material/getListadosMateriales') ?>',
                type: 'GET',
                data: function(d) {
                    d.estado = $('#filtroEstado').val();
                }
            },
            columns: [
                { data: 0 }, // Número
                { data: 1 }, // Título
                { data: 2 }, // Cliente
                { data: 3 }, // Proyecto
                { data: 4 }, // Fecha
                { data: 5 }, // Estado
                { data: 6, orderable: false, searchable: false } // Acciones
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            pageLength: 10,
            order: [[4, 'desc']]
        });

        // Filtro por estado
        $('#filtroEstado').on('change', function() {
            table.ajax.reload();
        });
    });

    function eliminarListado(id) {
        $('#eliminarId').val(id);
        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
        modal.show();
    }
</script>

<?= $this->endSection() ?>

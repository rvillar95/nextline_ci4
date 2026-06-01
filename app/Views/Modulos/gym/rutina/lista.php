<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/rutina/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Rutinas
                    </h3>
                    <div class="card-tools">
                        <a href="<?= base_url('dashboard/gym/rutina/registro') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Rutina
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                    <li><?= session()->getFlashdata('errors') ?></li>
                                <?php else : ?>
                                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success') !== null) : ?>
                        <div class="alert alert-success" role="alert">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered getRutinas">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Creada por</th>
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

<div class="modal fade" id="modalEliminacion" tabindex="-1" aria-labelledby="modalEliminacionTitle" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEliminacionTitle">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="modal-text">¿Eliminar esta rutina? Se perderá su detalle de ejercicios.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light-dark _effect--ripple waves-effect waves-light" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" action="<?= base_url('dashboard/gym/rutina/eliminar'); ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" id="id" name="id" value="">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    getRutinas();

    function getRutinas() {
        $('.getRutinas').DataTable().clear().destroy();
        $('.getRutinas').DataTable({
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Registros _MENU_ ",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla =(",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": { "sNext": "Siguiente", "sPrevious": "Anterior" }
            },
            processing: true,
            serverSide: false,
            ajax: {
                url: 'getRutinas',
                type: 'GET'
            }
        });
    }

    $(document).on('click', '#btnEliminar', function() {
        $('#id').val($(this).val());
        $('#modalEliminacion').modal('show');
    });
</script>

<?= $this->endSection() ?>


<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/asignacion/lista') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-link"></i> Asignar programas a alumnos
                    </h3>
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

                    <form method="POST" action="<?= base_url('dashboard/gym/asignacion/asignar') ?>">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Programa</label>
                                <select name="programa_id" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach (($programas ?? []) as $p) : ?>
                                        <option value="<?= (int)$p->id ?>"><?= esc($p->nombre) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Alumno</label>
                                <select name="usuario_id" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach (($alumnos ?? []) as $a) : ?>
                                        <option value="<?= (int)$a->id ?>"><?= esc($a->nombre . ' ' . $a->apellido) ?> (<?= esc($a->correo) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Inicio</label>
                                <input type="date" name="fecha_inicio" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Asignaciones actuales
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered getAsignaciones">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Alumno</th>
                                    <th>Correo</th>
                                    <th>Estado</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<form id="frmDesasignar" method="POST" action="<?= base_url('dashboard/gym/asignacion/desasignar'); ?>" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="desasignar_id" value="">
</form>

<script>
    getAsignaciones();

    function getAsignaciones() {
        $('.getAsignaciones').DataTable().clear().destroy();
        $('.getAsignaciones').DataTable({
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
            ajax: { url: 'getAsignaciones', type: 'GET' }
        });
    }

    $(document).on('click', '#btnDesasignar', function() {
        if (!confirm('¿Quitar asignación?')) return;
        $('#desasignar_id').val($(this).val());
        $('#frmDesasignar').submit();
    });
</script>

<?= $this->endSection() ?>


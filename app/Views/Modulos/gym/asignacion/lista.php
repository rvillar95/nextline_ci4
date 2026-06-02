<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/asignacion/lista') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>
<?= $this->include('Modulos/gym/partials/datatable_lang') ?>

<div class="container-fluid gym-page gym-page--asignacion">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-paper-plane',
        'title' => 'Asignar programas',
        'subtitle' => 'Vincula un plan de entrenamiento a un alumno. Lo verá en su portal desde la fecha de inicio.',
        'module' => 'asignacion',
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'asignacion']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-plus-circle"></i> Nueva asignación</h3>
        </div>
        <div class="gym-section-card__body gym-assign-box">
            <form method="POST" action="<?= base_url('dashboard/gym/asignacion/asignar') ?>" class="gym-form">
                <?= csrf_field() ?>
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Programa</label>
                        <select name="programa_id" class="form-select" required>
                            <option value="">Elige un programa...</option>
                            <?php foreach (($programas ?? []) as $p) : ?>
                                <option value="<?= (int) $p->id ?>"><?= esc($p->nombre) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <label class="form-label">Alumno</label>
                        <select name="usuario_id" class="form-select" required>
                            <option value="">Elige un alumno...</option>
                            <?php foreach (($alumnos ?? []) as $a) : ?>
                                <option value="<?= (int) $a->id ?>">
                                    <?= esc($a->nombre . ' ' . $a->apellido) ?> — <?= esc($a->correo) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 mb-md-0">
                        <label class="form-label">Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn gym-btn-primary w-100">
                            <i class="fas fa-check me-1"></i> Asignar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Asignaciones actuales</h3>
                <p class="gym-section-card__hint">Programas activos por alumno. Puedes quitar una asignación si fue un error.</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <div class="gym-table-wrap table-responsive">
                <table class="table gym-table getAsignaciones w-100">
                    <thead>
                        <tr>
                            <th>Programa</th>
                            <th>Alumno</th>
                            <th>Correo</th>
                            <th>Estado</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<form id="frmDesasignar" method="POST" action="<?= base_url('dashboard/gym/asignacion/desasignar') ?>" class="d-none">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="desasignar_id" value="">
</form>

<script>
    $(function() {
        $('.getAsignaciones').DataTable({
            language: window.gymDataTableLang,
            processing: true,
            serverSide: false,
            ajax: { url: 'getAsignaciones', type: 'GET' },
            order: [[4, 'desc']],
            pageLength: 25,
        });
    });

    $(document).on('click', '#btnDesasignar', function() {
        if (!confirm('¿Quitar esta asignación? El alumno dejará de ver el programa en el portal.')) {
            return;
        }
        $('#desasignar_id').val($(this).val());
        $('#frmDesasignar').submit();
    });
</script>

<?= $this->endSection() ?>

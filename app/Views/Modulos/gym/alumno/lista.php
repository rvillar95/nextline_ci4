<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/lista') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>
<?= $this->include('Modulos/gym/partials/datatable_lang') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-users',
        'title' => 'Alumnos',
        'subtitle' => 'Usuarios con acceso al portal del alumno. Desde aquí gestionas su cuenta antes de asignar programas.',
        'module' => 'alumno',
        'primary' => [
            'url' => base_url('dashboard/gym/alumno/registro'),
            'label' => 'Nuevo alumno',
            'icon' => 'fa-user-plus',
        ],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'alumno']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <?php if (! empty($sinEntrenar7)) : ?>
    <div class="gym-section-card mb-4 gym-inactive-alert">
        <div class="gym-section-card__head">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Sin entrenar (7+ días)</h3>
            <p class="gym-section-card__hint mb-0"><?= count($sinEntrenar7) ?> alumno(s) activos sin sesión completada reciente.</p>
        </div>
        <div class="gym-section-card__body">
            <ul class="list-unstyled mb-0 gym-inactive-list">
                <?php foreach ($sinEntrenar7 as $a) : ?>
                <li class="d-flex flex-wrap justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong><?= esc(trim(($a->nombre ?? '') . ' ' . ($a->apellido ?? ''))) ?></strong>
                        <?php if (! empty($a->correo)) : ?>
                            <span class="text-muted small ms-1"><?= esc($a->correo) ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="<?= base_url('dashboard/gym/alumno/editar/' . (int) $a->id) ?>" class="btn btn-sm gym-btn-outline">Ver ficha</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Listado de alumnos</h3>
                <p class="gym-section-card__hint">Correo y teléfono para contacto. El estado controla si pueden iniciar sesión.</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <div class="gym-table-wrap table-responsive">
                <table class="table gym-table getAlumnos w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Adherencia (28d)</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('Modulos/gym/partials/delete_modal', [
    'action' => base_url('dashboard/gym/alumno/eliminar'),
    'message' => '¿Eliminar este alumno? Perderá acceso al portal.',
]) ?>

<script>
    $(function() {
        gymInitListTable('.getAlumnos', '<?= base_url('dashboard/gym/alumno/getAlumnos') ?>', 6);
    });
    $(document).on('click', '.btnEliminarGym, #btnEliminar', function() {
        $('#id').val($(this).val());
        $('#modalEliminacion').modal('show');
    });
</script>

<?= $this->endSection() ?>

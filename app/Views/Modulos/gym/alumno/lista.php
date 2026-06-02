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
        $('.getAlumnos').DataTable({
            language: window.gymDataTableLang,
            processing: true,
            serverSide: false,
            ajax: { url: 'getAlumnos', type: 'GET' },
            order: [[0, 'asc']],
            pageLength: 25,
        });
    });
    $(document).on('click', '#btnEliminar', function() {
        $('#id').val($(this).val());
        $('#modalEliminacion').modal('show');
    });
</script>

<?= $this->endSection() ?>

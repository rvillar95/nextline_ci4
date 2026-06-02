<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/programa/lista') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>
<?= $this->include('Modulos/gym/partials/datatable_lang') ?>

<div class="container-fluid gym-page gym-page--programa">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-calendar-week',
        'title' => 'Programas de entrenamiento',
        'subtitle' => 'Combina rutinas en un plan por semanas. Después asígnalo a tus alumnos.',
        'module' => 'programa',
        'primary' => [
            'url' => base_url('dashboard/gym/programa/registro'),
            'label' => 'Nuevo programa',
            'icon' => 'fa-plus',
        ],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'programa']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Programas activos</h3>
                <p class="gym-section-card__hint">Edita un programa para ordenar rutinas y asignar días de la semana.</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <div class="gym-table-wrap table-responsive">
                <table class="table gym-table getProgramas w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Creado por</th>
                            <th>Semanas</th>
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
    'action' => base_url('dashboard/gym/programa/eliminar'),
    'message' => '¿Eliminar este programa? Los alumnos dejarán de verlo si estaba asignado.',
]) ?>

<script>
    $(function() {
        $('.getProgramas').DataTable({
            language: window.gymDataTableLang,
            processing: true,
            serverSide: false,
            ajax: { url: 'getProgramas', type: 'GET' },
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

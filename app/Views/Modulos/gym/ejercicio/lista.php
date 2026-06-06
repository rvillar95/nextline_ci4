<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/ejercicio/lista') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>
<?= $this->include('Modulos/gym/partials/datatable_lang') ?>

<div class="container-fluid gym-page gym-page--ejercicio">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-dumbbell',
        'title' => 'Biblioteca de ejercicios',
        'subtitle' => 'Crea y organiza los movimientos que usarás en las rutinas. Define grupo muscular y tipo de base.',
        'module' => 'ejercicio',
        'primary' => [
            'url' => base_url('dashboard/gym/ejercicio/registro'),
            'label' => 'Nuevo ejercicio',
            'icon' => 'fa-plus',
        ],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'ejercicio']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Todos los ejercicios</h3>
                <p class="gym-section-card__hint">Busca por nombre o filtra desde la tabla. Los inactivos no aparecen en nuevas rutinas.</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <div class="gym-table-wrap table-responsive">
                <table class="table gym-table getEjercicios w-100">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Grupo principal</th>
                            <th>Grupo secundario</th>
                            <th>Base</th>
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
    'action' => base_url('dashboard/gym/ejercicio/eliminar'),
    'message' => '¿Eliminar este ejercicio? Dejará de estar disponible en rutinas nuevas.',
]) ?>

<script>
    $(function() {
        gymInitListTable('.getEjercicios', '<?= base_url('dashboard/gym/ejercicio/getEjercicios') ?>', 6);
    });

    $(document).on('click', '.btnEliminarGym, #btnEliminar', function() {
        $('#id').val($(this).val());
        $('#modalEliminacion').modal('show');
    });
</script>

<?= $this->endSection() ?>

<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/rutina/lista') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>
<?= $this->include('Modulos/gym/partials/datatable_lang') ?>

<div class="container-fluid gym-page gym-page--rutina">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-clipboard-list',
        'title' => 'Rutinas de entrenamiento',
        'subtitle' => 'Agrupa ejercicios con series, repeticiones y descansos. Luego únelas en programas.',
        'module' => 'rutina',
        'primary' => [
            'url' => base_url('dashboard/gym/rutina/registro'),
            'label' => 'Nueva rutina',
            'icon' => 'fa-plus',
        ],
    ]) ?>

    <?= view('Modulos/gym/partials/workflow', ['active' => 'rutina']) ?>
    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Rutinas guardadas</h3>
                <p class="gym-section-card__hint">Abre una rutina para editar su lista de ejercicios (builder).</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <div class="gym-table-wrap table-responsive">
                <table class="table gym-table getRutinas w-100">
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

<?= view('Modulos/gym/partials/delete_modal', [
    'action' => base_url('dashboard/gym/rutina/eliminar'),
    'message' => '¿Eliminar esta rutina? Se perderá el detalle de ejercicios.',
]) ?>

<?= view('Modulos/gym/partials/duplicate_modal', [
    'action'  => base_url('dashboard/gym/rutina/duplicar'),
    'title'   => 'Duplicar rutina',
    'message' => 'Se copiarán todos los ejercicios, series y notas del alumno.',
    'module'  => 'rutina',
]) ?>

<script>
    $(function() {
        gymInitListTable('.getRutinas', '<?= base_url('dashboard/gym/rutina/getRutinas') ?>', 4);
    });
    $(document).on('click', '.btnEliminarGym, #btnEliminar', function() {
        $('#id').val($(this).val());
        $('#modalEliminacion').modal('show');
    });
    $(document).on('click', '.btnDuplicar', function() {
        var id = $(this).val();
        var nombre = $(this).data('nombre') || '';
        $('#dup_id').val(id);
        $('#dup_nombre').val('Copia de ' + nombre);
        $('#modalDuplicar').modal('show');
    });
</script>

<?= $this->endSection() ?>

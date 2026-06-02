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

<script>
    $(function() {
        $('.getRutinas').DataTable({
            language: window.gymDataTableLang,
            processing: true,
            serverSide: false,
            ajax: { url: 'getRutinas', type: 'GET' },
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

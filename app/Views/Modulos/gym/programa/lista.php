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

<?= view('Modulos/gym/partials/duplicate_modal', [
    'action'  => base_url('dashboard/gym/programa/duplicar'),
    'title'   => 'Duplicar programa',
    'message' => 'Se copiarán el programa y todas sus rutinas (ejercicios incluidos).',
    'module'  => 'programa',
]) ?>

<script>
    $(function() {
        gymInitListTable('.getProgramas', '<?= base_url('dashboard/gym/programa/getProgramas') ?>', 5);
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

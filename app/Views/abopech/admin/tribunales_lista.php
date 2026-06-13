<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech">Tribunales</h1>
<p><a href="<?= base_url('abopech/admin/tribunales/registro') ?>" class="btn btn-abopech btn-sm">Nuevo tribunal</a></p>
<table class="table table-sm bg-white shadow-sm">
    <thead><tr><th>Nombre</th><th>Tipo</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach ($tribunales as $t): ?>
        <tr><td><?= esc($t['nombre']) ?></td><td><?= esc($t['tipo_codigo']) ?></td><td><?= esc($t['estado']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>

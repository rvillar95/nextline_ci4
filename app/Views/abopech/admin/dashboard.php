<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech">Panel ABOPECH</h1>
<div class="row g-3">
    <div class="col-md-6"><div class="card"><div class="card-body"><h2 class="h6">Perfiles pendientes</h2><p class="display-6"><?= (int) $pendientes ?></p><a href="<?= base_url('abopech/admin/abogados') ?>">Ver</a></div></div></div>
    <div class="col-md-6"><div class="card"><div class="card-body"><h2 class="h6">Contactos nuevos</h2><p class="display-6"><?= (int) $contactos_nuevos ?></p><a href="<?= base_url('abopech/admin/contactos') ?>">Ver</a></div></div></div>
</div>
<ul class="mt-4">
    <li><a href="<?= base_url('abopech/admin/tribunales') ?>">Tribunales</a></li>
</ul>
<?= $this->endSection() ?>

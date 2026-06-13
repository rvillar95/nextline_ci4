<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech mb-3">Confirmar contacto</h1>
<div class="card mb-3"><div class="card-body">
    <p><strong>Abogado:</strong> <?= esc($abogado['nombres'] . ' ' . $abogado['apellidos']) ?></p>
    <p><strong>Nombre:</strong> <?= esc($datos['nombre'] . ' ' . $datos['apellido']) ?></p>
    <p><strong>Correo:</strong> <?= esc($datos['correo']) ?></p>
    <p><strong>Teléfono:</strong> <?= esc($datos['telefono']) ?></p>
</div></div>
<form method="post" action="<?= base_url('abopech/abogado/' . (int) $abogado['id'] . '/contactar/confirmar') ?>">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-abopech">Confirmar y abrir WhatsApp / teléfono</button>
    <a href="<?= base_url('abopech/abogado/' . (int) $abogado['id'] . '/contactar') ?>" class="btn btn-link">Volver</a>
</form>
<?= $this->endSection() ?>

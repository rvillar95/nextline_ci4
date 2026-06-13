<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<?php $a = $perfil['abogado']; ?>
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h1 class="h3 text-abopech"><?= esc($a['nombres'] . ' ' . $a['apellidos']) ?></h1>
        <p class="text-muted">Abogado penalista — más de 1.000 audiencias (ABOPECH)</p>
        <hr>
        <h2 class="h5">Habilidades</h2>
        <p><?= nl2br(esc($a['habilidades'])) ?></p>
        <h2 class="h5 mt-4">Experiencia</h2>
        <p><?= nl2br(esc($a['experiencia'])) ?></p>
        <?php if (!empty($perfil['estudios'])): ?>
            <h2 class="h5 mt-4">Estudios</h2>
            <ul>
                <?php foreach ($perfil['estudios'] as $e): ?>
                    <li><?= esc($e['nombre']) ?> — <?= esc($e['universidad']) ?> (<?= (int) $e['anio_titulacion'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php if (!empty($perfil['tribunales'])): ?>
            <h2 class="h5 mt-4">Tribunales</h2>
            <ul>
                <?php foreach ($perfil['tribunales'] as $t): ?>
                    <li><?= esc($t['nombre']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="<?= base_url('abopech/abogado/' . (int) $a['id'] . '/contactar') ?>" class="btn btn-abopech btn-lg mt-3">Contactar</a>
    </div>
</div>
<?= $this->endSection() ?>

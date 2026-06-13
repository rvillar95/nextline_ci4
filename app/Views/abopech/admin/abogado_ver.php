<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<?php $a = $perfil['abogado']; ?>
<h1 class="h4 text-abopech"><?= esc($a['nombres'] . ' ' . $a['apellidos']) ?></h1>
<p><?= nl2br(esc($a['habilidades'])) ?></p>
<p><?= nl2br(esc($a['experiencia'])) ?></p>
<a href="<?= base_url('abopech/admin/abogados') ?>" class="btn btn-link">Volver</a>
<?= $this->endSection() ?>

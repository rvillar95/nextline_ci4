<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<link href="<?= base_url('lib/css/nutrinext-equipo.css') ?>" rel="stylesheet" type="text/css" />

<section class="nutrinext-hero equipo-hero">
    <div class="container text-center text-white">
        <p class="section-eyebrow-ns mb-2">NutriNext</p>
        <h1 class="hero-title-ns mb-3">Nuestro equipo</h1>
        <p class="mb-0 mx-auto equipo-hero-sub">Conoce a los nutricionistas que te acompañan. Revisa su formación y reserva tu consulta en línea.</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <?php if (empty($nutricionistas)): ?>
            <div class="text-center py-5">
                <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">Pronto publicaremos el equipo de profesionales.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($nutricionistas as $n): ?>
                    <?= view('Web/partials/card_nutricionista', ['n' => $n, 'placeholder_foto' => $placeholder_foto]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

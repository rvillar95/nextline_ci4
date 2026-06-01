<?php $this->extend('layout/web') ?>



<?= $this->section('content') ?>



<link href="<?= base_url('lib/css/nutrinext-funcionalidades.css') ?>" rel="stylesheet" type="text/css" />



<section class="nutrinext-hero" style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%); padding: 100px 0 70px;">

    <div class="container text-center text-white">

        <p class="section-eyebrow-ns mb-2" style="color: rgba(255,255,255,0.9);">Plataforma NutriNext</p>

        <h1 class="hero-title-ns mb-3" style="font-weight: 800;">Funcionalidades del sistema</h1>

        <p class="mb-0 mx-auto" style="max-width: 640px; opacity: 0.95;">Todo lo que incluye la plataforma para gestionar tu consulta nutricional de punta a punta.</p>

    </div>

</section>



<section class="func-page-section py-5">

    <div class="container">

        
        <?php
        $urlTodas = base_url('funcionalidades');
        $catActiva = $categoria_actual ?? '';
        ?>
        <div class="func-toolbar">
            <div class="func-filter-mobile">
                <label class="func-filter-mobile__label" for="func-cat-select">Ver categoría</label>
                <select id="func-cat-select" class="func-filter-mobile__select" aria-label="Filtrar por categoría">
                    <option value="<?= esc($urlTodas) ?>"<?= $catActiva === '' ? ' selected' : '' ?>>Todas las categorías</option>
                    <?php foreach ($categorias as $key => $label): ?>
                        <?php $urlCat = base_url('funcionalidades?categoria=' . urlencode($key)); ?>
                        <option value="<?= esc($urlCat) ?>"<?= $catActiva === $key ? ' selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="func-filters--desktop" role="navigation" aria-label="Filtrar por categoría">
                <a href="<?= esc($urlTodas) ?>" class="func-filter-pill <?= $catActiva === '' ? 'is-active' : '' ?>">Todas</a>
                <?php foreach ($categorias as $key => $label): ?>
                    <a href="<?= base_url('funcionalidades?categoria=' . urlencode($key)) ?>"
                       class="func-filter-pill <?= $catActiva === $key ? 'is-active' : '' ?>"><?= esc($label) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="func-list">

        <?php if (empty($servicios)): ?>

            <div class="func-empty">

                <i class="fas fa-th-large d-block"></i>

                <p class="text-muted mb-0">No hay funcionalidades publicadas en la web.<br>Actívalas en el dashboard (visible web = Sí).</p>

            </div>

        <?php elseif (! empty($categoria_actual) && ! empty($por_categoria[$categoria_actual])): ?>

            <?php

            $items = $por_categoria[$categoria_actual];

            $catLabel = $categorias[$categoria_actual] ?? $categoria_actual;

            ?>

            <div class="func-categoria-block">

                <div class="func-categoria-header">

                    <span class="func-categoria-header__accent"></span>

                    <h2 class="func-categoria-header__title"><?= esc($catLabel) ?></h2>

                    <span class="func-categoria-header__count"><?= count($items) ?> <?= count($items) === 1 ? 'módulo' : 'módulos' ?></span>

                </div>

                <div class="func-cards-grid">

                    <?php foreach ($items as $s): ?>

                        <?= view('Web/partials/card_funcionalidad', ['s' => $s]) ?>

                    <?php endforeach; ?>

                </div>

            </div>

        <?php else: ?>

            <?php foreach ($categorias as $catKey => $catLabel): ?>

                <?php if (empty($por_categoria[$catKey])) {

                    continue;

                } ?>

                <?php $items = $por_categoria[$catKey]; ?>

                <div class="func-categoria-block">

                    <div class="func-categoria-header">

                        <span class="func-categoria-header__accent"></span>

                        <h2 class="func-categoria-header__title"><?= esc($catLabel) ?></h2>

                        <span class="func-categoria-header__count"><?= count($items) ?> <?= count($items) === 1 ? 'módulo' : 'módulos' ?></span>

                    </div>

                    <div class="func-cards-grid">

                        <?php foreach ($items as $s): ?>

                            <?= view('Web/partials/card_funcionalidad', ['s' => $s]) ?>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

        </div>

    </div>

</section>

<script>
document.getElementById('func-cat-select')?.addEventListener('change', function () {
    var url = this.value;
    if (url) {
        window.location.href = url;
    }
});
</script>

<section class="py-5" style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%);">

    <div class="container text-center text-white">

        <h2 class="h4 fw-bold mb-2">¿Listo para usar NutriNext?</h2>

        <p class="mb-4 opacity-90">Reserva una hora o inicia sesión si ya eres profesional.</p>

        <a href="<?= base_url('reservar') ?>" class="btn-hero-primary me-2"><i class="fas fa-calendar-check me-1"></i> Reservar</a>

        <a href="<?= base_url('login') ?>" class="btn-hero-secondary"><i class="fas fa-sign-in-alt me-1"></i> Iniciar sesión</a>

    </div>

</section>

<?= $this->endSection() ?>


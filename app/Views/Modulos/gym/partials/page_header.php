<?php
/**
 * @var string $icon      Clase Font Awesome (ej. fa-dumbbell)
 * @var string $title
 * @var string $subtitle
 * @var string $module    ejercicio|rutina|programa|alumno|asignacion
 * @var array|null $primary  ['url','label','icon'?]
 * @var array|null $back     ['url','label'?]
 */
$icon     = $icon ?? 'fa-dumbbell';
$title    = $title ?? '';
$subtitle = $subtitle ?? '';
$primary  = $primary ?? null;
$back     = $back ?? null;
?>
<header class="gym-hero">
    <div class="gym-hero__inner">
        <div>
            <h1 class="gym-hero__title">
                <i class="fas <?= esc($icon) ?>"></i>
                <?= esc($title) ?>
            </h1>
            <?php if (! empty($subtitle)) : ?>
                <p class="gym-hero__subtitle"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>
        <div class="gym-hero__actions">
            <?php if ($back !== null && ! empty($back['url'])) : ?>
                <a href="<?= esc($back['url']) ?>" class="btn gym-btn-hero gym-btn-hero--ghost">
                    <i class="fas fa-arrow-left me-1"></i>
                    <?= esc($back['label'] ?? 'Volver') ?>
                </a>
            <?php endif; ?>
            <?php if ($primary !== null && ! empty($primary['url'])) : ?>
                <a href="<?= esc($primary['url']) ?>" class="btn gym-btn-hero">
                    <?php if (! empty($primary['icon'])) : ?>
                        <i class="fas <?= esc($primary['icon']) ?> me-1"></i>
                    <?php endif; ?>
                    <?= esc($primary['label']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

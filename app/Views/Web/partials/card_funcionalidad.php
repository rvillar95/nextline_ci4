<?php /** @var object $s */ ?>
<?php
$accent = $s->color ?: '#22C55E';
$accentSafe = preg_match('/^#[0-9A-Fa-f]{3,8}$/', $accent) ? $accent : '#22C55E';
?>
<article class="card-func-ns-wrap">
    <a href="<?= base_url('funcionalidades/' . rawurlencode($s->codigo)) ?>"
       class="card-func-ns"
       style="--func-accent: <?= esc($accentSafe) ?>; --func-accent-soft: <?= esc($accentSafe) ?>22">
        <span class="card-func-ns__bar" aria-hidden="true"></span>
        <div class="card-func-ns__top">
            <div class="card-func-ns__icon">
                <i class="<?= esc($s->icono ?: 'fas fa-circle') ?>" aria-hidden="true"></i>
            </div>
        </div>
        <div class="card-func-ns__body">
            <h3 class="card-func-ns__title"><?= esc($s->nombre) ?></h3>
            <p class="card-func-ns__desc"><?= esc($s->descripcion_corta) ?></p>
        </div>
        <div class="card-func-ns__footer">
            <span class="card-func-ns__cta">Ver detalle <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </div>
    </a>
</article>

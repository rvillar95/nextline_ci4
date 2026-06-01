<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<link href="<?= base_url('lib/css/nutrinext-funcionalidades.css') ?>" rel="stylesheet" type="text/css" />

<section style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%); padding: 100px 0 60px;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider: '›';">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-white-50">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('funcionalidades') ?>" class="text-white-50">Funcionalidades</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?= esc($servicio->nombre) ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-start gap-4 flex-wrap">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:72px;height:72px;background:rgba(255,255,255,0.2);">
                <i class="<?= esc($servicio->icono ?: 'fas fa-circle') ?> fa-2x text-white"></i>
            </div>
            <div class="text-white flex-grow-1">
                <span class="badge bg-light text-dark mb-2"><?= esc($categorias[$servicio->categoria] ?? $servicio->categoria) ?></span>
                <h1 class="fw-bold mb-3"><?= esc($servicio->nombre) ?></h1>
                <p class="lead mb-0 opacity-90"><?= esc($servicio->descripcion_corta) ?></p>
            </div>
        </div>
    </div>
</section>

<section class="py-5 func-detail-content" style="background: #fff;">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <?php if (! empty($servicio->descripcion_larga)): ?>
                    <h2 class="h4 fw-bold mb-3">Descripcion</h2>
                    <div class="text-muted mb-4" style="white-space: pre-line;"><?= esc($servicio->descripcion_larga) ?></div>
                <?php endif; ?>

                <?php if (! empty($incluye)): ?>
                    <h3 class="h5 fw-bold mb-3"><i class="fas fa-check-circle text-success me-2"></i>Que incluye</h3>
                    <ul class="list-unstyled mb-4">
                        <?php foreach ($incluye as $linea): ?>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?= esc($linea) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if (! empty($beneficios)): ?>
                    <h3 class="h5 fw-bold mb-3"><i class="fas fa-star text-warning me-2"></i>Beneficios</h3>
                    <ul class="mb-4">
                        <?php foreach ($beneficios as $linea): ?>
                            <li class="mb-2 text-muted"><?= esc($linea) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php if ($servicio->requiere_configuracion === 'S' && ! empty($servicio->nota_configuracion)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-cog me-2"></i>
                        <strong>Configuracion:</strong> <?= esc($servicio->nota_configuracion) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 func-detail-sidebar">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h4 class="h6 fw-bold mb-3">Acciones</h4>
                        <a href="<?= base_url('reservar') ?>" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-calendar-check me-1"></i> Reservar hora
                        </a>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-sign-in-alt me-1"></i> Iniciar sesion
                        </a>
                        <?php if (! empty($servicio->documentacion_url)): ?>
                            <a href="<?= esc($servicio->documentacion_url) ?>" class="btn btn-outline-primary w-100 mb-2" target="_blank" rel="noopener">
                                <i class="fas fa-book me-1"></i> Documentacion
                            </a>
                        <?php endif; ?>
                        <?php if (! empty($servicio->video_url)): ?>
                            <a href="<?= esc($servicio->video_url) ?>" class="btn btn-outline-danger w-100" target="_blank" rel="noopener">
                                <i class="fab fa-youtube me-1"></i> Ver video
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (! empty($relacionados)): ?>
            <hr class="my-5">
            <h2 class="h4 fw-bold mb-4">También te puede interesar</h2>
            <div class="func-cards-grid">
                <?php foreach ($relacionados as $s): ?>
                    <?= view('Web/partials/card_funcionalidad', ['s' => $s]) ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

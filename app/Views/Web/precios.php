<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<link href="<?= base_url('lib/css/nutrinext-funcionalidades.css') ?>" rel="stylesheet">

<section class="page-content">
    <div class="container py-5">
        <div class="section-header-ns text-center mb-5">
            <p class="section-eyebrow-ns">Planes Chile (CLP)</p>
            <h1 class="section-title-ns">Elige el plan para tu consulta</h1>
            <p class="section-lead-ns text-muted mx-auto" style="max-width:640px;">
                Precios mensuales en pesos chilenos. Pago anual: <strong>2 meses gratis</strong>.
                Add-ons de antropometría disponibles en Esencial y Profesional.
            </p>
        </div>

        <div class="row g-4 justify-content-center mb-5">
            <?php foreach ($planes as $plan): ?>
                <?php
                $esRecomendado = ($plan->slug === 'nutri-profesional');
                $esClinica     = ($plan->slug === 'nutri-clinica');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm <?= $esRecomendado ? 'border-success border-2' : '' ?>">
                        <?php if ($esRecomendado): ?>
                            <div class="card-header bg-success text-white text-center py-2">
                                <small class="fw-bold">Más popular</small>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h2 class="h5 card-title"><?= esc($plan->nombre) ?></h2>
                            <p class="text-muted small flex-grow-1"><?= esc($plan->descripcion) ?></p>
                            <?php if ($esClinica): ?>
                                <p class="h4 text-success mb-0">Desde $<?= number_format((float) $plan->precio_mensual, 0, ',', '.') ?><small class="text-muted fs-6">/mes</small></p>
                                <p class="small text-muted">Cotización según usuarios y sedes</p>
                            <?php else: ?>
                                <p class="h3 text-success mb-0">
                                    $<?= number_format((float) $plan->precio_mensual, 0, ',', '.') ?>
                                    <small class="text-muted fs-6">/mes</small>
                                </p>
                                <?php if ((float) $plan->precio_setup > 0): ?>
                                    <p class="small text-muted">Implementación desde $<?= number_format((float) $plan->precio_setup, 0, ',', '.') ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <a href="<?= base_url('contacto') ?>?plan=<?= urlencode($plan->slug) ?>" class="btn <?= $esRecomendado ? 'btn-success' : 'btn-outline-success' ?> w-100">
                                <?= $esClinica ? 'Solicitar cotización' : 'Contratar' ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($addons)): ?>
        <div class="mb-5">
            <h2 class="h4 text-center mb-4">Add-ons mensuales</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <ul class="list-group list-group-flush shadow-sm">
                        <?php foreach ($addons as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <?= esc($a['nombre']) ?>
                                    <?php if (!empty($a['nota'])): ?>
                                        <small class="text-muted d-block"><?= esc($a['nota']) ?></small>
                                    <?php endif; ?>
                                </span>
                                <strong class="text-success">$<?= number_format((int) $a['precio'], 0, ',', '.') ?>/mes</strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($bundles)): ?>
        <div class="mb-5">
            <h2 class="h4 text-center mb-4">Bundles con sitio web NextLine</h2>
            <div class="row g-3 justify-content-center">
                <?php foreach ($bundles as $b): ?>
                    <div class="col-md-6">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body">
                                <h3 class="h6"><?= esc($b['nombre']) ?></h3>
                                <p class="small text-muted mb-2"><?= esc($b['detalle']) ?></p>
                                <p class="h5 text-success mb-0">$<?= number_format((int) $b['precio'], 0, ',', '.') ?>/mes</p>
                                <?php if ($b['ahorro'] > 0): ?>
                                    <small class="text-success">Ahorras $<?= number_format((int) $b['ahorro'], 0, ',', '.') ?> vs contratar por separado</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-center">
            <p class="text-muted mb-3">¿Dudas sobre qué plan elegir?</p>
            <a href="<?= base_url('contacto') ?>" class="btn btn-success btn-lg">
                <i class="fas fa-envelope me-2"></i> Hablar con ventas
            </a>
            <a href="<?= base_url('funcionalidades') ?>" class="btn btn-link">Ver funcionalidades</a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

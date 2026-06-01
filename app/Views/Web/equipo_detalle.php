<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<link href="<?= base_url('lib/css/nutrinext-equipo.css') ?>" rel="stylesheet" type="text/css" />

<?php
$fotoUrl = !empty($nutricionista->foto) ? base_url($nutricionista->foto) : $placeholder_foto;
?>

<section class="equipo-detalle-hero">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb breadcrumb-light mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('equipo') ?>" class="text-white-50">Equipo</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?= esc($nombre_completo) ?></li>
            </ol>
        </nav>
        <div class="row align-items-center g-4 text-white">
            <div class="col-auto">
                <div class="equipo-detalle-foto-wrap">
                    <img src="<?= esc($fotoUrl) ?>" alt="<?= esc($nombre_completo) ?>" class="equipo-detalle-foto">
                </div>
            </div>
            <div class="col">
                <h1 class="h2 fw-bold mb-2"><?= esc($nombre_completo) ?></h1>
                <?php if (!empty($nutricionista->titulo_profesional)): ?>
                    <p class="mb-1 opacity-90"><?= esc($nutricionista->titulo_profesional) ?></p>
                <?php endif; ?>
                <?php if (!empty($nutricionista->especialidad)): ?>
                    <p class="mb-1"><i class="fas fa-leaf me-1"></i><?= esc($nutricionista->especialidad) ?></p>
                <?php endif; ?>
                <?php if (!empty($nutricionista->carrera)): ?>
                    <p class="mb-2"><i class="fas fa-graduation-cap me-1"></i><?= esc($nutricionista->carrera) ?></p>
                <?php endif; ?>
                <div class="d-flex flex-wrap gap-3 small">
                    <?php if (!empty($nutricionista->telefono)): ?>
                        <a href="tel:<?= esc(preg_replace('/\s+/', '', $nutricionista->telefono)) ?>" class="text-white text-decoration-none">
                            <i class="fas fa-phone me-1"></i><?= esc($nutricionista->telefono) ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($nutricionista->correo)): ?>
                        <a href="mailto:<?= esc($nutricionista->correo) ?>" class="text-white text-decoration-none">
                            <i class="fas fa-envelope me-1"></i><?= esc($nutricionista->correo) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (!empty($nutricionista->presentacion)): ?>
                    <div class="mb-4">
                        <h2 class="h5 fw-bold mb-3"><i class="fas fa-hand-sparkles text-success me-2"></i>Presentación</h2>
                        <div class="equipo-text-block text-secondary"><?= esc($nutricionista->presentacion) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($nutricionista->descripcion_profesional)): ?>
                    <div class="mb-4">
                        <h2 class="h5 fw-bold mb-3"><i class="fas fa-briefcase text-success me-2"></i>Descripción profesional</h2>
                        <div class="equipo-text-block text-secondary"><?= esc($nutricionista->descripcion_profesional) ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($credenciales_por_tipo)): ?>
                    <div class="mb-4">
                        <h2 class="h5 fw-bold mb-3"><i class="fas fa-certificate text-success me-2"></i>Formación y certificaciones</h2>
                        <?php foreach ($credenciales_por_tipo as $tipo => $items): ?>
                            <h3 class="h6 text-muted text-uppercase mt-3 mb-2"><?= esc($tipos_label[$tipo] ?? $tipo) ?></h3>
                            <?php foreach ($items as $c): ?>
                                <div class="equipo-credencial-item">
                                    <strong><?= esc($c['nombre']) ?></strong>
                                    <?php if (!empty($c['institucion'])): ?>
                                        <span class="text-muted"> — <?= esc($c['institucion']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($c['anio'])): ?>
                                        <span class="text-muted"> (<?= (int) $c['anio'] ?>)</span>
                                    <?php endif; ?>
                                    <?php if (!empty($c['descripcion'])): ?>
                                        <p class="small text-secondary mb-1 mt-1"><?= esc($c['descripcion']) ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($c['archivo_ruta'])): ?>
                                        <a href="<?= base_url($c['archivo_ruta']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary mt-1">
                                            <i class="fas fa-file-download me-1"></i>Ver documento<?= !empty($c['archivo_nombre']) ? ': ' . esc($c['archivo_nombre']) : '' ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4 text-center">
                        <p class="text-muted small mb-3">Agenda tu consulta con <?= esc($nutricionista->nombre ?? '') ?>.</p>
                        <a href="<?= base_url('reservar?nutricionista_id=' . (int) $nutricionista->id) ?>" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-calendar-check me-1"></i> Reservar hora
                        </a>
                        <a href="<?= base_url('equipo') ?>" class="btn btn-outline-secondary w-100 btn-sm">Volver al equipo</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

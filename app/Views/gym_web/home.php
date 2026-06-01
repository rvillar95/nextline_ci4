<?= $this->extend('gym_web/layout') ?>

<?= $this->section('content') ?>

<header class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-5 fw-bold mb-3">Train Hard. Forge Stronger.</h1>
                <p class="lead mb-4">
                    Este front está preparado para integrar el template Canvas (CrossFit demo).
                    Cuando pegues los assets en `public/lib/canvas-gym/`, aquí se reemplaza el markup por el del template.
                </p>
                <a class="btn btn-primary btn-lg" href="<?= base_url('registro') . ($empresa_id ? '?empresa_id=' . (int)$empresa_id : '') ?>">
                    Crear cuenta
                </a>
            </div>
            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="p-4 bg-black bg-opacity-50 rounded">
                    <div class="small text-uppercase text-muted">WOD del día</div>
                    <?php if ($wod) : ?>
                        <h3 class="h5 mt-2 mb-2"><?= esc($wod->nombre) ?></h3>
                        <div class="text-muted"><?= esc($wod->descripcion ?? '') ?></div>
                    <?php else : ?>
                        <div class="text-muted mt-2">Sin rutina destacada todavía.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <h2 class="h3 mb-4">Membership</h2>
        <?php if (empty($planes)) : ?>
            <div class="text-muted">No hay planes configurados para esta empresa.</div>
        <?php else : ?>
            <div class="row">
                <?php foreach ($planes as $p) : ?>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="small text-muted">Plan mensual</div>
                                <h3 class="h5 mt-2"><?= esc($p->nombre) ?></h3>
                                <div class="fs-4 fw-bold"><?= esc($p->moneda ?? 'CLP') ?> <?= esc($p->monto_mensual) ?></div>
                                <?php if (!empty($p->descripcion)) : ?>
                                    <div class="text-muted mt-2"><?= esc($p->descripcion) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-white border-0">
                                <a class="btn btn-outline-primary w-100" href="<?= base_url('registro') . ($empresa_id ? '?empresa_id=' . (int)$empresa_id : '') ?>">
                                    Empezar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer class="py-4 bg-light">
    <div class="container">
        <div class="text-muted small">
            Canvas template integration scaffold. Reemplazar con footer del template final.
        </div>
    </div>
</footer>

<?= $this->endSection() ?>


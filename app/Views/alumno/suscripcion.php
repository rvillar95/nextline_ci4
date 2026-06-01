<?= $this->extend('layout/dashboard') ?>

<?= $this->section('alumno/suscripcion') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Mi suscripción</h3>
                    <div class="card-tools">
                        <a href="<?= base_url('alumno/inicio') ?>" class="btn btn-light">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger my-3" role="alert">
                            <?= is_array(session()->getFlashdata('errors')) ? implode(', ', session()->getFlashdata('errors')) : session()->getFlashdata('errors'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($suscripcion) : ?>
                        <div class="mb-2"><strong>Estado:</strong> <?= esc($suscripcion->estado) ?></div>
                        <div class="mb-2"><strong>Vence:</strong> <?= esc($suscripcion->fecha_fin ?? '-') ?></div>
                    <?php else : ?>
                        <div class="text-muted">Aún no tienes suscripción activa.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Planes</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($planes)) : ?>
                        <div class="text-muted">No hay planes disponibles para tu gimnasio.</div>
                    <?php else : ?>
                        <div class="row">
                            <?php foreach ($planes as $p) : ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= esc($p->nombre) ?></h5>
                                            <div class="mb-2"><strong><?= esc($p->moneda ?? 'CLP') ?></strong> <?= esc($p->monto_mensual) ?> / mes</div>
                                            <?php if (!empty($p->descripcion)) : ?>
                                                <div class="text-muted mb-3"><?= esc($p->descripcion) ?></div>
                                            <?php endif; ?>

                                            <form method="POST" action="<?= base_url('alumno/suscripcion/pagar') ?>">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="plan_id" value="<?= (int)$p->id ?>">
                                                <button type="submit" class="btn btn-primary w-100">Pagar con Mercado Pago</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


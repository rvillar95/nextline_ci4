<?= $this->extend('layout/dashboard') ?>

<?= $this->section('alumno/inicio') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Mi panel</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors') !== null) : ?>
                        <div class="alert alert-danger my-3" role="alert">
                            <?= session()->getFlashdata('errors'); ?>
                        </div>
                    <?php endif; ?>

                    <p class="mb-0">Hola, <strong><?= esc(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')) ?></strong></p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Programas asignados</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($asignaciones)) : ?>
                        <div class="text-muted">Aún no tienes programas asignados.</div>
                    <?php else : ?>
                        <div class="list-group">
                            <?php foreach ($asignaciones as $a) : ?>
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                   href="<?= base_url('alumno/programa/' . (int)$a->programa_id) ?>">
                                    <div>
                                        <div><strong><?= esc($a->programa_nombre) ?></strong></div>
                                        <small class="text-muted">Estado: <?= esc($a->estado) ?></small>
                                    </div>
                                    <span class="btn btn-sm btn-primary">Ver</span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


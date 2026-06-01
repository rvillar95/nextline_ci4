<?= $this->extend('layout/dashboard') ?>

<?= $this->section('alumno/programa/ver') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title"><?= esc($asign->programa_nombre ?? 'Programa') ?></h3>
                    <div class="card-tools">
                        <a href="<?= base_url('alumno/inicio') ?>" class="btn btn-light">Volver</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-muted">Estado asignación: <?= esc($asign->estado ?? '') ?></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rutinas</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($rutinas)) : ?>
                        <div class="text-muted">Este programa no tiene rutinas todavía.</div>
                    <?php else : ?>
                        <div class="accordion" id="accRutinas">
                            <?php foreach ($rutinas as $idx => $r) : ?>
                                <?php $hid = 'rutina_' . $idx; ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="h_<?= esc($hid) ?>">
                                        <button class="accordion-button <?= $idx === 0 ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#c_<?= esc($hid) ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>" aria-controls="c_<?= esc($hid) ?>">
                                            <?= esc($r->rutina_nombre) ?>
                                        </button>
                                    </h2>
                                    <div id="c_<?= esc($hid) ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" aria-labelledby="h_<?= esc($hid) ?>" data-bs-parent="#accRutinas">
                                        <div class="accordion-body">
                                            <?php if (!empty($r->rutina_descripcion)) : ?>
                                                <div class="mb-2"><?= esc($r->rutina_descripcion) ?></div>
                                            <?php endif; ?>

                                            <?php
                                            // cargar ejercicios de la rutina
                                            $db = \Config\Database::connect();
                                            $items = $db->table('gym_rutina_ejercicio re')
                                                ->select('re.orden, re.series, re.repeticiones, re.descanso_seg, re.notas, e.nombre as ejercicio_nombre')
                                                ->join('gym_ejercicio e', 'e.id = re.ejercicio_id')
                                                ->where('re.rutina_id', (int)$r->rutina_id)
                                                ->orderBy('re.orden', 'ASC')
                                                ->get()
                                                ->getResult('object');
                                            ?>

                                            <?php if (empty($items)) : ?>
                                                <div class="text-muted">Sin ejercicios.</div>
                                            <?php else : ?>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Orden</th>
                                                                <th>Ejercicio</th>
                                                                <th>Series</th>
                                                                <th>Reps</th>
                                                                <th>Descanso</th>
                                                                <th>Notas</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($items as $it) : ?>
                                                                <tr>
                                                                    <td><?= (int)$it->orden ?></td>
                                                                    <td><?= esc($it->ejercicio_nombre) ?></td>
                                                                    <td><?= esc($it->series ?? '') ?></td>
                                                                    <td><?= esc($it->repeticiones ?? '') ?></td>
                                                                    <td><?= esc($it->descanso_seg ?? '') ?></td>
                                                                    <td><?= esc($it->notas ?? '') ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
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


<?php
$alumnoNombre = trim(($alumno->nombre ?? '') . ' ' . ($alumno->apellido ?? ''));
$baseAlumno = base_url('dashboard/gym/alumno/' . (int) $alumno->id);
?>
<?= $this->extend('layout/dashboard') ?>

<?= $this->section('gym/alumno/entrenamientos') ?>

<?= $this->include('Modulos/gym/partials/assets') ?>

<div class="container-fluid gym-page gym-page--alumno">
    <?= view('Modulos/gym/partials/page_header', [
        'icon' => 'fa-history',
        'title' => 'Historial de entrenamientos',
        'subtitle' => $alumnoNombre,
        'module' => 'alumno',
        'back' => ['url' => base_url('dashboard/gym/alumno/editar/' . (int) $alumno->id), 'label' => 'Ficha del alumno'],
        'primary' => [
            'url' => $baseAlumno . '/comparar',
            'label' => 'Comparar sesiones',
            'icon' => 'fa-columns',
        ],
    ]) ?>

    <?= $this->include('Modulos/gym/partials/flash') ?>

    <div class="gym-section-card">
        <div class="gym-section-card__head">
            <div>
                <h3><i class="fas fa-list"></i> Sesiones registradas</h3>
                <p class="gym-section-card__hint mb-0">Marca dos sesiones de la misma rutina y pulsa Comparar.</p>
            </div>
        </div>
        <div class="gym-section-card__body">
            <?php if (empty($entrenamientos)) : ?>
                <p class="text-muted mb-0">Sin entrenamientos registrados.</p>
            <?php else : ?>
                <form id="gymComparePick" action="<?= esc($baseAlumno . '/comparar') ?>" method="get" class="mb-3">
                    <button type="submit" class="btn gym-btn-primary btn-sm" id="gymBtnCompare" disabled>
                        <i class="fas fa-columns me-1"></i> Comparar seleccionadas
                    </button>
                </form>
                <div class="table-responsive">
                    <table class="table table-sm table-hover gym-table">
                        <thead>
                            <tr>
                                <th style="width:40px"></th>
                                <th>Fecha</th>
                                <th>Rutina</th>
                                <th>Estado</th>
                                <th>Duración</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entrenamientos as $t) :
                                $tid = (int) $t->id;
                                $mins = '';
                                if (!empty($t->iniciado_en) && !empty($t->finalizado_en)) {
                                    $mins = max(0, (int) round((strtotime($t->finalizado_en) - strtotime($t->iniciado_en)) / 60)) . ' min';
                                }
                                $badge = ($t->estado ?? '') === 'completado' ? 'success' : (($t->estado ?? '') === 'en_curso' ? 'warning' : 'secondary');
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input gym-cmp-check"
                                           data-id="<?= $tid ?>"
                                           data-rutina="<?= (int) ($t->rutina_id ?? 0) ?>"
                                           aria-label="Seleccionar sesión <?= esc(date('d/m/Y', strtotime($t->iniciado_en ?? 'now'))) ?>">
                                </td>
                                <td><?= esc(date('d/m/Y H:i', strtotime($t->iniciado_en ?? 'now'))) ?></td>
                                <td><?= esc($t->rutina_nombre ?? '-') ?></td>
                                <td><span class="badge bg-<?= esc($badge) ?>"><?= esc(ucfirst($t->estado ?? '')) ?></span></td>
                                <td><?= esc($mins ?: '-') ?></td>
                                <td class="text-end">
                                    <a href="<?= esc($baseAlumno . '/entrenamiento/' . $tid) ?>" class="btn btn-sm gym-btn-outline">Ver detalle</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
(function () {
    var checks = document.querySelectorAll('.gym-cmp-check');
    var btn = document.getElementById('gymBtnCompare');
    var form = document.getElementById('gymComparePick');
    if (!checks.length || !btn || !form) return;

    function sync() {
        var picked = [];
        checks.forEach(function (c) {
            if (c.checked) picked.push(c);
        });
        btn.disabled = picked.length !== 2;
        if (picked.length === 2 && picked[0].dataset.rutina !== picked[1].dataset.rutina) {
            btn.title = 'Rutinas distintas: la comparación puede ser parcial';
        } else {
            btn.title = '';
        }
    }

    checks.forEach(function (c) {
        c.addEventListener('change', function () {
            var picked = document.querySelectorAll('.gym-cmp-check:checked');
            if (picked.length > 2) {
                this.checked = false;
            }
            sync();
        });
    });

    form.addEventListener('submit', function (e) {
        var picked = document.querySelectorAll('.gym-cmp-check:checked');
        if (picked.length !== 2) {
            e.preventDefault();
            return;
        }
        var a = document.createElement('input');
        a.type = 'hidden';
        a.name = 'a';
        a.value = picked[0].dataset.id;
        var b = document.createElement('input');
        b.type = 'hidden';
        b.name = 'b';
        b.value = picked[1].dataset.id;
        form.appendChild(a);
        form.appendChild(b);
    });
})();
</script>

<?= $this->endSection() ?>

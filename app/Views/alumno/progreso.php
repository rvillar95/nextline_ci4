<?= $this->extend('layout/alumno') ?>

<?= $this->section('head') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="nn-alumno-card">
    <h2>Mi progreso</h2>
    <p class="text-muted mb-0">Últimos 30 días de entrenamiento</p>
</div>

<div class="row g-2 mb-2">
    <div class="col-4">
        <div class="nn-alumno-card nn-stat-card text-center py-3 mb-0">
            <div class="nn-stat-card__value"><?= (int) ($resumen['sesiones'] ?? 0) ?></div>
            <div class="nn-stat-card__label">Sesiones</div>
        </div>
    </div>
    <div class="col-4">
        <div class="nn-alumno-card nn-stat-card text-center py-3 mb-0">
            <div class="nn-stat-card__value"><?= (int) ($racha ?? 0) ?></div>
            <div class="nn-stat-card__label">Racha días</div>
        </div>
    </div>
    <div class="col-4">
        <div class="nn-alumno-card nn-stat-card text-center py-3 mb-0">
            <div class="nn-stat-card__value"><?= number_format((int) ($resumen['volumen'] ?? 0), 0, ',', '.') ?></div>
            <div class="nn-stat-card__label">Volumen kg</div>
        </div>
    </div>
</div>

<div class="nn-alumno-card">
    <h3 class="h6 mb-3"><i class="fas fa-chart-bar text-success me-1"></i> Volumen semanal</h3>
    <?php if (empty($volumenSemanal)) : ?>
        <p class="text-muted small mb-0">Completa entrenamientos con peso y repeticiones para ver el gráfico.</p>
    <?php else : ?>
        <div class="nn-chart-wrap">
            <canvas id="chartVolumen" height="200"></canvas>
        </div>
    <?php endif; ?>
</div>

<div class="nn-alumno-card">
    <h3 class="h6 mb-2"><i class="fas fa-chart-line text-primary me-1"></i> Peso por ejercicio</h3>
    <?php if (empty($ejercicios)) : ?>
        <p class="text-muted small mb-0">Aún no hay ejercicios registrados.</p>
    <?php else : ?>
        <select id="selEjercicioProgreso" class="form-select form-select-sm mb-3">
            <?php foreach ($ejercicios as $ej) : ?>
            <option value="<?= (int) $ej->ejercicio_id ?>" <?= (int) $ejercicioId === (int) $ej->ejercicio_id ? 'selected' : '' ?>>
                <?= esc($ej->nombre_ejercicio) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <div id="prBadge" class="mb-2 small text-success" style="<?= empty($pr) ? 'display:none' : '' ?>">
            <?php if (!empty($pr)) : ?>
                <i class="fas fa-trophy"></i> Récord: <strong><?= esc($pr['peso_kg']) ?> kg</strong>
                <?php if (!empty($pr['repeticiones'])) : ?> × <?= (int) $pr['repeticiones'] ?> reps<?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="nn-chart-wrap">
            <canvas id="chartPeso" height="200"></canvas>
        </div>
        <p id="chartPesoEmpty" class="text-muted small mb-0 mt-2" style="display:none">Sin datos de peso para este ejercicio.</p>
    <?php endif; ?>
</div>

<div class="nn-alumno-card">
    <h3 class="h6 mb-3"><i class="fas fa-trophy text-warning me-1"></i> Récords recientes</h3>
    <?php if (empty($ultimosPr)) : ?>
        <p class="text-muted small mb-0">Al superar tu peso máximo en un ejercicio verás el récord aquí.</p>
    <?php else : ?>
        <ul class="list-group list-group-flush nn-pr-list">
            <?php foreach ($ultimosPr as $prItem) : ?>
            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= esc($prItem->nombre_ejercicio ?? 'Ejercicio') ?></strong>
                    <small class="d-block text-muted"><?= date('d/m/Y H:i', strtotime($prItem->logrado_en)) ?></small>
                </div>
                <span class="badge bg-success nn-pr-badge">
                    <?= esc($prItem->peso_kg) ?> kg
                    <?php if (!empty($prItem->repeticiones)) : ?> × <?= (int) $prItem->repeticiones ?><?php endif; ?>
                </span>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    var volumenData = <?= json_encode($volumenSemanal ?? [], JSON_UNESCAPED_UNICODE) ?>;
    var historialEj = <?= json_encode($historialEj ?? [], JSON_UNESCAPED_UNICODE) ?>;
    var baseUrl = document.body.dataset.baseUrl || '/';
    var chartVol = null;
    var chartPeso = null;

    function makeBarChart(canvasId, labels, values, label, color) {
        var el = document.getElementById(canvasId);
        if (!el || typeof Chart === 'undefined') return null;
        return new Chart(el, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: values,
                    backgroundColor: color,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    function makeLineChart(canvasId, labels, values) {
        var el = document.getElementById(canvasId);
        if (!el || typeof Chart === 'undefined') return null;
        return new Chart(el, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Peso máx (kg)',
                    data: values,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }

    if (volumenData.length) {
        chartVol = makeBarChart(
            'chartVolumen',
            volumenData.map(function (x) { return x.label; }),
            volumenData.map(function (x) { return x.volumen; }),
            'Volumen (kg)',
            'rgba(123, 193, 67, 0.75)'
        );
    }

    function renderPesoChart(data, pr) {
        var empty = document.getElementById('chartPesoEmpty');
        var badge = document.getElementById('prBadge');
        if (chartPeso) {
            chartPeso.destroy();
            chartPeso = null;
        }
        if (!data || !data.length) {
            if (empty) empty.style.display = '';
            return;
        }
        if (empty) empty.style.display = 'none';
        chartPeso = makeLineChart(
            'chartPeso',
            data.map(function (x) { return x.label; }),
            data.map(function (x) { return x.peso_max; })
        );
        if (badge && pr && pr.peso_kg) {
            var txt = '<i class="fas fa-trophy"></i> Récord: <strong>' + pr.peso_kg + ' kg</strong>';
            if (pr.repeticiones) txt += ' × ' + pr.repeticiones + ' reps';
            badge.innerHTML = txt;
            badge.style.display = '';
        } else if (badge) {
            badge.style.display = 'none';
        }
    }

    renderPesoChart(historialEj, <?= json_encode($pr ?? null) ?>);

    var sel = document.getElementById('selEjercicioProgreso');
    if (sel) {
        sel.addEventListener('change', function () {
            var id = this.value;
            fetch(baseUrl.replace(/\/?$/, '/') + 'alumno/progreso/ejercicio/' + id, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (json && json.ok) {
                        renderPesoChart(json.historial, json.pr);
                    }
                });
        });
    }
})();
</script>
<?= $this->endSection() ?>

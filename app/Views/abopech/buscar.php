<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h3 text-abopech mb-4">Buscar abogado penalista</h1>
<form method="get" action="<?= base_url('abopech/buscar') ?>" class="card card-body shadow-sm mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Región</label>
            <select name="region_id" id="region_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($regiones as $r): ?>
                    <option value="<?= (int) $r['id'] ?>" <?= (int) $region_id === (int) $r['id'] ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Comuna</label>
            <select name="comuna_id" id="comuna_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($comunas as $c): ?>
                    <option value="<?= (int) $c['id'] ?>" <?= (int) $comuna_id === (int) $c['id'] ? 'selected' : '' ?>><?= esc($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Tipo tribunal</label>
            <select name="tipo_tribunal" class="form-select">
                <option value="">Todos</option>
                <option value="garantia" <?= $tipo_tribunal === 'garantia' ? 'selected' : '' ?>>Garantía</option>
                <option value="juicio_oral_penal" <?= $tipo_tribunal === 'juicio_oral_penal' ? 'selected' : '' ?>>Juicio Oral en lo Penal</option>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Tribunal</label>
            <select name="tribunal_id" class="form-select">
                <option value="">Cualquiera</option>
                <?php foreach ($tribunales as $t): ?>
                    <option value="<?= (int) $t['id'] ?>" <?= (int) $tribunal_id === (int) $t['id'] ? 'selected' : '' ?>><?= esc($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-abopech">Buscar</button>
        </div>
    </div>
</form>

<?php if (empty($abogados)): ?>
    <p class="text-muted">No hay abogados publicados con esos filtros.</p>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($abogados as $a): ?>
            <a href="<?= base_url('abopech/abogado/' . (int) $a['id']) ?>" class="list-group-item list-group-item-action">
                <div class="fw-semibold"><?= esc($a['nombres'] . ' ' . $a['apellidos']) ?></div>
                <small class="text-muted">Perfil verificado ABOPECH</small>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.getElementById('region_id')?.addEventListener('change', function () {
    const rid = this.value;
    const comunaSel = document.getElementById('comuna_id');
    if (!rid) { return; }
    fetch('<?= base_url('abopech/buscar/comunas') ?>/' + rid)
        .then(r => r.json())
        .then(items => {
            comunaSel.innerHTML = '<option value="">Todas</option>';
            items.forEach(c => {
                const o = document.createElement('option');
                o.value = c.id;
                o.textContent = c.nombre;
                comunaSel.appendChild(o);
            });
        });
});
</script>
<?= $this->endSection() ?>

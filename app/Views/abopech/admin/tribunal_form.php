<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech">Registrar tribunal</h1>
<form method="post" action="<?= base_url('abopech/admin/tribunales/guardar') ?>" class="card card-body shadow-sm">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Nombre</label><input name="nombre" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
    <div class="mb-3"><label class="form-label">Región</label>
        <select name="region_id" class="form-select" required>
            <?php foreach ($regiones as $r): ?><option value="<?= (int) $r['id'] ?>"><?= esc($r['nombre']) ?></option><?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3"><label class="form-label">Tipo</label>
        <select name="tipo_codigo" class="form-select" required>
            <option value="garantia">Garantía</option>
            <option value="juicio_oral_penal">Juicio Oral en lo Penal</option>
            <option value="otro">Otro</option>
        </select>
    </div>
    <button type="submit" class="btn btn-abopech">Guardar</button>
</form>
<?= $this->endSection() ?>

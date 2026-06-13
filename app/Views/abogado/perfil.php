<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<h1 class="h4 text-abopech mb-3">Mi perfil profesional</h1>
<p class="text-muted">Estado: <strong><?= esc($abogado['estado_perfil']) ?></strong></p>

<form method="post" action="<?= base_url('abopech/mi-perfil/guardar') ?>" class="card card-body shadow-sm mb-4">
    <?= csrf_field() ?>
    <div class="row g-3">
        <div class="col-md-4"><label class="form-label">RUT</label><input name="rut" class="form-control" required value="<?= esc($abogado['rut']) ?>"></div>
        <div class="col-md-4"><label class="form-label">Nombres</label><input name="nombres" class="form-control" required value="<?= esc($abogado['nombres']) ?>"></div>
        <div class="col-md-4"><label class="form-label">Apellidos</label><input name="apellidos" class="form-control" required value="<?= esc($abogado['apellidos']) ?>"></div>
        <div class="col-12"><label class="form-label">Habilidades (máx. 500 palabras)</label><textarea name="habilidades" class="form-control" rows="4" required><?= esc($abogado['habilidades']) ?></textarea></div>
        <div class="col-12"><label class="form-label">Experiencia (máx. 500 palabras)</label><textarea name="experiencia" class="form-control" rows="4" required><?= esc($abogado['experiencia']) ?></textarea></div>
        <div class="col-md-6">
            <label class="form-label">Regiones</label>
            <select name="region_ids[]" id="region_ids" class="form-select" multiple size="6">
                <?php
                $selReg = array_column($perfil['regiones'] ?? [], 'id');
                foreach ($regiones as $r):
                ?>
                    <option value="<?= (int) $r['id'] ?>" <?= in_array((int) $r['id'], $selReg, true) ? 'selected' : '' ?>><?= esc($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Comunas</label>
            <select name="comuna_ids[]" id="comuna_ids" class="form-select" multiple size="6">
                <?php
                $selCom = array_column($perfil['comunas'] ?? [], 'id');
                foreach ($perfil['comunas'] ?? [] as $c):
                ?>
                    <option value="<?= (int) $c['id'] ?>" selected><?= esc($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Tribunales</label>
            <select name="tribunal_ids[]" class="form-select" multiple size="8">
                <?php
                $selTri = array_column($perfil['tribunales'] ?? [], 'id');
                foreach ($tribunales as $t):
                ?>
                    <option value="<?= (int) $t['id'] ?>" <?= in_array((int) $t['id'], $selTri, true) ? 'selected' : '' ?>><?= esc($t['nombre']) ?> (<?= esc($t['tipo_codigo']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12"><button type="submit" class="btn btn-abopech">Guardar perfil</button></div>
    </div>
</form>

<h2 class="h5">Estudios (documento obligatorio)</h2>
<ul class="list-group mb-3">
    <?php foreach ($perfil['estudios'] ?? [] as $e): ?>
        <li class="list-group-item d-flex justify-content-between">
            <span><?= esc($e['nombre']) ?> — <?= esc($e['universidad']) ?></span>
            <form method="post" action="<?= base_url('abopech/mi-perfil/estudio/eliminar/' . (int) $e['id']) ?>" onsubmit="return confirm('¿Eliminar?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Eliminar</button></form>
        </li>
    <?php endforeach; ?>
</ul>

<form method="post" action="<?= base_url('abopech/mi-perfil/estudio') ?>" enctype="multipart/form-data" class="card card-body shadow-sm mb-4">
    <?= csrf_field() ?>
    <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Nombre estudio</label><input name="nombre" class="form-control" required></div>
        <div class="col-md-6"><label class="form-label">Universidad</label><input name="universidad" class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Año</label><input type="number" name="anio_titulacion" class="form-control" required min="1950" max="2100"></div>
        <div class="col-md-4"><label class="form-label">Tipo</label>
            <select name="tipo_estudio_id" class="form-select" required>
                <?php foreach ($tipos_estudio as $te): ?>
                    <option value="<?= (int) $te['id'] ?>"><?= esc($te['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4"><label class="form-label">Documento PDF</label><input type="file" name="documento" class="form-control" required accept=".pdf,.doc,.docx,image/*"></div>
        <div class="col-12"><label class="form-label">Descripción</label><textarea name="descripcion" class="form-control" rows="2"></textarea></div>
        <div class="col-12"><button type="submit" class="btn btn-outline-primary">Agregar estudio</button></div>
    </div>
</form>

<form method="post" action="<?= base_url('abopech/mi-perfil/enviar-revision') ?>">
    <?= csrf_field() ?>
    <button type="submit" class="btn btn-success" <?= ($abogado['estado_perfil'] ?? '') === 'pendiente' ? 'disabled' : '' ?>>Enviar a revisión</button>
</form>
<?= $this->endSection() ?>

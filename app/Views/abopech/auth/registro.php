<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center"><div class="col-md-6">
<h1 class="h4 text-abopech mb-3">Registro abogado penalista</h1>
<form method="post" action="<?= base_url('abopech/auth/registro') ?>">
    <?= csrf_field() ?>
    <div class="row g-2">
        <div class="col-md-6"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required value="<?= esc(old('nombre')) ?>"></div>
        <div class="col-md-6"><label class="form-label">Apellido</label><input type="text" name="apellido" class="form-control" required value="<?= esc(old('apellido')) ?>"></div>
        <div class="col-12"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" required value="<?= esc(old('correo')) ?>"></div>
        <div class="col-12"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" required value="<?= esc(old('telefono')) ?>"></div>
        <div class="col-12"><label class="form-label">Contraseña</label><input type="password" name="clave" class="form-control" required minlength="8"></div>
        <div class="col-12"><div class="form-check"><input type="checkbox" name="consentimiento" value="1" class="form-check-input" required id="c"><label for="c" class="form-check-label">Acepto términos y tratamiento de datos</label></div></div>
        <div class="col-12"><button type="submit" class="btn btn-abopech">Registrarme</button></div>
    </div>
</form>
</div></div>
<?= $this->endSection() ?>

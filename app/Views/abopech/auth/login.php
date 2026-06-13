<?= $this->extend('layout/abopech') ?>
<?= $this->section('content') ?>
<div class="row justify-content-center"><div class="col-md-5">
<h1 class="h4 text-abopech mb-3">Ingreso abogados</h1>
<form method="post" action="<?= base_url('abopech/auth/login') ?>">
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Contraseña</label><input type="password" name="clave" class="form-control" required></div>
    <button type="submit" class="btn btn-abopech w-100 mb-2">Ingresar</button>
    <a href="<?= base_url('abopech/auth/google') ?>" class="btn btn-outline-secondary w-100 mb-2">Continuar con Google</a>
    <a href="<?= base_url('abopech/auth/registro') ?>">Crear cuenta</a>
</form>
</div></div>
<?= $this->endSection() ?>

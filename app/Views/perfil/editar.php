<?php $this->extend('layout/dashboard') ?>
<?php
//print_r($perfil);
//exit();
?>
<?= $this->section("perfil/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle Perfil</h4>
            </div>
        </div>
        <form method="POST" action="<?= base_url('dashboard/perfil/update'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $perfil['nombre']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $perfil['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php $perfil['estado']; ?>">
                            <option value="A" <?= $perfil['estado'] == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $perfil['estado'] == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <?php if (session()->getFlashdata('success') !== null) : ?>
                    <div class="alert alert-success my-3" role="alert">
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>
                <div class="col-12">
                    <div class="mb-4">
                        <button type="submit" class="btn btn-secondary w-100">Editar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
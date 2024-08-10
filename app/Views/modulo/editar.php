<?php $this->extend('layout/dashboard') ?>
<?php
//print_r($perfil);
//exit();
?>
<?= $this->section("modulo/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle Modulo</h4>
            </div>
        </div>
        <form method="POST" action="<?= base_url('dashboard/modulo/update'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $modulo['nombre']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $modulo['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <input type="text" id="descripcion" name="descripcion" value="<?= $modulo['descripcion']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $modulo['descripcion']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcion']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Ruta</label>
                        <input type="text" id="nombre" name="ruta" value="<?= $modulo['ruta']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $modulo['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['ruta'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['ruta']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php $modulo['estado']; ?>">
                            <option value="A" <?= $modulo['estado'] == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $modulo['estado'] == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Desea que se muestre?</label>
                        <select class="form-select" id="mostrar" name="mostrar" value="<?php $modulo['mostrar']; ?>">
                            <option value="S" <?= $modulo['mostrar'] == 'S' ? 'selected' : '' ?>>Si</option>
                            <option value="N" <?= $modulo['mostrar'] == 'N' ? 'selected' : '' ?>>No</option>
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
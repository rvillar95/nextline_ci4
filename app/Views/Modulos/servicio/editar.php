<?php $this->extend('layout/dashboard') ?>
<?php
//print_r($perfil);
//exit();
?>
<?= $this->section("servicio/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle de Servicio</h4>
            </div>
        </div>
        <!--form method="POST" action="<?= base_url('dashboard/servicio/update'); ?>"-->
        <?= form_open_multipart('dashboard/servicio/update') ?>
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $servicio['nombre']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $servicio['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción Corta</label>
                        <input type="text" id="descripcionCorta" name="descripcionCorta" value="<?= $servicio['descripcionCorta']; ?>" class="form-control" autofocus>

                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcionCorta'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcionCorta']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción Larga</label>
                        <textarea id="descripcionLarga" name="descripcionLarga" class="form-control" autofocus rows="5"><?= $servicio['descripcionLarga']; ?></textarea>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcionLarga'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcionLarga']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Valor</label>
                        <input type="number" id="valor" name="valor" value="<?= $servicio['valor']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['valor'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['valor']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <img src='<?= base_url() . $servicio['foto']; ?>' style='max-width: 100%; height: auto; text-align: center;' />
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Imagen</label>
                        <input type="file" id="img" name="img" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['img'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['img']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php $servicio['estado']; ?>">
                            <option value="A" <?= $servicio['estado'] == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $servicio['estado'] == 'I' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <?php if (session()->getFlashdata('success') !== null) : ?>
                    <div class="alert alert-success my-3" role="alert">
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors') !== null) : ?>
                    <p style="color:red; font-weight:bold;">
                        <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                            <?= session()->getFlashdata('errors'); ?>
                        <?php endif; ?>
                    </p>
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
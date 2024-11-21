<?php $this->extend('layout/dashboard') ?>
<?php
//print_r($perfil);
//exit();
?>
<?= $this->section("galeria/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle de Galeria</h4>
            </div>
        </div>
        <?= form_open_multipart('dashboard/galeria/update') ?>
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $galeria['nombre']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $galeria['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" autofocus rows="5"><?= $galeria['descripcion']; ?></textarea>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcion']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" value="<?= isset($galeria['fecha']) ? date('Y-m-d', strtotime($galeria['fecha'])) : ''; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['fecha'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['fecha']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <img src='<?= base_url() . $galeria['portada']; ?>' style='max-width: 100%; height: auto; text-align: center;' />
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Portada</label>
                        <input type="file" id="portada" name="portada" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['portada'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['portada']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php $galeria['estado']; ?>">
                            <option value="A" <?= $galeria['estado'] == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $galeria['estado'] == 'I' ? 'selected' : '' ?>>Inactivo</option>
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
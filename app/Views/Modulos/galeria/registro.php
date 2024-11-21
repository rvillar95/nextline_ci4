<?php $this->extend('layout/dashboard') ?>

<?= $this->section("galeria/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Galeria</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">
                    <div class="form-group">
                    <?= form_open_multipart('dashboard/galeria/registrar') ?>
                            <?= csrf_field(); ?>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" value="<?= set_value('nombre'); ?>" class="form-control" autofocus>
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
                                    <textarea type="text" id="descripcion" name="descripcion" class="form-control" autofocus rows="5"><?= set_value('descripcion'); ?></textarea>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['descripcion']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Portada</label>
                                    <input type="file" id="portada" name="portada" value="<?= set_value('portada'); ?>" class="form-control" autofocus >
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['portada'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['portada']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="datetime-local" id="fecha" name="fecha" value="<?= set_value('fecha'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['fecha'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['fecha']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Seleccione el Estado</label>
                                    <select class="form-select" id="estado" name="estado" value="<?php set_value('estado'); ?>">
                                        <option value="A">Activo</option>
                                        <option value="I">Inactivo</option>
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
                                    <button type="submit" class="btn btn-secondary w-100">Crear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
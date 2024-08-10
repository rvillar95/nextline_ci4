<?php $this->extend('layout/dashboard') ?>

<?= $this->section("modulo/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Modulo</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                        <form method="POST" action="<?= base_url('dashboard/modulo/registrar'); ?>">
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
                                    <label class="form-label">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion" value="<?= set_value('descripcion'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['descripcion']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Ruta</label>
                                    <input type="text" id="ruta" name="ruta" value="<?= set_value('ruta'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['ruta'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['ruta']; ?>
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Desea que se muestre?</label>
                                    <select class="form-select" id="mostrar" name="mostrar" value="<?php set_value('mostrar'); ?>">
                                            <option value="S">Si</option>
                                            <option value="N">No</option>
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

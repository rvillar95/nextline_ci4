<?php $this->extend('layout/dashboard') ?>

<?= $this->section("servicio/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Servicio</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                    <?= form_open_multipart('dashboard/servicio/registrar') ?>
                        <!--form method="POST" action="<?= base_url('dashboard/servicio/registrar'); ?>"-->
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
                                    <label class="form-label">Descripción Corta</label>
                                    <input type="text" id="descripcionCorta" name="descripcionCorta" value="<?= set_value('descripcionCorta'); ?>" class="form-control" autofocus>
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
                                    <textarea type="text" id="descripcionLarga" name="descripcionLarga" class="form-control" autofocus rows="5"><?= set_value('descripcionLarga'); ?></textarea>
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
                                    <input placeholder="$ 10.000" type="number" id="valor" name="valor" value="<?= set_value('valor'); ?>" class="form-control" autofocus >
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['valor'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['valor']; ?>
                                    <p>
                                    <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Imagen</label>
                                    <input type="file" id="img" name="img" value="<?= set_value('img'); ?>" class="form-control" autofocus >
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
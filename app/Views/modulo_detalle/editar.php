<?php $this->extend('layout/dashboard') ?>

<?php
//exit();
?>

<?= $this->section("modulo_detalle/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle Modulo Detalle</h4>
            </div>
        </div>
        <form method="POST" action="<?= base_url('dashboard/modulo-detalle/update'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" value="<?= $perfil['id']; ?>" class="form-control" autofocus>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Modulo</label>
                        <select class="form-select" id="modulo" name="modulo">
                            <?php foreach ($modulos as $modulo) : ?>
                                <option value="<?= esc($modulo['id']) ?>" <?= set_select('modulo', $modulo['id'], $perfil['modulo_id'] == $modulo['id']); ?>>
                                    <?= esc($modulo['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset(session()->getFlashdata('errors')['modulo'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['modulo']; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" value="<?= $perfil['descripcion']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['descripcion'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['descripcion']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Ruta</label>
                        <input type="text" id="ruta" name="ruta" value="<?= $perfil['ruta']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['ruta'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['ruta']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Acción</label>
                        <input type="text" id="accion" name="accion" value="<?= $perfil['accion']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['accion'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['accion']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Orden</label>
                        <input type="text" id="orden" name="orden" value="<?= $perfil['orden']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['orden'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['orden']; ?>
                        </p>
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Desea que se muestre?</label>
                        <select class="form-select" id="mostrar" name="mostrar" value="<?php $perfil['mostrar']; ?>">
                            <option value="S" <?= $perfil['mostrar'] == 'S' ? 'selected' : '' ?>>Si</option>
                            <option value="N" <?= $perfil['mostrar'] == 'N' ? 'selected' : '' ?>>No</option>
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
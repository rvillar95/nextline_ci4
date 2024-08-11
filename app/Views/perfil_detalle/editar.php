<?php $this->extend('layout/dashboard') ?>

<?php
//echo "<pre>";
//print_r($perfil);
//echo "</pre>";
//exit();
?>

<?= $this->section("perfil_detalle/detalle") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle Detalle Perfil</h4>
            </div>
        </div>
        <form method="POST" action="<?= base_url('dashboard/perfil-detalle/update'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" value="<?= $perfil['id']; ?>" class="form-control" autofocus>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Perfil</label>
                        <select class="form-select" id="perfil" name="perfil">
                            <?php foreach ($perfiles as $per) : ?>
                                <option value="<?= esc($per['id']) ?>" <?= set_select('perfil', $per['id'], $perfil['perfil_id'] == $per['id']); ?>>
                                    <?= esc($per['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset(session()->getFlashdata('errors')['perfil'])) : ?>
                            <p style="color:red; font-weight:bold;">
                                <?= session()->getFlashdata('errors')['perfil']; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
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
                    <div class="mb-3 form-check form-check-primary form-check-inline">
                        <label class="form-check-label">Ver</label> <br>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="ver" value="1" id="form-check-radio-default-checked" <?= $perfil['ver'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default-checked">
                                Si
                            </label>
                        </div>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="ver" value="0" id="form-check-radio-default" <?= $perfil['ver'] == 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default">
                                No
                            </label>
                        </div>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['ver'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['ver']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 form-check form-check-primary form-check-inline">
                        <label class="form-check-label">Registrar</label> <br>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="registrar" value="1" id="form-check-radio-default-checked" <?= $perfil['registrar'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default-checked">
                                Si
                            </label>
                        </div>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="registrar" value="0" id="form-check-radio-default" <?= $perfil['registrar'] == 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default">
                                No
                            </label>
                        </div>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['registrar'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['registrar']; ?>
                        <p>
                        <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <div class="mb-3 form-check form-check-primary form-check-inline">
                        <label class="form-check-label">Editar</label> <br>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="editar" value="1" id="form-check-radio-default-checked" <?= $perfil['editar'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default-checked">
                                Si
                            </label>
                        </div>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="editar" value="0" id="form-check-radio-default" <?= $perfil['editar'] == 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default">
                                No
                            </label>
                        </div>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['editar'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['editar']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 form-check form-check-primary form-check-inline">
                        <label class="form-check-label">Eliminar</label> <br>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="eliminar" value="1" id="form-check-radio-default-checked" <?= $perfil['eliminar'] == 1 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default-checked">
                                Si
                            </label>
                        </div>
                        <div class="form-check form-check-primary form-check-inline">
                            <input class="form-check-input" type="radio" name="eliminar" value="0" id="form-check-radio-default" <?= $perfil['eliminar'] == 0 ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="form-check-radio-default">
                                No
                            </label>
                        </div>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['eliminar'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['eliminar']; ?>
                        <p>
                        <?php endif; ?>
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
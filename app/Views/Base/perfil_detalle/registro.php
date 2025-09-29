<?php $this->extend('layout/dashboard') ?>

<?= $this->section("perfil_detalle/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de Perfil Detalle</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                        <form method="POST" action="<?= base_url('dashboard/perfil-detalle/registrar'); ?>">
                            <?= csrf_field(); ?>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Seleccione el Perfil</label>
                                    <select class="form-select" id="perfil" name="perfil">
                                        <option value="">Seleccionar perfil</option>
                                        <?php foreach ($perfiles as $perfil) : ?>
                                            <option value="<?= esc($perfil['id']) ?>" <?= old('perfil') == $perfil['id'] ? 'selected' : '' ?>><?= esc($perfil['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['perfil'])) : ?>
                                        <p style="color:red; font-weight:bold;">
                                            <?= session()->getFlashdata('errors')['perfil']; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Seleccione el Modulo</label>
                                    <select class="form-select" id="modulo" name="modulo">
                                        <option value="">Seleccionar módulo</option>
                                        <?php foreach ($modulos as $modulo) : ?>
                                            <option value="<?= esc($modulo['id']) ?>" <?= old('modulo') == $modulo['id'] ? 'selected' : '' ?>><?= esc($modulo['nombre']) ?> (<?= esc($modulo['descripcion']) ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset(session()->getFlashdata('errors')['modulo'])) : ?>
                                        <p style="color:red; font-weight:bold;">
                                            <?= session()->getFlashdata('errors')['modulo']; ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-12">

                                <div class="mb-3 form-check form-check-primary form-check-inline">
                                    <label class="form-check-label">Ver</label> <br>
                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="ver" value="1" id="ver_si" <?= old('ver', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="ver_si">
                                            Si
                                        </label>
                                    </div>

                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="ver" value="0" id="ver_no" <?= old('ver', '1') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="ver_no">
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
                            <div class="col-md-12">

                                <div class="mb-3 form-check form-check-primary form-check-inline">
                                    <label class="form-check-label">Registrar</label> <br>
                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="registrar" value="1" id="registrar_si" <?= old('registrar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registrar_si">
                                            Si
                                        </label>
                                    </div>

                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="registrar" value="0" id="registrar_no" <?= old('registrar', '1') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="registrar_no">
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
                            <div class="col-md-12">
                                <div class="mb-3 form-check form-check-primary form-check-inline">
                                    <label class="form-check-label">Editar</label> <br>
                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="editar" value="1" id="editar_si" <?= old('editar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="editar_si">
                                            Si
                                        </label>
                                    </div>

                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="editar" value="0" id="editar_no" <?= old('editar', '1') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="editar_no">
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
                            <div class="col-md-12">
                                <div class="mb-3 form-check form-check-primary form-check-inline">
                                    <label class="form-check-label">Eliminar</label> <br>
                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="eliminar" value="1" id="eliminar_si" <?= old('eliminar', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eliminar_si">
                                            Si
                                        </label>
                                    </div>

                                    <div class="form-check form-check-primary form-check-inline">
                                        <input class="form-check-input" type="radio" name="eliminar" value="0" id="eliminar_no" <?= old('eliminar', '1') == '0' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eliminar_no">
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
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Orden</label>
                                    <input type="number" min="0" id="orden" name="orden" value="<?= old('orden', '0'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['orden'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['orden']; ?>
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
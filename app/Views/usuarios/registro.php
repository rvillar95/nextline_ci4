<?php $this->extend('layout/dashboard') ?>

<?= $this->section("usuario/registro") ?>

<div id="basic" class="col-lg-12 layout-spacing">
    <div class="statbox widget box box-shadow">
        <div class="widget-header">
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                    <div class="mb-3">
                        <h4>Registro de usuarios</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="widget-content widget-content-area">

            <div class="row">
                <div class="col-lg-12 col-12 ">

                    <div class="form-group">
                        <form method="POST" action="<?= base_url('dashboard/usuario/registrar'); ?>">
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
                                    <label class="form-label">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" value="<?= set_value('apellido'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['apellido'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['apellido']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Correo</label>
                                    <input type="email" id="correo" name="correo" value="<?= set_value('correo'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['correo'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['correo']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Telefono</label>
                                    <input type="text" id="telefono" name="telefono" value="<?= set_value('telefono'); ?>" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['telefono'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['telefono']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Clave</label>
                                    <input type="password" placeholder="********" id="clave" name="clave" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['clave'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['clave']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Repita Clave</label>
                                    <input type="password" placeholder="********" id="reclave" name="reclave" class="form-control" autofocus>
                                </div>
                                <?php if (isset(session()->getFlashdata('errors')['reclave'])) : ?>
                                    <p style="color:red; font-weight:bold;">
                                        <?= session()->getFlashdata('errors')['reclave']; ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Seleccione el Perfil</label>
                                    <select class="form-select" id="perfil" name="perfil" value="<?php set_value('perfil'); ?>">
                                        <?php foreach ($perfiles as $perfil) : ?>
                                            <option value="<?= esc($perfil['id']) ?>"><?= esc($perfil['nombre']) ?></option>
                                        <?php endforeach; ?>
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

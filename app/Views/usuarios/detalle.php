<?php $this->extend('layout/dashboard') ?>
<?php
print_r($usuario);
//exit();
?>
<?= $this->section("detalle_usuario") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Detalle Usuario</h4>
            </div>
        </div>
        <form method="POST" action="<?= base_url('dashboard/usuario/editar'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" value="<?= $usuario['nombre']; ?>" class="form-control" autofocus>
                        <input type="hidden" id="id" name="id" value="<?= $usuario['id']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['nombre'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['nombre']; ?>
                        <p>
                        <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="apellido" name="apellido" value="<?= $usuario['apellido']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['apellido'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['apellido']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" id="correo" name="correo" value="<?= $usuario['correo']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['correo'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['correo']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" id="telefono" name="telefono" value="<?= $usuario['telefono']; ?>" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errors')['telefono'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errors')['telefono']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Perfil</label>
                        <select class="form-select" id="perfil" name="perfil" value="<?php $usuario['nombre_perfil']; ?>">
                            <?php foreach ($perfiles as $perfil) : ?>
                                <option value="<?= esc($perfil['id']) ?>" <?= $perfil['id'] == $usuario['perfil_id'] ? 'selected' : '' ?>><?= esc($perfil['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Seleccione el Estado</label>
                        <select class="form-select" id="estado" name="estado" value="<?php $usuario['estado']; ?>">
                            <option value="A" <?= $usuario['estado'] == 'A' ? 'selected' : '' ?>>Activo</option>
                            <option value="I" <?= $usuario['estado'] == 'I' ? 'selected' : '' ?>>Inactivo</option>
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

        <form method="POST" action="<?= base_url('dashboard/usuario/editar/clave'); ?>">
            <div class="row">
                <?= csrf_field(); ?>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Clave</label>
                        <input type="hidden" id="id" name="id" value="<?= $usuario['id']; ?>" class="form-control" autofocus>
                        <input type="password" placeholder="********" id="clave" name="clave"  class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errorsPassword')['clave'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errorsPassword')['clave']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Repita Clave</label>
                        <input type="password" placeholder="********" id="reclave" name="reclave" class="form-control" autofocus>
                    </div>
                    <?php if (isset(session()->getFlashdata('errorsPassword')['reclave'])) : ?>
                        <p style="color:red; font-weight:bold;">
                            <?= session()->getFlashdata('errorsPassword')['reclave']; ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php if (session()->getFlashdata('successPassword') !== null) : ?>
                    <div class="alert alert-success my-3" role="alert">
                        <?= session()->getFlashdata('successPassword'); ?>
                    </div>
                <?php endif; ?>
                <div class="col-12">
                    <div class="mb-4">
                        <button type="submit" class="btn btn-secondary w-100">Editar Clave</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
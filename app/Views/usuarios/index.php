<?php $this->extend('layout/dashboard') ?>

<?= $this->section("registro") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Registro de usuarios</h4>
            </div>
        </div>
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
<?= $this->endSection() ?>

<?= $this->section("table") ?>
<div class="row">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
        <div class="col-md-12">
            <div class="mb-3">
                <h4>Lista de usuarios</h4>
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Apellido</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Perfil</th>
                            <th scope="col">Creación</th>
                            <th class="text-center" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios[0] as $usuario) : ?>
                            <tr>
                                <td><?= $usuario['nombre'] ?></td>
                                <td><?= $usuario['apellido'] ?></td>
                                <td><?= $usuario['correo'] ?></td>
                                <td><?= $usuario['telefono'] ?></td>
                                <td><?= $usuario['nombre_perfil'] ?></td>
                                <td><?= $usuario['fcreacion'] ?></td>
                                <td class="text-center">
                                    <div class="action-btns">
                                        <a href="<?= base_url('dashboard/usuario/detalle/'. $usuario['id'])?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <a href="javascript:void(0);" class="action-btn btn-edit bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2">
                                                <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                            </svg>
                                        </a>
                                        <a href="javascript:void(0);" class="action-btn btn-delete bs-tooltip" data-toggle="tooltip" data-placement="top" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
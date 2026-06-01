<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Registro - Alumno</title>
    <link rel="icon" type="image/png" href="<?= base_url('lib/logo/icono-transparente.png') ?>" />

    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="<?= base_url("lib/src/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/light/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/src/assets/css/light/authentication/auth-boxed.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/dark/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/src/assets/css/dark/authentication/auth-boxed.css") ?>" rel="stylesheet" type="text/css" />
</head>

<body class="form">
    <div class="auth-container d-flex">
        <div class="container mx-auto align-self-center">
            <div class="row">
                <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-9 col-12 d-flex flex-column align-self-center mx-auto">
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" alt="VitaSync" style="max-height: 64px; width: auto;">
                            </div>
                            <h2>Crear cuenta</h2>
                            <p>Registro de alumno</p>

                            <?php if (session()->getFlashdata('errors') !== null) : ?>
                                <div class="alert alert-danger my-3" role="alert">
                                    <?php if (!is_array(session()->getFlashdata('errors'))) : ?>
                                        <?= session()->getFlashdata('errors'); ?>
                                    <?php else : ?>
                                        <ul class="mb-0">
                                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="<?= base_url('registro'); ?>" class="row g-3">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="empresa_id" value="<?= (int)($empresa_id ?? 0) ?>">

                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" name="nombre" class="form-control" value="<?= esc(old('nombre') ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellido</label>
                                    <input type="text" name="apellido" class="form-control" value="<?= esc(old('apellido') ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Correo</label>
                                    <input type="email" name="correo" class="form-control" value="<?= esc(old('correo') ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control" value="<?= esc(old('telefono') ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Clave</label>
                                    <input type="password" name="clave" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-secondary w-100">Crear cuenta</button>
                                </div>
                            </form>

                            <div class="col-12 mt-4">
                                <div class="seperator">
                                    <hr>
                                    <div class="seperator-text"><span>O continuar con</span></div>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <a class="btn btn-social-login w-100" href="<?= base_url('auth/google') . '?empresa_id=' . (int)($empresa_id ?? 0) ?>">
                                    <span class="btn-text-inner">Google</span>
                                </a>
                            </div>

                            <div class="col-12 mt-4 text-center">
                                <p class="mb-0">¿Ya tienes cuenta? <a href="<?= base_url('login') ?>" class="text-warning">Iniciar sesión</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url("lib/src/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
</body>

</html>


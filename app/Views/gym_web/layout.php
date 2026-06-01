<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Gimnasio') ?></title>

    <!-- Canvas assets (pegar en public/lib/canvas-gym) -->
    <!-- Ejemplo:
    <link rel="stylesheet" href="<?= base_url('lib/canvas-gym/css/style.css') ?>">
    -->

    <!-- Fallback: Bootstrap del proyecto para que la vista sea usable -->
    <link href="<?= base_url("lib/src/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet" type="text/css" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('gym') . ($empresa_id ? '?empresa_id=' . (int)$empresa_id : '') ?>">
                <?= esc($empresa->nombre ?? 'Gimnasio') ?>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('registro') . ($empresa_id ? '?empresa_id=' . (int)$empresa_id : '') ?>">Registro</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?= $this->renderSection('content') ?>

    <script src="<?= base_url("lib/src/bootstrap/js/bootstrap.bundle.min.js") ?>"></script>
    <!-- Canvas JS (cuando se agreguen assets) -->
</body>
</html>


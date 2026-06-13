<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'ABOPECH | Abogados Penalistas de Chile') ?></title>
    <link href="<?= base_url('lib/css/bootstrap.min.css') ?>" rel="stylesheet">
    <style>
        :root { --abopech-blue: #003893; --abopech-red: #D52B1E; }
        .navbar-abopech { background: var(--abopech-blue); }
        .navbar-abopech .navbar-brand, .navbar-abopech .nav-link { color: #fff !important; }
        .btn-abopech { background: var(--abopech-blue); color: #fff; border: none; }
        .btn-abopech:hover { background: #002a6d; color: #fff; }
        .text-abopech { color: var(--abopech-blue); }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-abopech mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('abopech') ?>">ABOPECH</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="<?= base_url('abopech/buscar') ?>">Buscar abogado</a>
            <?php if (abopech_usuario()): ?>
                <?php if ((abopech_usuario()['tipo_cuenta'] ?? '') === 'administrador'): ?>
                    <a class="nav-link" href="<?= base_url('abopech/admin') ?>">Admin</a>
                <?php else: ?>
                    <a class="nav-link" href="<?= base_url('abopech/mi-perfil') ?>">Mi perfil</a>
                <?php endif; ?>
                <a class="nav-link" href="<?= base_url('abopech/auth/logout') ?>">Salir</a>
            <?php else: ?>
                <a class="nav-link" href="<?= base_url('abopech/auth/login') ?>">Ingresar</a>
                <a class="nav-link" href="<?= base_url('abopech/auth/registro') ?>">Registro abogado</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container pb-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?= $this->renderSection('content') ?>
</main>
<footer class="border-top py-4 mt-auto">
    <div class="container text-center text-muted small">
        ABOPECH — Abogados Penalistas de Chile. Red exclusiva de abogados con más de 1.000 audiencias.
    </div>
</footer>
<script src="<?= base_url('lib/js/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>

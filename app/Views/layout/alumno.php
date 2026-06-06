<?php
$usuario = session()->get('usuario');
$navActive = $navActive ?? 'inicio';
$base = rtrim(base_url(), '/');
$flashError = session()->getFlashdata('errors');
$flashSuccess = session()->getFlashdata('success');
if (is_array($flashError)) {
    $flashError = implode(', ', $flashError);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= esc($pageTitle ?? 'Mi entrenamiento') ?> · NutriNext</title>
    <link rel="icon" type="image/png" href="<?= base_url('lib/logo/isotipo.png') ?>" />
    <link href="<?= base_url('lib/src/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="<?= base_url('lib/css/nutrinext-alumno.css') ?>" rel="stylesheet">
    <?= $this->renderSection('head') ?>
</head>
<body class="nn-alumno-body<?= !empty($hideBottomNav) ? ' nn-alumno-body--no-nav' : '' ?>"
      data-base-url="<?= esc($base) ?>/"
      data-flash-error="<?= esc($flashError ?? '', 'attr') ?>"
      data-flash-success="<?= esc($flashSuccess ?? '', 'attr') ?>">
    <header class="nn-alumno-topbar">
        <a href="<?= base_url('alumno/inicio') ?>" class="nn-alumno-topbar__brand">
            <img src="<?= base_url('lib/logo/logo-horizontal.png') ?>" alt="NutriNext" height="32">
        </a>
        <div class="nn-alumno-topbar__user dropdown">
            <button class="btn btn-link text-dark dropdown-toggle p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= esc(trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? ''))) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= base_url('alumno/suscripcion') ?>">Suscripción</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">Cerrar sesión</a></li>
            </ul>
        </div>
    </header>

    <main class="nn-alumno-main">
        <?= $this->renderSection('content') ?>
    </main>

    <?php if (empty($hideBottomNav)) : ?>
    <nav class="nn-alumno-bottomnav" aria-label="Navegación principal">
        <a href="<?= base_url('alumno/inicio') ?>" class="nn-alumno-bottomnav__item <?= $navActive === 'inicio' ? 'is-active' : '' ?>">
            <i class="fas fa-home"></i><span>Inicio</span>
        </a>
        <a href="<?= base_url('alumno/progreso') ?>" class="nn-alumno-bottomnav__item <?= $navActive === 'progreso' ? 'is-active' : '' ?>">
            <i class="fas fa-chart-line"></i><span>Progreso</span>
        </a>
        <a href="<?= base_url('alumno/historial') ?>" class="nn-alumno-bottomnav__item <?= $navActive === 'historial' ? 'is-active' : '' ?>">
            <i class="fas fa-history"></i><span>Historial</span>
        </a>
        <?php
        $enCurso = null;
        if (!empty($usuario['id'])) {
            $enCurso = (new \App\Services\Gym\EntrenamientoService())->sesionEnCurso((int) $usuario['id']);
        }
        ?>
        <?php if ($enCurso) : ?>
        <a href="<?= base_url('alumno/entrenamiento/' . (int) $enCurso->id) ?>" class="nn-alumno-bottomnav__item nn-alumno-bottomnav__item--cta <?= $navActive === 'entrenar' ? 'is-active' : '' ?>">
            <i class="fas fa-dumbbell"></i><span>Continuar</span>
        </a>
        <?php else : ?>
        <span class="nn-alumno-bottomnav__item nn-alumno-bottomnav__item--muted">
            <i class="fas fa-dumbbell"></i><span>Entrenar</span>
        </span>
        <?php endif; ?>
    </nav>
    <?php endif; ?>

    <?= view('components/modals') ?>

    <script src="<?= base_url('lib/src/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/nutrinext-csrf.js') ?>"></script>
    <script src="<?= base_url('lib/js/modals.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>

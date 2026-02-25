<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title>Dashboard VitaSync</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="<?= base_url("lib/logo/icono-transparente.png") ?>" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/light/loader.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/dark/loader.css") ?>" rel="stylesheet" type="text/css" />
    <script src="<?= base_url("lib/layouts/vertical-light-menu/loader.js") ?>"></script>

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="<?= base_url("lib/src/bootstrap/css/bootstrap.min.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/light/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/layouts/vertical-light-menu/css/dark/plugins.css") ?>" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <link href="<?= base_url("lib/src/plugins/src/animate/animate.css") ?>" rel="stylesheet" type="text/css" />

    <link href="<?= base_url("lib/src/assets/css/light/scrollspyNav.css") ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url("lib/src/assets/css/light/components/carousel.css") ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url("lib/src/assets/css/light/components/modal.css") ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url("lib/src/assets/css/light/components/tabs.css") ?>" rel="stylesheet" type="text/css">



    <!-- <link href="<?= base_url("lib/src/plugins/src/table/datatable/datatables.css") ?>" rel="stylesheet" type="text/css"> -->
    <!-- <link href="<?= base_url("lib/src/plugins/css/light/table/datatable/dt-global_style.css") ?>" rel="stylesheet" type="text/css"> -->
    <!-- <link href="<?= base_url("lib/src/plugins/css/light/table/datatable/custom_dt_miscellaneous.css") ?>" rel="stylesheet" type="text/css"> -->
    <!-- <link href="<?= base_url("lib/src/plugins/css/dark/table/datatable/dt-global_style.css") ?>" rel="stylesheet" type="text/css"> --> 
    <!-- <link href="<?= base_url("lib/src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css") ?>" rel="stylesheet" type="text/css"> -->
    
<!--     <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/dt-global_style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/custom_dt_miscellaneous.css">

    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/dt-global_style.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css"> -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="<?= base_url("lib/src/plugins/src/apex/apexcharts.css") ?>" rel="stylesheet" type="text/css">
    <link href="<?= base_url("lib/src/assets/css/light/dashboard/dash_1.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/src/assets/css/dark/dashboard/dash_1.css") ?>" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    
    <!-- CSS y JS para selector de iconos -->
    <link href="<?= base_url("lib/css/icon-selector.css") ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url("lib/css/buttons-ripple-fix.css") ?>" rel="stylesheet" type="text/css" />
    
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script> -->
    <script src="<?= base_url("lib/js/jquery-2.1.1.js") ?> "></script>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
    
    <?php
    $usuarioHeader = session()->get('usuario') ?? [];
    $temaUsuario = $usuarioHeader['tema'] ?? 'claro';
    $colorPrimario = $usuarioHeader['color_primario'] ?? '#4dcba5';
    $cardHeaderPorDefecto = (int)($usuarioHeader['card_header_por_defecto'] ?? 1);
    $cardHeaderEsGradiente = (int)($usuarioHeader['card_header_es_gradiente'] ?? 0);
    $colorCardHeaderBg = $usuarioHeader['color_card_header_bg'] ?? '#6c757d';
    $colorCardHeaderBg2 = $usuarioHeader['color_card_header_bg2'] ?? null;
    $colorCardHeaderText = $usuarioHeader['color_card_header_text'] ?? '#ffffff';
    $mainHeaderPorDefecto = (int)($usuarioHeader['main_header_por_defecto'] ?? 1);
    $mainHeaderEsGradiente = (int)($usuarioHeader['main_header_es_gradiente'] ?? 0);
    $colorMainHeaderBg = $usuarioHeader['color_main_header_bg'] ?? '#4dcba5';
    $colorMainHeaderBg2 = $usuarioHeader['color_main_header_bg2'] ?? null;
    $colorMainHeaderText = $usuarioHeader['color_main_header_text'] ?? '#ffffff';
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $colorPrimario)) { $colorPrimario = '#4dcba5'; }
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $colorCardHeaderBg)) { $colorCardHeaderBg = '#6c757d'; }
    if ($colorCardHeaderBg2 !== null && !preg_match('/^#[a-fA-F0-9]{6}$/', $colorCardHeaderBg2)) { $colorCardHeaderBg2 = '#495057'; }
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $colorCardHeaderText)) { $colorCardHeaderText = '#ffffff'; }
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $colorMainHeaderBg)) { $colorMainHeaderBg = '#4dcba5'; }
    if ($colorMainHeaderBg2 !== null && !preg_match('/^#[a-fA-F0-9]{6}$/', $colorMainHeaderBg2)) { $colorMainHeaderBg2 = '#bee6db'; }
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $colorMainHeaderText)) { $colorMainHeaderText = '#ffffff'; }
    $hex = ltrim($colorCardHeaderBg, '#');
    $dr = max(0, (int)round(hexdec(substr($hex, 0, 2)) * 0.65));
    $dg = max(0, (int)round(hexdec(substr($hex, 2, 2)) * 0.65));
    $db = max(0, (int)round(hexdec(substr($hex, 4, 2)) * 0.65));
    $colorCardHeaderBgDark = '#' . sprintf('%02x%02x%02x', $dr, $dg, $db);
    $aplicarCardHeaderCustom = ($cardHeaderPorDefecto === 0);
    $aplicarMainHeaderCustom = ($mainHeaderPorDefecto === 0);
    ?>
    <style id="user-theme-css">
        :root {
            --vitasync-primary: #4dcba5;
            --vitasync-bg: #fafeff;
            --vitasync-muted: #bee6db;
            --user-primary: <?= esc($colorPrimario) ?>;
            --user-primary-hover: <?= esc($colorPrimario) ?>dd;
            <?php if ($aplicarCardHeaderCustom): ?>
            --user-card-header-bg: <?= esc($colorCardHeaderBg) ?>;
            --user-card-header-bg-dark: <?= esc($colorCardHeaderBgDark) ?>;
            <?php if ($cardHeaderEsGradiente && $colorCardHeaderBg2): ?>--user-card-header-bg2: <?= esc($colorCardHeaderBg2) ?>;<?php endif; ?>
            --user-card-header-text: <?= esc($colorCardHeaderText) ?>;
            <?php endif; ?>
            <?php if ($aplicarMainHeaderCustom): ?>
            --user-main-header-bg: <?= esc($colorMainHeaderBg) ?>;
            <?php if ($mainHeaderEsGradiente && $colorMainHeaderBg2): ?>--user-main-header-bg2: <?= esc($colorMainHeaderBg2) ?>;<?php endif; ?>
            --user-main-header-text: <?= esc($colorMainHeaderText) ?>;
            <?php endif; ?>
        }
        /* Título del módulo (Gestión de Pacientes, Lista de Citas, etc.): estilo unificado */
        .main-header {
            padding: 30px !important;
            border-radius: 15px !important;
            margin-bottom: 30px !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        .main-header h2, .main-header p {
            margin-bottom: 0;
        }
        .main-header h2 { font-size: 1.5rem; font-weight: 600; }
        .main-header p { font-size: 0.95rem; opacity: 0.95; }
        <?php if ($aplicarMainHeaderCustom): ?>
        /* Main-header personalizado: color o gradiente + texto */
        body .main-header {
            <?php if ($mainHeaderEsGradiente && $colorMainHeaderBg2): ?>background: linear-gradient(135deg, var(--user-main-header-bg) 0%, var(--user-main-header-bg2) 100%) !important;
            <?php else: ?>background-color: var(--user-main-header-bg) !important;
            <?php endif; ?>
            color: var(--user-main-header-text) !important;
        }
        body .main-header h2,
        body .main-header p {
            color: var(--user-main-header-text) !important;
        }
        body .main-header .btn-light {
            background-color: rgba(255,255,255,0.95) !important;
            color: #334155 !important;
            border-color: rgba(255,255,255,0.8) !important;
        }
        <?php else: ?>
        /* Main-header por defecto: gradiente VitaSync */
        body:not(.dark) .main-header {
            background: linear-gradient(135deg, #4dcba5 0%, #bee6db 100%) !important;
            color: #fff !important;
        }
        body:not(.dark) .main-header h2,
        body:not(.dark) .main-header p {
            color: #fff !important;
        }
        body:not(.dark) .main-header .btn-light {
            background-color: rgba(255,255,255,0.95) !important;
            color: #334155 !important;
            border-color: rgba(255,255,255,0.8) !important;
        }
        <?php endif; ?>
        /* Logo VitaSync: mismo aspecto en header y sidebar en todos los módulos */
        .header-container .theme-brand .theme-logo a img.navbar-logo,
        .header-container .navbar .theme-brand .theme-logo img {
            height: 36px !important;
            width: auto !important;
            max-height: 36px !important;
            object-fit: contain !important;
        }
        #sidebar .theme-brand div.theme-logo img.navbar-logo,
        #sidebar .theme-brand .nav-logo img {
            width: auto !important;
            height: auto !important;
            max-height: 48px !important;
            max-width: 100% !important;
            object-fit: contain !important;
        }
        .sidebar-closed #sidebar .theme-brand div.theme-logo img.navbar-logo,
        .sidebar-closed #sidebar .theme-brand .nav-logo img {
            max-height: 40px !important;
            max-width: 40px !important;
        }
        .theme-text .nav-link span,
        .navbar .theme-text a,
        .sidebar-wrapper .menu .dropdown-toggle.active .nav-link span,
        .sidebar-wrapper .menu .submenu .nav-link:hover { color: var(--user-primary) !important; }
        .btn-primary {
            background-color: var(--user-primary) !important;
            border-color: var(--user-primary) !important;
            color: #fff !important;
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--user-primary-hover) !important;
            border-color: var(--user-primary-hover) !important;
            color: #fff !important;
        }
        .sidebar-wrapper .menu .dropdown-toggle .nav-link:hover span,
        .sidebar-wrapper .menu .submenu li a:hover { color: var(--user-primary) !important; }
        /* card-header: si "por defecto" = estilo Bootstrap original; si no = colores/gradiente del usuario */
        <?php if ($aplicarCardHeaderCustom): ?>
        .card-header {
            <?php if ($cardHeaderEsGradiente && $colorCardHeaderBg2): ?>background: linear-gradient(135deg, var(--user-card-header-bg) 0%, var(--user-card-header-bg2) 100%) !important;
            <?php else: ?>background-color: var(--user-card-header-bg) !important;
            <?php endif; ?>
            color: var(--user-card-header-text) !important;
            border-color: var(--user-card-header-bg) !important;
        }
        body:not(.dark) .card-header.bg-primary {
            <?php if ($cardHeaderEsGradiente && $colorCardHeaderBg2): ?>background: linear-gradient(135deg, var(--user-card-header-bg) 0%, var(--user-card-header-bg2) 100%) !important;
            <?php else: ?>background-color: var(--user-card-header-bg) !important;
            <?php endif; ?>
            color: var(--user-card-header-text) !important;
            border-color: var(--user-card-header-bg) !important;
        }
        body:not(.dark) .card-header.bg-success,
        body:not(.dark) .card-header.bg-info,
        body:not(.dark) .card-header.bg-danger,
        body:not(.dark) .card-header.bg-warning,
        body:not(.dark) .card-header.bg-light {
            <?php if ($cardHeaderEsGradiente && $colorCardHeaderBg2): ?>background: linear-gradient(135deg, var(--user-card-header-bg) 0%, var(--user-card-header-bg2) 100%) !important;
            <?php else: ?>background-color: var(--user-card-header-bg) !important;
            <?php endif; ?>
            color: var(--user-card-header-text) !important;
            border-color: var(--user-card-header-bg) !important;
        }
        body.dark .card-header,
        body.dark .card-header.bg-primary,
        body.dark .card-header.bg-success,
        body.dark .card-header.bg-info,
        body.dark .card-header.bg-danger,
        body.dark .card-header.bg-warning,
        body.dark .card-header.bg-light {
            <?php if ($cardHeaderEsGradiente && $colorCardHeaderBg2): ?>background: linear-gradient(135deg, var(--user-card-header-bg) 0%, var(--user-card-header-bg2) 100%) !important;
            <?php else: ?>background-color: var(--user-card-header-bg) !important;
            <?php endif; ?>
            color: var(--user-card-header-text) !important;
            border-color: var(--user-card-header-bg) !important;
        }
        <?php else: ?>
        /* Por defecto: no sobrescribir .card-header; Bootstrap y bg-primary, bg-info, etc. se muestran tal cual */
        <?php endif; ?>
        .card-header .card-title,
        .card-header h3, .card-header h4, .card-header h5, .card-header h6,
        .card-header i, .card-header .fas, .card-header .far, .card-header .fab {
            color: inherit !important;
        }
        /* section-card: mismo estilo en todas las vistas (Lista de Citas, Pacientes, Botones de Pago, etc.) */
        .section-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--user-primary);
        }
        .section-title {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .section-subtitle {
            margin-bottom: 1rem;
            color: #6c757d;
        }
        /* Normalización tema oscuro: cards, tablas y headers como en Lista de Citas / Mi perfil */
        body.dark .card,
        body.dark .card-body {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }
        body.dark .section-card {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
            border-left-color: var(--user-primary) !important;
        }
        body.dark .section-title,
        body.dark .section-subtitle {
            color: #e2e8f0 !important;
        }
        /* main-header: mismo gradiente/colores en dark que en claro (no se sobreescribe) */
        body.dark .table thead th,
        body.dark table.dataTable thead th,
        body.dark .dataTables_wrapper .table thead th {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }
        body.dark .table tbody td,
        body.dark .table tbody tr,
        body.dark table.dataTable tbody td,
        body.dark table.dataTable tbody tr {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }
        body.dark .table-striped tbody tr:nth-of-type(odd) {
            background-color: #0f172a !important;
        }
        body.dark .form-control,
        body.dark .form-select {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }
        body.dark .form-control::placeholder {
            color: #94a3b8 !important;
        }
        body.dark label {
            color: #e2e8f0 !important;
        }
        body.dark .text-muted {
            color: #94a3b8 !important;
        }
        body.dark .dataTables_wrapper .dataTables_length label,
        body.dark .dataTables_wrapper .dataTables_filter label {
            color: #e2e8f0 !important;
        }
        body.dark .dataTables_wrapper .dataTables_info {
            color: #94a3b8 !important;
        }
        /* Paginado DataTables: Anterior / Siguiente y números visibles en tema oscuro */
        body.dark .dataTables_wrapper .dataTables_paginate .page-link,
        body.dark .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #f1f5f9 !important;
            background-color: #334155 !important;
            border-color: #475569 !important;
        }
        body.dark .dataTables_wrapper .dataTables_paginate .page-item.previous:not(.disabled) .page-link,
        body.dark .dataTables_wrapper .dataTables_paginate .page-item.next:not(.disabled) .page-link,
        body.dark .dataTables_wrapper .dataTables_paginate .paginate_button.previous:not(.disabled),
        body.dark .dataTables_wrapper .dataTables_paginate .paginate_button.next:not(.disabled) {
            color: #fff !important;
            background-color: #475569 !important;
            border-color: #64748b !important;
        }
        body.dark .dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
        body.dark .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            color: #fff !important;
            background-color: var(--user-primary) !important;
            border-color: var(--user-primary) !important;
        }
        body.dark .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            color: #64748b !important;
            background-color: #1e293b !important;
        }
        /* Fondo VitaSync por defecto (modo claro) */
        body:not(.dark) { background-color: #fafeff !important; }
    </style>

</head>

<body class="layout-boxed<?= ($temaUsuario === 'oscuro') ? ' dark' : '' ?>">
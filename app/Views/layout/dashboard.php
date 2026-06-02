<?php
helper('auth_portal');

if (session()->get('usuario')) {
    $usuario = session()->get('usuario');
}

$tieneModuloClinico = usuario_dashboard_tiene_modulo_clinico($usuario ?? null);


/* echo "<pre>";
print_r($data);
echo "</pre>";
exit(); */
/* foreach ($data as $menu) {
    echo "<pre>";
    print_r($menu['menu']['nombre']);
    echo "</pre>";
    if (count($menu['submenu']) > 0) {
        echo "si tiene";
    } else {
        echo "no tiene";
    }
} */

?>

<?= view('template/header') ?>
<!-- BEGIN LOADER -->
<div id="load_screen">
    <div class="loader">
        <div class="loader-content">
            <div class="spinner-grow align-self-center"></div>
        </div>
    </div>
</div>
<!--  END LOADER -->

<!--  BEGIN NAVBAR  -->
<div class="header-container container-xxl">
    <header class="header navbar navbar-expand-sm expand-header">

        <ul class="navbar-item theme-brand flex-row  text-center">
            <li class="nav-item theme-logo">
                <a href="<?= base_url('dashboard/menu') ?>" class="nav-link d-flex align-items-center gap-2">
                    <img src="<?= base_url('lib/logo/logo-horizontal.png') ?>" alt="NutriNext" class="navbar-logo" style="height: 40px; width: auto;">
                </a>
            </li>
        </ul>
        <ul class="navbar-item flex-row ms-lg-auto ms-0 action-area">
            <?php if ($tieneModuloClinico): ?>
            <li class="nav-item dropdown notification-dropdown">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle position-relative" id="notificationDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notificaciones">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span class="notif-badge-count" id="notifBadge" style="display: none;">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end notification-menu-panel" aria-labelledby="notificationDropdown" >
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notificaciones</h6>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="btnMarcarTodasLeidas" style="display: none;">Marcar todas</button>
                    </div>
                    <div class="notification-scroll" id="notifLista">
                        <div class="notif-empty text-muted small py-3 text-center" id="notifCargando">Cargando...</div>
                    </div>
                </div>
            </li>
            <?php endif; ?>
            <li class="nav-item theme-toggle-item">
                <a href="javascript:void(0);" class="nav-link theme-toggle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-moon dark-mode">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-sun light-mode">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </a>
            </li>
            <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar-container">
                        <div class="avatar avatar-sm avatar-indicators avatar-online">
                            <?php
                            $fotoPerfil = !empty($usuario['foto']) ? base_url($usuario['foto']) : base_url('lib/src/assets/img/profile-30.png');
                            ?>
                            <img alt="avatar" src="<?= esc($fotoPerfil) ?>" class="rounded-circle" style="object-fit: cover; width: 30px; height: 30px;">
                        </div>
                    </div>
                </a>

                <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                    <div class="user-profile-section">
                        <div class="media mx-auto">
                            <div class="emoji me-2">
                                &#x1F44B;
                            </div>
                            <div class="media-body">
                                <h5><?= $usuario['nombre'] ?></h5>
                                <p><?= $usuario['perfil_nombre'] ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?= base_url('dashboard/mi-perfil') ?>">
                            <i class="fas fa-user-circle me-2"></i> <span>Mi perfil</span>
                        </a>
                    </div>
                    <div class="dropdown-item">
                        <a href="<?= base_url('logout') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg> <span>Salir</span>
                        </a>
                    </div>
                </div>

            </li>
        </ul>
    </header>
</div>
<!--  END NAVBAR  -->

<?php if ($tieneModuloClinico): ?>
<!-- CRONÓMETRO GLOBAL DE CONSULTA ACTIVA -->
<div id="cronometroGlobalConsulta" style="display: none; position: fixed; top: 48px; right: 20px; z-index: 1050; background: linear-gradient(135deg, #7bc143 0%, #2daae1 100%); padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); color: white; min-width: 280px;">
    <div class="d-flex align-items-center justify-content-between">
        <div class="flex-grow-1">
            <div style="font-size: 0.85rem; opacity: 0.9; margin-bottom: 5px;">
                <i class="fas fa-user-md me-1"></i> <span id="cronometroPacienteNombre">-</span>
            </div>
            <div style="font-size: 1.5rem; font-weight: bold; font-family: 'Courier New', monospace;" id="cronometroTiempo">
                00:00:00
            </div>
        </div>
        <div class="ms-3">
            <a href="#" id="cronometroBtnIrConsulta" class="btn btn-light btn-sm" style="white-space: nowrap;">
                <i class="fas fa-arrow-right me-1"></i> Ir a Consulta
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!--  BEGIN MAIN CONTAINER  -->
<div class="main-container" id="container">

    <div class="overlay"></div>
    <div class="search-overlay"></div>

    <!--  BEGIN SIDEBAR  -->
    <div class="sidebar-wrapper sidebar-theme">

        <nav id="sidebar">

            <div class="navbar-nav theme-brand flex-row  text-center">
                <div class="nav-logo">
                    <div class="nav-item theme-logo">
                        <a href="<?= base_url('dashboard/menu') ?>">
                            <img src="<?= base_url('lib/logo/logo-horizontal.png') ?>" class="navbar-logo" alt="NutriNext" style="max-height: 56px; width: auto;">
                        </a>
                    </div>
                    <div class="nav-item theme-text">
                        <a href="<?= base_url('dashboard/menu') ?>" class="nav-link"><span style="color: #6b7280;">Nutri</span><span style="color: var(--user-primary, #7bc143);">Next</span></a>
                    </div>
                </div>
                <div class="nav-item sidebar-toggle">
                    <div class="btn-toggle sidebarCollapse">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left">
                            <polyline points="11 17 6 12 11 7"></polyline>
                            <polyline points="18 17 13 12 18 7"></polyline>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="shadow-bottom"></div>
            
            <style>
                /* Anular nowrap global del tema (#sidebar * { white-space: nowrap }) */
                #sidebar ul.nutrinext-sidebar-menu,
                #sidebar ul.nutrinext-sidebar-menu li,
                #sidebar ul.nutrinext-sidebar-menu a,
                #sidebar ul.nutrinext-sidebar-menu .dropdown-toggle,
                #sidebar ul.nutrinext-sidebar-menu .dropdown-toggle span {
                    overflow: visible !important;
                    white-space: normal !important;
                    text-overflow: unset !important;
                }

                #sidebar ul.nutrinext-sidebar-menu .menu-section-heading {
                    font-size: 11px;
                    font-weight: 600;
                    letter-spacing: 0.06em;
                    text-transform: uppercase;
                    color: #94a3b8;
                    padding: 14px 16px 6px;
                    margin: 0;
                }

                #sidebar ul.nutrinext-sidebar-menu li.menu-section-heading-item {
                    margin-bottom: 0 !important;
                }

                #sidebar ul.nutrinext-sidebar-menu .sidebar-menu-link-inner {
                    display: flex;
                    align-items: flex-start;
                    gap: 10px;
                    flex: 1;
                    min-width: 0;
                }

                #sidebar ul.nutrinext-sidebar-menu .sidebar-menu-link-inner svg {
                    flex-shrink: 0;
                    margin-top: 2px;
                }

                #sidebar ul.nutrinext-sidebar-menu .sidebar-menu-link-inner span {
                    line-height: 1.35;
                    word-break: break-word;
                }

                #sidebar ul.nutrinext-sidebar-menu li.menu > .dropdown-toggle.sidebar-menu-link {
                    display: flex !important;
                    align-items: flex-start;
                    height: auto !important;
                    min-height: 42px;
                    padding: 10px 14px !important;
                }

                #sidebar ul.nutrinext-sidebar-menu ul.submenu > li a {
                    display: block;
                    line-height: 1.35 !important;
                    word-break: break-word;
                    padding: 8px 12px 8px 18px !important;
                    margin-left: 28px !important;
                    font-size: 13px;
                }

                #sidebar ul.nutrinext-sidebar-menu li.menu:last-child {
                    margin-bottom: 72px !important;
                }

                #sidebar {
                    overflow-y: auto !important;
                    overflow-x: hidden !important;
                    -webkit-overflow-scrolling: touch;
                    max-height: 100vh !important;
                }

                @media (max-width: 991px) {
                    .sidebar-wrapper { overflow-y: auto !important; }
                    #sidebar {
                        height: 100vh !important;
                        padding-bottom: 48px !important;
                    }
                }
            </style>

            <?php
            if (isset($data['data']) && is_array($data['data'])) {
                $menuItemsRaw = $data['data'];
            } elseif (isset($data[0]['menu']) && isset($data[0]['submenu'])) {
                $menuItemsRaw = $data;
            } else {
                $menuItemsRaw = [];
            }
            $sidebarMenu = \App\Libraries\DashboardMenuBuilder::build($menuItemsRaw, $usuario ?? null);
            echo view('layout/partials/sidebar_menu', ['sidebarMenu' => $sidebarMenu]);
            ?>
        </nav>
    </div>
    <!--  END SIDEBAR  -->

    <!--  BEGIN CONTENT AREA  -->
    <div id="content" class="main-content">
        <div class="layout-px-spacing">

            <div class="middle-content container-xxl p-0">

                <!--  BEGIN BREADCRUMBS  -->
                <div class="secondary-nav">
                    <div class="breadcrumbs-container" data-page-heading="Analytics">
                        <header class="header navbar navbar-expand-sm">
                            <a href="javascript:void(0);" class="btn-toggle sidebarCollapse" data-placement="bottom">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu">
                                    <line x1="3" y1="12" x2="21" y2="12"></line>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <line x1="3" y1="18" x2="21" y2="18"></line>
                                </svg>
                            </a>
                            <div class="d-flex breadcrumb-content">
                                <div class="page-header">

                                    <div class="page-title">
                                    </div>

                                    <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="<?= base_url("dashboard/menu") ?>">Dashboard</a></li>
                                            <li class="breadcrumb-item active" aria-current="page"> <?php echo $this->renderSection("titulo"); ?></li>
                                        </ol>
                                    </nav>

                                </div>
                            </div>
                        </header>
                    </div>
                </div>
                <div class="row layout-top-spacing">
                    <!-- START Section Usuario -->
                    <?php echo $this->renderSection("usuario/registro"); ?>
                    <?php echo $this->renderSection("usuario/lista"); ?>
                    <?php echo $this->renderSection("usuario/detalle"); ?>
                    <!-- END Section Usuario -->

                    <!-- Star Section Perfil -->
                    <?php echo $this->renderSection("perfil/registro"); ?>
                    <?php echo $this->renderSection("perfil/lista"); ?>
                    <?php echo $this->renderSection("perfil/detalle"); ?>
                    <!-- END Section Perfil -->

                    <!-- Star Section Modulo -->
                    <?php echo $this->renderSection("modulo/registro"); ?>
                    <?php echo $this->renderSection("modulo/lista"); ?>
                    <?php echo $this->renderSection("modulo/detalle"); ?>
                    <!-- END Section Modulo -->

                    <!-- Star Section Perfil Detalle-->
                    <?php echo $this->renderSection("perfil_detalle/registro"); ?>
                    <?php echo $this->renderSection("perfil_detalle/lista"); ?>
                    <?php echo $this->renderSection("perfil_detalle/detalle"); ?>
                    <!-- END Section Perfil Detalle-->

                    <!-- Star Section Modulo Detalle-->
                    <?php echo $this->renderSection("modulo_detalle/registro"); ?>
                    <?php echo $this->renderSection("modulo_detalle/lista"); ?>
                    <?php echo $this->renderSection("modulo_detalle/detalle"); ?>

                    <?php echo $this->renderSection("menu_grupo/lista"); ?>
                    <?php echo $this->renderSection("menu_grupo/registro"); ?>
                    <?php echo $this->renderSection("menu_grupo/detalle"); ?>
                    <!-- END Section Modulo Detalle-->

                    <!-- Star Section Modulo Servicio-->
                    <?php echo $this->renderSection("servicio/registro"); ?>
                    <?php echo $this->renderSection("servicio/lista"); ?>
                    <?php echo $this->renderSection("servicio/editar"); ?>
                    <?php echo $this->renderSection("servicio/detalle"); ?>
                    <!-- END Section Modulo Servicio-->

                    <!-- Star Section Modulo Galeria-->
                    <?php echo $this->renderSection("galeria/registro"); ?>
                    <?php echo $this->renderSection("galeria/lista"); ?>
                    <?php echo $this->renderSection("galeria/editar"); ?>
                    <!-- END Section Modulo Galeria-->

                    <!-- Star Section Modulo Galeria Categoria-->
                    <?php echo $this->renderSection("galeria_categoria/registro"); ?>
                    <?php echo $this->renderSection("galeria_categoria/lista"); ?>
                    <?php echo $this->renderSection("galeria_categoria/editar"); ?>
                    <!-- END Section Modulo Galeria Categoria-->

                    <!-- Star Section Modulo Servicio Categoria-->
                    <?php echo $this->renderSection("servicio_categoria/registro"); ?>
                    <?php echo $this->renderSection("servicio_categoria/lista"); ?>
                    <?php echo $this->renderSection("servicio_categoria/editar"); ?>
                    <!-- END Section Modulo Servicio Categoria-->

                    <!-- Star Section Modulo Proyecto-->
                    <?php echo $this->renderSection("proyecto/registro"); ?>
                    <?php echo $this->renderSection("proyecto/lista"); ?>
                    <?php echo $this->renderSection("proyecto/editar"); ?>
                    <!-- END Section Modulo Proyecto-->

                    <!-- Star Section Modulo Leads-->
                    <?php echo $this->renderSection("leads/lista"); ?>
                    <!-- END Section Modulo Leads-->

                    <!-- Star Section Modulo Testimonio-->
                    <?php echo $this->renderSection("testimonio/registro"); ?>
                    <?php echo $this->renderSection("testimonio/lista"); ?>
                    <?php echo $this->renderSection("testimonio/editar"); ?>
                    
                    <!-- Secciones de Clientes -->
                    <?php echo $this->renderSection("cliente/lista"); ?>
                    <?php echo $this->renderSection("cliente/registro"); ?>
                    <?php echo $this->renderSection("cliente/editar"); ?>
                    <?php echo $this->renderSection("cliente/detalle"); ?>
                    
                    <!-- Secciones de Cotizaciones -->
                    <?php echo $this->renderSection("cotizacion/lista"); ?>
                    <?php echo $this->renderSection("cotizacion/registro"); ?>
                    <?php echo $this->renderSection("cotizacion/editar"); ?>
                    <?php echo $this->renderSection("cotizacion/detalle"); ?>
                    
                    <!-- Secciones de Empresa -->
                    <?php echo $this->renderSection("empresa/lista"); ?>
                    <?php echo $this->renderSection("empresa/registro"); ?>
                    <?php echo $this->renderSection("empresa/detalle"); ?>

                    <?php echo $this->renderSection("servicio-nutrinext/lista"); ?>
                    <?php echo $this->renderSection("servicio-nutrinext/registro"); ?>

                    <?php echo $this->renderSection("paquete/lista"); ?>
                    <?php echo $this->renderSection("paquete/registro"); ?>
                    <?php echo $this->renderSection("paquete/detalle"); ?>
                    <?php echo $this->renderSection("paquete/gestionar_modulos"); ?>

                    <!-- Secciones de Add-ons (Super Admin) -->
                    <?php echo $this->renderSection("addon/lista"); ?>
                    
                    <!-- Secciones de Facturación (Super Admin) -->
                    <?php echo $this->renderSection("facturacion/lista"); ?>
                    
                    <!-- Secciones de Listado de Materiales -->
                    <?php echo $this->renderSection("listado_material/lista"); ?>
                    <?php echo $this->renderSection("listado_material/registro"); ?>
                    <?php echo $this->renderSection("listado_material/editar"); ?>
                    <?php echo $this->renderSection("listado_material/detalle"); ?>
                    <!-- END Section Listado de Materiales-->
                    
                    <!-- ============================================ -->
                    <!-- SECCIONES DE MÓDULOS DE NUTRICIONISTAS -->
                    <!-- ============================================ -->
                    
                    <!-- Secciones de Pacientes -->
                    <?php echo $this->renderSection("paciente/lista"); ?>
                    <?php echo $this->renderSection("paciente/registro"); ?>
                    <?php echo $this->renderSection("paciente/editar"); ?>
                    <?php echo $this->renderSection("paciente/detalle"); ?>
                    <!-- END Section Pacientes -->
                    
                    <!-- Secciones de Agenda -->
                    <?php echo $this->renderSection("agenda/lista"); ?>
                    <?php echo $this->renderSection("agenda/gestionar"); ?>
                    <?php echo $this->renderSection("agenda/calendario"); ?>
                    <?php echo $this->renderSection("agenda/cancelar_horas"); ?>
                    <?php echo $this->renderSection("agenda/consulta"); ?>
                    <?php echo $this->renderSection("agenda/estadisticas"); ?>
                    <!-- END Section Agenda -->

                    <!-- Secciones de Mensajes WhatsApp -->
                    <?php echo $this->renderSection("mensajes/index"); ?>
                    <!-- END Section Mensajes -->
                    
                    <!-- Secciones de Documentos -->
                    <?php echo $this->renderSection("documento/lista"); ?>
                    <?php echo $this->renderSection("documento/registro"); ?>
                    <?php echo $this->renderSection("documento/editar"); ?>
                    <?php echo $this->renderSection("documento/detalle"); ?>
                    <!-- END Section Documentos -->
                    
                    <!-- Secciones de Historial Clínico -->
                    <?php echo $this->renderSection("historial/lista"); ?>
                    <?php echo $this->renderSection("historial/registro"); ?>
                    <?php echo $this->renderSection("historial/editar"); ?>
                    <?php echo $this->renderSection("historial/detalle"); ?>
                    <?php echo $this->renderSection("historial/comparar"); ?>
                    <!-- END Section Historial Clínico -->
                    
                    <!-- Secciones de Plan Alimentario -->
                    <?php echo $this->renderSection("plan_alimentario/index"); ?>
                    <!-- END Section Plan Alimentario -->
                    
                    <!-- Secciones de Pagos -->
                    <?php echo $this->renderSection("pago/lista"); ?>
                    <?php echo $this->renderSection("pago/registro"); ?>
                    <?php echo $this->renderSection("pago/editar"); ?>
                    <!-- END Section Pagos -->
                    
                    <!-- Tarifas de consulta / Mercado Pago -->
                    <?php echo $this->renderSection("boton_pago/lista"); ?>
                    <?php echo $this->renderSection("boton_pago/crear"); ?>
                    <?php echo $this->renderSection("boton_pago/ver"); ?>
                    <!-- END Section Botones de Pago -->
                    
                    <!-- Secciones de Configuraciones -->
                    <?php echo $this->renderSection("configuracion/index"); ?>
                    <?php echo $this->renderSection("mi_perfil/index"); ?>
                    <!-- END Section Configuraciones -->

                    <!-- Gimnasio -->
                    <?php echo $this->renderSection("gym/ejercicio/lista"); ?>
                    <?php echo $this->renderSection("gym/ejercicio/registro"); ?>
                    <?php echo $this->renderSection("gym/ejercicio/editar"); ?>
                    <?php echo $this->renderSection("gym/rutina/lista"); ?>
                    <?php echo $this->renderSection("gym/rutina/registro"); ?>
                    <?php echo $this->renderSection("gym/rutina/editar"); ?>
                    <?php echo $this->renderSection("gym/programa/lista"); ?>
                    <?php echo $this->renderSection("gym/programa/registro"); ?>
                    <?php echo $this->renderSection("gym/programa/editar"); ?>
                    <?php echo $this->renderSection("gym/alumno/lista"); ?>
                    <?php echo $this->renderSection("gym/alumno/registro"); ?>
                    <?php echo $this->renderSection("gym/alumno/editar"); ?>
                    <?php echo $this->renderSection("gym/asignacion/lista"); ?>
                    <!-- END Gimnasio -->
                </div>

            </div>

        </div>
        
        <!-- Modal de Advertencia de Sesión -->
        <div class="modal fade" id="sessionExpirationModal" tabindex="-1" role="dialog" aria-labelledby="sessionExpirationModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="sessionExpirationModalLabel">
                            <i class="fas fa-clock me-2"></i> ⏰ Tu sesión está por expirar
                        </h5>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #f0ad4e;"></i>
                        </div>
                        <h5 class="mb-3">Tu sesión expirará en <span id="sessionTimeRemaining" class="text-danger fw-bold">5:00</span> minutos</h5>
                        <p class="text-muted">
                            Si estás llenando un formulario, guarda tu trabajo o extiende tu sesión haciendo clic en el botón de abajo.
                        </p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-success btn-lg" id="extendSessionBtn">
                            <i class="fas fa-sync-alt me-2"></i> Extender Sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script de Monitoreo de Sesión -->
        <script>
        (function() {
            // Configuración de la sesión (en segundos)
            const SESSION_DURATION = <?= config('Session')->expiration ?>; // 7200 segundos (2 horas)
            const WARNING_TIME = 300; // Mostrar advertencia 5 minutos antes (300 segundos)
            
            // MODO PRUEBA: Descomentar las siguientes líneas para pruebas rápidas (modal aparece en 30 segundos)
            // const SESSION_DURATION = 90; // PRUEBA: 90 segundos (1.5 minutos)
            // const WARNING_TIME = 60; // PRUEBA: Advertencia 60 segundos antes (1 minuto)
            
            let sessionStartTime = Date.now();
            let warningShown = false;
            let countdownInterval = null;
            
            // Función para verificar el tiempo de sesión
            function checkSessionExpiration() {
                const currentTime = Date.now();
                const elapsedSeconds = Math.floor((currentTime - sessionStartTime) / 1000);
                const remainingSeconds = SESSION_DURATION - elapsedSeconds;
                
                // Si quedan menos de WARNING_TIME segundos, mostrar advertencia
                if (remainingSeconds <= WARNING_TIME && !warningShown) {
                    showExpirationWarning(remainingSeconds);
                }
                
                // Si la sesión expiró, redirigir al login
                if (remainingSeconds <= 0) {
                    sessionExpired();
                }
            }
            
            // Mostrar modal de advertencia
            function showExpirationWarning(remainingSeconds) {
                warningShown = true;
                const modal = new bootstrap.Modal(document.getElementById('sessionExpirationModal'));
                modal.show();
                
                // Iniciar countdown
                startCountdown(remainingSeconds);
            }
            
            // Countdown en el modal
            function startCountdown(seconds) {
                const timeDisplay = document.getElementById('sessionTimeRemaining');
                let remaining = seconds;
                
                countdownInterval = setInterval(() => {
                    remaining--;
                    
                    if (remaining <= 0) {
                        clearInterval(countdownInterval);
                        sessionExpired();
                        return;
                    }
                    
                    const minutes = Math.floor(remaining / 60);
                    const secs = remaining % 60;
                    timeDisplay.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;
                    
                    // Cambiar color según el tiempo restante
                    if (remaining <= 60) {
                        timeDisplay.classList.remove('text-warning');
                        timeDisplay.classList.add('text-danger', 'blink');
                    } else if (remaining <= 180) {
                        timeDisplay.classList.add('text-warning');
                    }
                }, 1000);
            }
            
            // Extender sesión
            function extendSession() {
                // Hacer una petición AJAX a la ruta keepalive para renovar la sesión
                fetch('<?= base_url('dashboard/keepalive') ?>', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Reiniciar el timer
                        sessionStartTime = Date.now();
                        warningShown = false;
                        
                        // Cerrar modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('sessionExpirationModal'));
                        modal.hide();
                        
                        // Limpiar countdown
                        if (countdownInterval) {
                            clearInterval(countdownInterval);
                            countdownInterval = null;
                        }
                        
                        // Mostrar notificación de éxito
                        showSuccessNotification();
                    }
                })
                .catch(error => {
                    console.error('Error al extender la sesión:', error);
                });
            }
            
            // Sesión expirada
            function sessionExpired() {
                if (countdownInterval) {
                    clearInterval(countdownInterval);
                }
                
                // Mostrar mensaje y redirigir
                alert('Tu sesión ha expirado. Serás redirigido al inicio de sesión.');
                window.location.href = '<?= base_url('login') ?>';
            }
            
            // Notificación de éxito
            function showSuccessNotification() {
                // Crear un toast o alerta temporal
                const toast = document.createElement('div');
                toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = `
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>¡Sesión extendida!</strong> Tu sesión ha sido renovada por 2 horas más.
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            }
            
            // Event listener para el botón de extender sesión
            document.getElementById('extendSessionBtn').addEventListener('click', extendSession);
            
            // Verificar cada 30 segundos
            setInterval(checkSessionExpiration, 30000);
            
            // También extender sesión automáticamente en cualquier actividad del usuario
            let activityTimeout = null;
            function resetActivityTimer() {
                clearTimeout(activityTimeout);
                activityTimeout = setTimeout(() => {
                    // Si el usuario ha estado activo, extender silenciosamente la sesión
                    if (!warningShown) {
                        sessionStartTime = Date.now();
                    }
                }, 60000); // Después de 1 minuto de actividad
            }
            
            // Detectar actividad del usuario
            ['mousedown', 'keypress', 'scroll', 'touchstart'].forEach(event => {
                document.addEventListener(event, resetActivityTimer, true);
            });
        })();

        // ============================================
        // VERIFICACIÓN Y RENOVACIÓN AUTOMÁTICA DE TOKEN DE CALENDARIO
        // Solo perfiles clínicos con crear_evento_calendario activo
        // ============================================
        <?php
        $crearEventoCalendario = false;
        if ($tieneModuloClinico) {
            $configuracionModel = new \App\Models\EmpresaConfiguracion();
            $usuarioId = session()->get('usuario')['id'] ?? null;
            if ($usuarioId) {
                $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);
                $crearEventoCalendario = ($configuracion['crear_evento_calendario'] ?? 1) == 1;
            }
        }
        ?>
        <?php if ($tieneModuloClinico && $crearEventoCalendario): ?>
        (function() {
            // Verificar y renovar token de calendario cada 30 minutos
            const TOKEN_CHECK_INTERVAL = 30 * 60 * 1000; // 30 minutos en milisegundos
            let calendarTokenWarningShown = false;
            const connectUrl = '<?= base_url('dashboard/agenda/calendario/connect') ?>';
            
            function mostrarAvisoConectarCalendario() {
                if (calendarTokenWarningShown) return;
                calendarTokenWarningShown = true;
                var id = 'calendar-token-warning';
                var prev = document.getElementById(id);
                if (prev) prev.remove();
                var div = document.createElement('div');
                div.id = id;
                div.className = 'alert alert-warning alert-dismissible fade show rounded-0 mb-0';
                div.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; z-index: 9999; border-radius: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.15);';
                div.innerHTML = '<strong><i class="fas fa-calendar-alt me-2"></i>Calendario:</strong> El token ha expirado o fue revocado. ' +
                    'Debe conectarse en <a href="' + connectUrl + '" class="alert-link">Agenda &rarr; Conectar calendario</a> para sincronizar eventos. ' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>';
                document.body.insertBefore(div, document.body.firstChild);
            }
            
            function verificarTokenCalendario() {
                fetch('<?= base_url('dashboard/agenda/calendario/verificar-token') ?>', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.renovado) {
                            console.log('✅ Token de calendario renovado automáticamente:', data.message);
                        } else {
                            console.log('ℹ️ Token de calendario verificado:', data.message);
                            if (data.minutos_restantes !== undefined) {
                                console.log('   Minutos restantes:', data.minutos_restantes);
                            }
                        }
                    } else {
                        console.warn('⚠️ Advertencia de token de calendario:', data.message);
                        if (!data.renovado) {
                            mostrarAvisoConectarCalendario();
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al verificar token de calendario:', error);
                });
            }
            
            // Verificar inmediatamente al cargar la página (si el usuario tiene token configurado)
            // Esperar 5 segundos para no interferir con la carga inicial
            setTimeout(verificarTokenCalendario, 5000);
            
            // Verificar periódicamente cada 30 minutos
            setInterval(verificarTokenCalendario, TOKEN_CHECK_INTERVAL);
        })();
        <?php endif; ?>
        
        <?php if ($tieneModuloClinico): ?>
        // =====================================================
        // CRONÓMETRO GLOBAL DE CONSULTA ACTIVA
        // =====================================================
        (function() {
            var cronometroInterval = null;
            var fechaInicioConsulta = null;
            var detalleAgendaIdActiva = null;
            var pollTimer = null;
            var POLL_MS_ACTIVE = 5000;
            var POLL_MS_INACTIVE = 30000;
            var POLL_MS_HIDDEN = 60000;

            function scheduleNextPoll(ms) {
                try { if (pollTimer) clearTimeout(pollTimer); } catch (e) {}
                pollTimer = setTimeout(verificarConsultaActiva, ms);
            }
            
            function actualizarCronometro() {
                if (!fechaInicioConsulta) return;
                
                var ahora = new Date();
                var inicio = new Date(fechaInicioConsulta);
                var diff = ahora - inicio;
                
                if (diff < 0) {
                    fechaInicioConsulta = ahora;
                    diff = 0;
                }
                
                var horas = Math.floor(diff / (1000 * 60 * 60));
                var minutos = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                var segundos = Math.floor((diff % (1000 * 60)) / 1000);
                
                var tiempo = String(horas).padStart(2, '0') + ':' + 
                             String(minutos).padStart(2, '0') + ':' + 
                             String(segundos).padStart(2, '0');
                
                $('#cronometroTiempo').text(tiempo);
            }
            
            function verificarConsultaActiva() {
                $.ajax({
                    url: '<?= base_url('dashboard/agenda/getConsultaActiva') ?>',
                    type: 'GET',
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response.activa) {
                            fechaInicioConsulta = response.fecha_inicio;
                            detalleAgendaIdActiva = response.detalle_agenda_id;
                            
                            $('#cronometroPacienteNombre').text(response.paciente.nombre || 'Paciente');
                            $('#cronometroBtnIrConsulta').attr('href', response.url_consulta);
                            $('#cronometroGlobalConsulta').fadeIn(300);
                            document.body.classList.add('cronometro-consulta-visible');
                            
                            // Iniciar actualización del cronómetro
                            if (!cronometroInterval) {
                                actualizarCronometro();
                                cronometroInterval = setInterval(actualizarCronometro, 1000);
                            }
                        } else {
                            // Ocultar cronómetro si no hay consulta activa
                            document.body.classList.remove('cronometro-consulta-visible');
                            $('#cronometroGlobalConsulta').fadeOut(300);
                            if (cronometroInterval) {
                                clearInterval(cronometroInterval);
                                cronometroInterval = null;
                            }
                            fechaInicioConsulta = null;
                            detalleAgendaIdActiva = null;
                        }

                        // Poll adaptativo: si hay consulta activa, más frecuente; si no, menos; si la pestaña está oculta, mínimo impacto.
                        var nextMs = document.hidden ? POLL_MS_HIDDEN : (response.activa ? POLL_MS_ACTIVE : POLL_MS_INACTIVE);
                        scheduleNextPoll(nextMs);
                    },
                    error: function() {
                        // En caso de error, ocultar el cronómetro
                        document.body.classList.remove('cronometro-consulta-visible');
                        $('#cronometroGlobalConsulta').fadeOut(300);
                        if (cronometroInterval) {
                            clearInterval(cronometroInterval);
                            cronometroInterval = null;
                        }
                        // En error, reintentar más lento
                        scheduleNextPoll(document.hidden ? POLL_MS_HIDDEN : POLL_MS_INACTIVE);
                    }
                });
            }
            
            // Verificar inmediatamente al cargar
            verificarConsultaActiva();

            // Si la pestaña cambia de visibilidad, reajustar el polling
            document.addEventListener('visibilitychange', function() {
                scheduleNextPoll(document.hidden ? POLL_MS_HIDDEN : POLL_MS_INACTIVE);
            });
        })();
        <?php endif; ?>
        </script>
        
        <?php if ($tieneModuloClinico): ?>
        <style>
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.3; }
        }
        .blink {
            animation: blink 1s infinite;
        }
        .notification-dropdown .nav-link { padding-right: 0.35rem !important; }
        .notif-badge-count {
            position: absolute;
            top: 2px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 999px;
            background: #dc3545;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            line-height: 1;
            box-sizing: border-box;
            border: 2px solid var(--user-main-header-bg, #1e293b);
            pointer-events: none;
            z-index: 1;
            align-items: center;
            justify-content: center;
        }
        /* Panel notificaciones: anular tema (min-width 15rem, overflow, etc.) */
        .navbar .navbar-item .nav-item.notification-dropdown .dropdown-menu.notification-menu-panel {
            width: 400px !important;
            min-width: 400px !important;
            max-width: min(420px, calc(100vw - 24px)) !important;
            padding: 0 !important;
            overflow: visible !important;
        }
        .notification-menu-panel .notification-scroll {
            height: auto !important;
            max-height: min(420px, 70vh) !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            position: relative !important;
        }
        .notification-menu-panel .notif-panel-header {
            flex-shrink: 0;
        }
        .notification-menu-panel .notif-panel-header h6 {
            white-space: nowrap;
        }
        .notification-menu-panel .notif-empty {
            padding: 1rem;
            text-align: center;
        }
        .notification-menu-panel .notif-item {
            cursor: pointer;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            padding: 0.85rem 1rem !important;
            text-decoration: none !important;
            color: #1e293b !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: unset !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: break-word !important;
            border-radius: 0 !important;
            background: transparent !important;
        }
        .notification-menu-panel .notif-item.unread {
            background: rgba(123, 193, 67, 0.12) !important;
            border-left: 3px solid #7bc143;
        }
        .notification-menu-panel .notif-item:hover {
            background: rgba(123, 193, 67, 0.18) !important;
            color: #1e293b !important;
        }
        .notification-menu-panel .notif-item .notif-titulo {
            display: block;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
            color: #0f172a;
            line-height: 1.35;
            white-space: normal !important;
        }
        .notification-menu-panel .notif-item .notif-msg {
            display: block;
            font-size: 0.875rem;
            color: #334155;
            margin: 0 0 0.35rem 0;
            line-height: 1.5;
            white-space: normal !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        .notification-menu-panel .notif-item .notif-hace {
            display: block;
            font-size: 0.8rem;
            color: #64748b;
        }
        </style>

        <script>
        (function() {
            var urlListar = <?= json_encode(base_url('dashboard/notificaciones/listar')) ?>;
            var urlMarcar = <?= json_encode(base_url('dashboard/notificaciones/marcar-leida')) ?>;
            var urlMarcarTodas = <?= json_encode(base_url('dashboard/notificaciones/marcar-todas-leidas')) ?>;
            var csrfName = <?= json_encode(csrf_token()) ?>;
            var csrfHash = <?= json_encode(csrf_hash()) ?>;
            var badge = document.getElementById('notifBadge');
            var lista = document.getElementById('notifLista');
            var btnTodas = document.getElementById('btnMarcarTodasLeidas');
            var ultimoNoLeidas = 0;
            var ultimaNotifId = 0;

            function csrfBody(extra) {
                var p = new URLSearchParams(extra || {});
                p.set(csrfName, csrfHash);
                return p;
            }

            function actualizarBadge(n) {
                if (!badge) return;
                if (n > 0) {
                    badge.textContent = n > 99 ? '99+' : String(n);
                    badge.style.display = 'inline-flex';
                } else {
                    badge.style.display = 'none';
                }
                if (btnTodas) btnTodas.style.display = n > 0 ? '' : 'none';
            }

            function renderLista(notificaciones) {
                if (!lista) return;
                if (!notificaciones || !notificaciones.length) {
                    lista.innerHTML = '<div class="notif-empty text-muted small py-3 text-center">Sin notificaciones</div>';
                    return;
                }
                var html = '';
                notificaciones.forEach(function(n) {
                    var unread = parseInt(n.leida, 10) === 0;
                    html += '<a href="#" class="notif-item' + (unread ? ' unread' : '') + '" data-id="' + n.id + '" data-enlace="' + escapeHtml(n.enlace || '') + '">';
                    html += '<span class="notif-titulo">' + escapeHtml(n.titulo || '') + '</span>';
                    html += '<span class="notif-msg">' + escapeHtml(n.mensaje || '') + '</span>';
                    html += '<span class="notif-hace">' + escapeHtml(n.hace || '') + '</span></a>';
                });
                lista.innerHTML = html;
                lista.querySelectorAll('.notif-item').forEach(function(el) {
                    el.addEventListener('click', function(e) {
                        e.preventDefault();
                        var id = parseInt(this.getAttribute('data-id'), 10);
                        var enlace = this.getAttribute('data-enlace') || '';
                        if (enlace) {
                            marcarLeida(id, function() {
                                window.location.href = enlace;
                            });
                        } else {
                            marcarLeida(id);
                        }
                    });
                });
            }

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function cargarNotificaciones(mostrarToastNueva) {
                fetch(urlListar + '?limit=15', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (!data || data.error) return;
                        var noLeidas = data.no_leidas || 0;
                        if (mostrarToastNueva && noLeidas > ultimoNoLeidas && data.notificaciones && data.notificaciones.length) {
                            var primera = data.notificaciones[0];
                            if (parseInt(primera.leida, 10) === 0 && parseInt(primera.id, 10) > ultimaNotifId) {
                                mostrarAvisoNueva(primera.titulo, primera.mensaje);
                                ultimaNotifId = parseInt(primera.id, 10);
                            }
                        }
                        ultimoNoLeidas = noLeidas;
                        actualizarBadge(noLeidas);
                        renderLista(data.notificaciones);
                    })
                    .catch(function() {
                        if (lista) lista.innerHTML = '<div class="notif-empty text-muted small py-3 text-center">No se pudieron cargar</div>';
                    });
            }

            function mostrarAvisoNueva(titulo, mensaje) {
                var toast = document.createElement('div');
                toast.className = 'alert alert-info shadow position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '10000';
                toast.style.maxWidth = '360px';
                toast.innerHTML = '<strong><i class="fas fa-bell me-1"></i> ' + escapeHtml(titulo) + '</strong><br><span class="small">' + escapeHtml(mensaje || '') + '</span>';
                document.body.appendChild(toast);
                setTimeout(function() { toast.remove(); }, 8000);
            }

            function marcarLeida(id, cb) {
                fetch(urlMarcar, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: csrfBody({ id: id }).toString()
                }).then(function(r) { return r.json(); }).then(function(data) {
                    if (data && data.no_leidas !== undefined) ultimoNoLeidas = data.no_leidas;
                    cargarNotificaciones(false);
                    if (typeof cb === 'function') cb();
                });
            }

            if (btnTodas) {
                btnTodas.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fetch(urlMarcarTodas, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: csrfBody().toString()
                    }).then(function() { cargarNotificaciones(false); });
                });
            }

            var dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.addEventListener('show.bs.dropdown', function() { cargarNotificaciones(false); });
            }

            cargarNotificaciones(false);
            setInterval(function() { cargarNotificaciones(true); }, 120000);
        })();
        </script>
        <?php endif; ?>
        
        <?= view('template/footer') ?>
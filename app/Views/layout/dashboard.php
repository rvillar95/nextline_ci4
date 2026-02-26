<?php
if (session()->get('usuario')) {
    $usuario = session()->get('usuario');
}


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
                    <img src="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" alt="VitaSync" class="navbar-logo" style="height: 36px; width: auto;">
                </a>
            </li>
        </ul>
        <ul class="navbar-item flex-row ms-lg-auto ms-0 action-area">
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

<!-- CRONÓMETRO GLOBAL DE CONSULTA ACTIVA -->
<div id="cronometroGlobalConsulta" style="display: none; position: fixed; top: 80px; right: 20px; z-index: 1050; background: linear-gradient(135deg, #4dcba5 0%, #bee6db 100%); padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); color: white; min-width: 280px;">
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
                            <img src="<?= base_url('lib/logo/logo-grande-sin-margen.png') ?>" class="navbar-logo" alt="VitaSync" style="max-height: 64px; width: auto;">
                        </a>
                    </div>
                    <div class="nav-item theme-text">
                        <a href="<?= base_url('dashboard/menu') ?>" class="nav-link"> <span style="color: var(--user-primary, #4dcba5);">N</span>utri<span style="color: var(--user-primary, #4dcba5);">S</span>ync</a>
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
            
            <!-- CSS personalizado para el menú -->
            <style>
                /* Solución simple: permitir que el texto se envuelva en los botones del menú */
                #sidebar ul.menu-categories li.menu > .dropdown-toggle {
                    white-space: normal !important;     /* Permite que el texto se envuelva */
                    word-wrap: break-word !important;   /* Permite que se divida si es necesario */
                    height: auto !important;            /* Altura automática para acomodar múltiples líneas */
                    min-height: 40px !important;       /* Altura mínima para mantener consistencia */
                    padding: 10px 16px !important;      /* Padding adecuado para múltiples líneas */
                }

                /* Ajustar el contenedor del texto para que se expanda */
                #sidebar ul.menu-categories li.menu > .dropdown-toggle > div {
                    flex: 1 !important;                 /* Permitir que se expanda */
                    min-width: 0 !important;            /* Permitir que se contraiga si es necesario */
                }

                /* Ajustar el texto para que se envuelva correctamente */
                #sidebar ul.menu-categories li.menu > .dropdown-toggle > div span {
                    white-space: normal !important;     /* Permitir envoltura del texto */
                    word-wrap: break-word !important;   /* Permitir división de palabras */
                    line-height: 1.3 !important;       /* Altura de línea cómoda para múltiples líneas */
                    display: block !important;          /* Mostrar como bloque para mejor control */
                }

                /* Asegurar que el último elemento del menú se vea bien */
                #sidebar ul.menu-categories li.menu:last-child {
                    margin-bottom: 80px !important;     /* Espacio al final del menú */
                }

                /* Asegurar que todos los elementos del menú tengan el mismo estilo */
                #sidebar ul.menu-categories li.menu {
                    margin-bottom: 5px !important;     /* Espacio consistente entre elementos */
                }

                /* SOLUCIÓN PARA EL SCROLL EN MÓVILES */
                /* Permitir que el sidebar tenga scroll en todas las resoluciones */
                #sidebar {
                    overflow-y: auto !important;        /* Permitir scroll vertical */
                    overflow-x: hidden !important;      /* Ocultar scroll horizontal */
                    -webkit-overflow-scrolling: touch;  /* Scroll suave en iOS */
                    max-height: 100vh !important;       /* Altura máxima de la ventana */
                }

                /* Permitir que el contenedor del menú tenga scroll */
                #sidebar ul.menu-categories {
                    overflow-y: visible !important;     /* Permitir que el contenido sea visible */
                    overflow-x: hidden !important;
                }

                /* En móviles, asegurar que el sidebar tenga altura correcta */
                @media (max-width: 991px) {
                    .sidebar-wrapper {
                        overflow-y: auto !important;
                        -webkit-overflow-scrolling: touch;
                    }
                    
                    #sidebar {
                        height: 100vh !important;
                        overflow-y: auto !important;
                        padding-bottom: 50px !important;
                    }
                }
            </style>
            
            <ul class="list-unstyled menu-categories" id="accordionExample">
                <?php
                $contador = 1;
                // Soporte para $data['data'] (módulos) o $data como lista de ítems (dashboard/menu)
                if (isset($data['data']) && is_array($data['data'])) {
                    $menuItems = $data['data'];
                } elseif (isset($data[0]['menu']) && isset($data[0]['submenu'])) {
                    $menuItems = $data;
                } else {
                    $menuItems = [];
                }
                foreach ($menuItems as $menu) : ?>
                    <?php //if ($modulo['mostrar'] == 'S') { 
                    if (count($menu['submenu']) > 0) { ?>
                        <li class="menu active">
                            <a href="#modulo_<?= $contador ?>" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle collapsed">
                                <div class="">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers">
                                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                        <polyline points="2 17 12 22 22 17"></polyline>
                                        <polyline points="2 12 12 17 22 12"></polyline>
                                    </svg>
                                    <span><?= $menu['menu']['nombre'] ?></span>
                                </div>
                            </a>
                            <ul class="submenu list-unstyled collapse" id="modulo_<?= $contador ?>" data-bs-parent="#accordionExample" style="">
                                <?php foreach ($menu['submenu'] as $submenu) :
                                    // Verificar si el permiso correspondiente en el menú principal está habilitado
                                    $acciones = explode(',', $submenu['accion']);
                                    $mostrar = false;
                                    foreach ($acciones as $accion) {
                                        if (isset($menu['menu'][$accion]) && $menu['menu'][$accion] == 1) {
                                            $mostrar = true;
                                            break;
                                        }
                                    }
                                    if ($mostrar && $submenu['mostrar'] == 'S') : 
                                        // Si la ruta del submódulo empieza con /dashboard/ o dashboard/, usarla directamente (ruta absoluta)
                                        // Si no, concatenarla con la ruta del módulo padre (ruta relativa)
                                        $ruta_submenu = trim($submenu['ruta']);
                                        $ruta_completa = (strpos($ruta_submenu, '/dashboard/') === 0 || strpos($ruta_submenu, 'dashboard/') === 0) 
                                            ? (strpos($ruta_submenu, '/') === 0 ? $ruta_submenu : '/' . $ruta_submenu)
                                            : $menu['menu']['ruta'] . $ruta_submenu;
                                    ?>
                                        <li>
                                            <a href="<?= base_url($ruta_completa) ?>"> <?= $submenu['descripcion'] ?></a>
                                        </li>
                                <?php endif;
                                endforeach; ?>
                            </ul>
                        </li>
                    <?php
                        $contador++;
                    } else {
                    ?>
                        <li class="menu active">

                            <a href="<?= base_url($menu['menu']['ruta']) ?>" aria-expanded="false" class="dropdown-toggle">
                                <div class="aaa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                    <span><?= $menu['menu']['nombre'] ?></span>
                                </div>
                            </a>
                        </li>
                    <?php
                    }
                    ?>
                <?php endforeach; ?>
            </ul>
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
                    
                    <!-- Secciones de Botones de Pago (Mercado Pago) -->
                    <?php echo $this->renderSection("boton_pago/lista"); ?>
                    <?php echo $this->renderSection("boton_pago/crear"); ?>
                    <?php echo $this->renderSection("boton_pago/ver"); ?>
                    <!-- END Section Botones de Pago -->
                    
                    <!-- Secciones de Configuraciones -->
                    <?php echo $this->renderSection("configuracion/index"); ?>
                    <?php echo $this->renderSection("mi_perfil/index"); ?>
                    <!-- END Section Configuraciones -->
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
        // Solo se ejecuta si la configuración tiene activo crear_evento_calendario
        // ============================================
        <?php
        // Verificar si la empresa tiene activo crear_evento_calendario
        $configuracionModel = new \App\Models\EmpresaConfiguracion();
        $usuarioId = session()->get('usuario')['id'] ?? null;
        $crearEventoCalendario = false;
        
        if ($usuarioId) {
            $configuracion = $configuracionModel->obtenerConfiguracionPorUsuario($usuarioId);
            $crearEventoCalendario = ($configuracion['crear_evento_calendario'] ?? 1) == 1;
        }
        ?>
        <?php if ($crearEventoCalendario): ?>
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
                            
                            // Iniciar actualización del cronómetro
                            if (!cronometroInterval) {
                                actualizarCronometro();
                                cronometroInterval = setInterval(actualizarCronometro, 1000);
                            }
                        } else {
                            // Ocultar cronómetro si no hay consulta activa
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
        </script>
        
        <style>
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.3; }
        }
        .blink {
            animation: blink 1s infinite;
        }
        </style>
        
        <?= view('template/footer') ?>
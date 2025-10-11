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
            </li>
            <li class="nav-item theme-text">
                <a href="index.html" class="nav-link"> <span style="color: #6aff99;">N</span>ext<span style="color: #6aff99;">L</span>ine</a>
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
                            <img alt="avatar" src="<?= base_url("lib/src/assets/img/profile-30.png") ?>" class="rounded-circle">
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
                        <a href="./index.html">
                            <img src="" class="navbar-logo" alt="logo">
                        </a>
                    </div>
                    <div class="nav-item theme-text">
                        <a href="./index.html" class="nav-link"> CORK A</a>
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
            </style>
            
            <ul class="list-unstyled menu-categories" id="accordionExample">
                <?php
                $contador = 1;
                foreach ($data as $menu) : ?>
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
                                    if ($mostrar && $submenu['mostrar'] == 'S') : ?>
                                        <li>
                                            <a href="<?= base_url($menu['menu']['ruta'] . $submenu['ruta']) ?>"> <?= $submenu['descripcion'] ?></a>
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
                    <?php echo $this->renderSection("empresa/registro"); ?>
                    <!-- END Section Modulo Testimonio-->
                </div>

            </div>

        </div>
        <?= view('template/footer') ?>
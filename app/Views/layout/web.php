<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?? 'NextLine - Constructor Profesional' ?></title>
    <link rel="icon" href="<?= base_url('lib/images/icon.png') ?>" type="image/gif" sizes="16x16" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="<?= $description ?? 'Constructor profesional con años de experiencia en construcción residencial y comercial' ?>" name="description" />
    <meta content="<?= $keywords ?? 'constructor, construcción, obras, proyectos, remodelación' ?>" name="keywords" />
    <meta content="NextLine" name="author" />
    
    <!-- CSS Files -->
    <link href="<?= base_url('lib/css/bootstrap.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/animate.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/owl.carousel.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/owl.theme.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/owl.transitions.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/magnific-popup.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/jquery.countdown.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/style.css') ?>" rel="stylesheet" type="text/css" />
    <!-- color scheme -->
    <link href="<?= base_url('lib/css/colors/scheme-01.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('lib/css/coloring.css') ?>" rel="stylesheet" type="text/css" />
    <!-- RS5.0 Stylesheet -->
    <link rel="stylesheet" href="<?= base_url('lib/css/settings.css') ?>" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('lib/css/layers.css') ?>" type="text/css" />
    <link rel="stylesheet" href="<?= base_url('lib/css/navigation.css') ?>" type="text/css" />
    
    <!-- Custom CSS for page spacing -->
    <style>
        .page-content {
            margin-top: 120px;
        }
        
        @media (max-width: 768px) {
            .page-content {
                margin-top: 100px;
            }
        }
    </style>
</head>

<body>
    <div id="wrapper">
        <!-- Top Bar -->
        <div id="topbar" class="topbar-noborder">
            <div class="container">
                <div class="topbar-left sm-hide">
                    <span class="topbar-widget tb-social">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-instagram"></i></a>
                        <a href="#"><i class="fa fa-linkedin"></i></a>
                    </span>
                </div>
                <div class="topbar-right">
                    <span class="topbar-widget"><a href="<?= base_url('contacto') ?>">Solicitar Cotización</a></span>
                    <span class="topbar-widget"><a href="<?= base_url('servicios') ?>">Nuestros Servicios</a></span>
                    <span class="topbar-widget"><a href="<?= base_url('proyectos') ?>">Proyectos</a></span>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <!-- Header -->
        <header>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="de-flex sm-pt10">
                            <div class="de-flex-col">
                                <!-- Logo -->
                                <div id="logo">
                                    <a href="<?= base_url() ?>">
                                        <img alt="NextLine Constructor" class="logo" src="<?= base_url('lib/images/logo-light.png') ?>" />
                                        <img alt="NextLine Constructor" class="logo-2" src="<?= base_url('lib/images/logo.png') ?>" />
                                    </a>
                                </div>
                            </div>
                            <div class="de-flex-col header-col-mid">
                                <!-- Navigation -->
                                <ul id="mainmenu">
                                    <li><a href="<?= base_url() ?>">Inicio</a></li>
                                    <li><a href="<?= base_url('servicios') ?>">Servicios</a></li>
                                    <li><a href="<?= base_url('proyectos') ?>">Proyectos</a></li>
                                    <li><a href="<?= base_url('nosotros') ?>">Nosotros</a></li>
                                    <li><a href="<?= base_url('contacto') ?>">Contacto</a></li>
                                </ul>
                            </div>
                            <div class="de-flex-col">
                                <div class="h-phone md-hide">
                                    <span>¿Necesitas Ayuda?</span><i class="fa fa-phone"></i> +56 9 1234 5678
                                </div>
                                <span id="menu-btn"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="no-bottom no-top" id="content">
            <?= $this->renderSection('content') ?>
        </div>

        <!-- Footer -->
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="widget">
                            <a href="<?= base_url() ?>">
                                <img alt="NextLine Constructor" class="img-fluid mb20" src="<?= base_url('lib/images/logo-light.png') ?>" />
                            </a>
                            <address class="s1">
                                <span><i class="id-color fa fa-map-marker fa-lg"></i>Av. Principal 123, Santiago, Chile</span>
                                <span><i class="id-color fa fa-phone fa-lg"></i>+56 9 1234 5678</span>
                                <span><i class="id-color fa fa-envelope-o fa-lg"></i><a href="mailto:contacto@nextline.cl">contacto@nextline.cl</a></span>
                                <span><i class="id-color fa fa-whatsapp fa-lg"></i><a href="https://wa.me/56912345678">WhatsApp</a></span>
                            </address>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h4 class="id-color mb20">Servicios</h4>
                        <ul class="ul-style-2">
                            <li><a href="<?= base_url('servicios') ?>">Construcción Residencial</a></li>
                            <li><a href="<?= base_url('servicios') ?>">Construcción Comercial</a></li>
                            <li><a href="<?= base_url('servicios') ?>">Remodelaciones</a></li>
                            <li><a href="<?= base_url('servicios') ?>">Ampliaciones</a></li>
                            <li><a href="<?= base_url('servicios') ?>">Consultoría</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h4 class="id-color">Newsletter</h4>
                        <p>Suscríbete a nuestro boletín para recibir las últimas noticias, actualizaciones y ofertas especiales.</p>
                        <form action="<?= base_url('newsletter/suscribir') ?>" class="row" method="post">
                            <?= csrf_field() ?>
                            <div class="col text-center">
                                <input class="form-control" name="email" placeholder="Ingresa tu email" type="email" required />
                                <button type="submit" id="btn-submit"><i class="fa fa-long-arrow-right"></i></button>
                                <div class="clearfix"></div>
                            </div>
                        </form>
                        <div class="spacer-10"></div>
                        <small>Tu email está seguro con nosotros. No enviamos spam.</small>
                    </div>
                </div>
            </div>
            <div class="subfooter">
                <div class="container">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-6">
                            &copy; Copyright <?= date('Y') ?> - NextLine Constructor. Todos los derechos reservados.
                        </div>
                        <div class="col-lg-6 text-lg-end">
                            <div class="social-icons">
                                <a href="#"><i class="fa fa-facebook fa-lg"></i></a>
                                <a href="#"><i class="fa fa-twitter fa-lg"></i></a>
                                <a href="#"><i class="fa fa-linkedin fa-lg"></i></a>
                                <a href="#"><i class="fa fa-instagram fa-lg"></i></a>
                                <a href="#"><i class="fa fa-youtube fa-lg"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Preloader -->
        <div id="preloader">
            <div class="spinner">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
    </div>

    <!-- Javascript Files -->
    <script src="<?= base_url('lib/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/bootstrap.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/wow.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.isotope.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/easing.js') ?>"></script>
    <script src="<?= base_url('lib/js/owl.carousel.js') ?>"></script>
    <script src="<?= base_url('lib/js/validation.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.magnific-popup.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/enquire.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.stellar.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.plugin.js') ?>"></script>
    <script src="<?= base_url('lib/js/typed.js') ?>"></script>
    <script src="<?= base_url('lib/js/jarallax.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.countTo.js') ?>"></script>
    <script src="<?= base_url('lib/js/jquery.countdown.js') ?>"></script>
    <script src="<?= base_url('lib/js/mdb.min.js') ?>"></script>
    <script src="<?= base_url('lib/js/designesia.js') ?>"></script>
</body>
</html>

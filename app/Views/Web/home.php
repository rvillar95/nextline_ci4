<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section id="de-carousel" class="no-top no-bottom carousel slide carousel-fade shadow-2-strong" data-mdb-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-mdb-target="#de-carousel" data-mdb-slide-to="0" class="active"></li>
        <li data-mdb-target="#de-carousel" data-mdb-slide-to="1"></li>
        <li data-mdb-target="#de-carousel" data-mdb-slide-to="2"></li>
    </ol>

    <!-- Inner -->
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active" data-bgimage="url(<?= base_url('lib/images/slider/construction1.jpg') ?>)">
            <div class="mask">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="container text-white">
                        <div class="row">
                            <div class="col-lg-5">
                                <h4 class="id-color wow fadeInUp">¿Necesitas Construir?</h4>
                                <h1 class="mb-3 wow fadeInUp">Construimos tus Sueños</h1>
                                <p class="lead wow fadeInUp" data-wow-delay=".3s">
                                    Más de 15 años de experiencia en construcción residencial y comercial. 
                                    Proyectos de calidad, cumplimiento de plazos y garantía total.
                                </p>
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('contacto') ?>" class="btn-custom wow fadeInUp" data-wow-delay=".6s">Solicitar Cotización</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="carousel-item" data-bgimage="url(<?= base_url('lib/images/slider/construction2.jpg') ?>)">
            <div class="mask">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="container text-white">
                        <div class="row">
                            <div class="col-lg-5">
                                <h4 class="id-color wow fadeInUp">Especialistas en Construcción</h4>
                                <h1 class="mb-3 wow fadeInUp">Calidad Garantizada</h1>
                                <p class="lead wow fadeInUp" data-wow-delay=".3s">
                                    Desde casas familiares hasta edificios comerciales. 
                                    Materiales de primera calidad y mano de obra especializada.
                                </p>
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('proyectos') ?>" class="btn-custom wow fadeInUp" data-wow-delay=".6s">Ver Proyectos</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="carousel-item" data-bgimage="url(<?= base_url('lib/images/slider/construction3.jpg') ?>)">
            <div class="mask">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="container text-white">
                        <div class="row">
                            <div class="col-lg-5">
                                <h4 class="id-color wow fadeInUp">Remodelaciones</h4>
                                <h1 class="mb-3 wow fadeInUp">Transformamos Espacios</h1>
                                <p class="lead wow fadeInUp" data-wow-delay=".3s">
                                    Remodelaciones completas, ampliaciones y mejoras. 
                                    Modernizamos tu hogar o negocio con diseños innovadores.
                                </p>
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('servicios') ?>" class="btn-custom wow fadeInUp" data-wow-delay=".6s">Nuestros Servicios</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <a class="carousel-control-prev" href="#de-carousel" role="button" data-mdb-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#de-carousel" role="button" data-mdb-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Siguiente</span>
    </a>
</section>

<!-- Call to Action -->
<section class="pt40 pb40 bg-color text-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 mb-sm-30 text-lg-start text-sm-center">
                <h3 class="no-bottom">
                    ¡Contáctanos Ahora! Obtén una Consulta Gratuita para tu Proyecto.
                </h3>
            </div>
            <div class="col-md-4 text-lg-end rtl-lg-start text-sm-center">
                <a href="<?= base_url('contacto') ?>" class="btn-custom btn-black light">Solicitar Consulta</a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="section-highlight" class="relative text-light" data-bgcolor="#111111">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="p-title">Bienvenidos</span><br />
                <h2>Experiencia.<br />Calidad. Resultados.</h2>
                <div class="small-border sm-left"></div>
            </div>
            <div class="col-md-8">
                <p>
                    Con más de 15 años de experiencia en el rubro de la construcción, 
                    hemos desarrollado proyectos residenciales y comerciales de alta calidad. 
                    Nuestro compromiso es entregar obras que superen las expectativas de nuestros clientes, 
                    cumpliendo con los más altos estándares de calidad y seguridad.
                </p>
            </div>
        </div>
        <div class="spacer-double"></div>
    </div>
</section>

<!-- Services Section -->
<section class="no-top relative z1000">
    <div class="container">
        <div class="row mt-100">
            <?php if (!empty($servicios_destacados)): ?>
                <?php foreach ($servicios_destacados as $index => $servicio): ?>
                    <div class="col-md-4 mb-sm-30 wow fadeInRight" data-wow-delay="<?= ($index + 1) * 0.2 ?>s">
                        <div class="mask">
                            <div class="cover">
                                <div class="c-inner">
                                    <h3>
                                        <i class="icofont-home"></i><span><?= esc($servicio->nombre) ?></span>
                                    </h3>
                                    <p><?= esc($servicio->descripcionCorta) ?></p>
                                    <div class="spacer20"></div>
                                    <a href="<?= base_url('servicios') ?>" class="btn-custom capsule">Ver más</a>
                                </div>
                            </div>
                            <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-responsive" />
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Servicios por defecto -->
                <div class="col-md-4 mb-sm-30 wow fadeInRight" data-wow-delay=".2s">
                    <div class="mask">
                        <div class="cover">
                            <div class="c-inner">
                                <h3><i class="icofont-home"></i><span>Construcción Residencial</span></h3>
                                <p>Casas familiares, condominios y proyectos habitacionales con los más altos estándares de calidad.</p>
                                <div class="spacer20"></div>
                                <a href="<?= base_url('servicios') ?>" class="btn-custom capsule">Ver más</a>
                            </div>
                        </div>
                        <img src="<?= base_url('lib/images/services/construction1.jpg') ?>" alt="Construcción Residencial" class="img-responsive" />
                    </div>
                </div>
                <div class="col-md-4 mb-sm-30 wow fadeInRight" data-wow-delay=".4s">
                    <div class="mask">
                        <div class="cover">
                            <div class="c-inner">
                                <h3><i class="icofont-building"></i><span>Construcción Comercial</span></h3>
                                <p>Edificios de oficinas, locales comerciales y proyectos industriales con tecnología moderna.</p>
                                <div class="spacer20"></div>
                                <a href="<?= base_url('servicios') ?>" class="btn-custom capsule">Ver más</a>
                            </div>
                        </div>
                        <img src="<?= base_url('lib/images/services/construction2.jpg') ?>" alt="Construcción Comercial" class="img-responsive" />
                    </div>
                </div>
                <div class="col-md-4 mb-sm-30 wow fadeInRight" data-wow-delay=".6s">
                    <div class="mask">
                        <div class="cover">
                            <div class="c-inner">
                                <h3><i class="icofont-tools"></i><span>Remodelaciones</span></h3>
                                <p>Transformamos espacios existentes con diseños modernos y funcionales que se adaptan a tus necesidades.</p>
                                <div class="spacer20"></div>
                                <a href="<?= base_url('servicios') ?>" class="btn-custom capsule">Ver más</a>
                            </div>
                        </div>
                        <img src="<?= base_url('lib/images/services/remodelation.jpg') ?>" alt="Remodelaciones" class="img-responsive" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="jarallax text-light">
    <img src="<?= base_url('lib/images/background/construction-bg.jpg') ?>" class="jarallax-img" alt="" />
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <span class="p-title">Nuestros Logros</span><br />
                <h2>¿Qué hemos logrado?</h2>
                <div class="small-border sm-left"></div>
                <p>
                    Más de una década construyendo sueños y transformando espacios.
                </p>
            </div>
            <div class="col-md-8 offset-md-1">
                <div class="row">
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay="0s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="150" data-speed="3000">0</span>+</h3>
                            <h5 class="id-color">Proyectos Completados</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay=".25s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="15" data-speed="3000">0</span></h3>
                            <h5 class="id-color">Años de Experiencia</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay=".4s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="98" data-speed="3000">0</span>%</h3>
                            <h5 class="id-color">Clientes Satisfechos</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay=".6s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="50" data-speed="3000">0</span>+</h3>
                            <h5 class="id-color">Empleados Especializados</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay=".8s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="24" data-speed="3000">0</span>/7</h3>
                            <h5 class="id-color">Soporte Disponible</h5>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 wow fadeInRight mb30" data-wow-delay="1s">
                        <div class="de_count">
                            <h3><span class="timer" data-to="100" data-speed="3000">0</span>%</h3>
                            <h5 class="id-color">Garantía Total</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Projects Gallery -->
<section id="section-practice-areas">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <h2>Nuestros Proyectos</h2>
                    <div class="small-border"></div>
                </div>
            </div>
            <div class="col-md-6 offset-md-3 text-center">
                <p>
                    Cada proyecto es único y representa nuestro compromiso con la excelencia. 
                    Desde casas familiares hasta edificios comerciales, cada obra refleja nuestra pasión por la construcción.
                </p>
            </div>
            <div class="spacer-single"></div>
            
            <?php if (!empty($proyectos_destacados)): ?>
                <div class="row">
                    <?php foreach ($proyectos_destacados as $proyecto): ?>
                        <div class="col-md-4 mb-4">
                            <div class="gallery-item">
                                <img src="<?= base_url($proyecto->portada) ?>" alt="<?= esc($proyecto->nombre) ?>" class="img-fluid" />
                                <div class="gallery-overlay">
                                    <h4><?= esc($proyecto->nombre) ?></h4>
                                    <p><?= esc($proyecto->descripcion) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="col-md-12 text-center">
                    <p>Próximamente mostraremos nuestros proyectos destacados.</p>
                    <a href="<?= base_url('proyectos') ?>" class="btn-custom">Ver Todos los Proyectos</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section aria-label="section" class="jarallax text-light">
    <img src="<?= base_url('lib/images/background/testimonials-bg.jpg') ?>" class="jarallax-img" alt="" />
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center text-light">
                    <h2>Lo que Dicen Nuestros Clientes</h2>
                    <div class="small-border"></div>
                </div>
                <div class="owl-carousel owl-theme" id="testimonial-carousel">
                    <div class="item">
                        <div class="de_testi opt-2 review">
                            <blockquote>
                                <i class="fa fa-quote-left id-color"></i>
                                <h3>Excelente trabajo</h3>
                                <p>
                                    La construcción de nuestra casa superó todas nuestras expectativas. 
                                    Calidad excepcional y cumplimiento perfecto de plazos.
                                </p>
                                <div class="de_testi_by">
                                    <span>María González, Propietaria</span>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                    <div class="item">
                        <div class="de_testi opt-2 review">
                            <blockquote>
                                <i class="fa fa-quote-left id-color"></i>
                                <h3>Profesionales de confianza</h3>
                                <p>
                                    Remodelaron completamente nuestro local comercial. 
                                    El resultado es espectacular y el proceso fue muy profesional.
                                </p>
                                <div class="de_testi_by">
                                    <span>Carlos Rodríguez, Empresario</span>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                    <div class="item">
                        <div class="de_testi opt-2 review">
                            <blockquote>
                                <i class="fa fa-quote-left id-color"></i>
                                <h3>Recomendados al 100%</h3>
                                <p>
                                    Construyeron nuestro edificio de oficinas con la más alta calidad. 
                                    Definitivamente los recomiendo para cualquier proyecto.
                                </p>
                                <div class="de_testi_by">
                                    <span>Ana Martínez, Arquitecta</span>
                                </div>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

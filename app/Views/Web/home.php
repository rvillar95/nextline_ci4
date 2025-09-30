<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<?php
// Función helper para mostrar estrellas
function mostrarEstrellas($calificacion) {
    $html = '<div class="estrellas-calificacion">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $calificacion) {
            $html .= '<i class="fa fa-star text-warning" style="font-size: 15px"></i>';
        } else {
            $html .= '<i class="fa fa-star-o text-muted" style="font-size: 15px"></i>';
        }
    }
    $html .= '</div>';
    return $html;
}
?>

<!-- Hero Section Moderno -->
<section class="hero-section-modern" style="background: linear-gradient(to bottom, #1d2844 30%, #4a5f7a 100%) !important; padding: 120px 0 100px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-modern"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content-modern text-white">
                    <div class="hero-badge">
                        <i class="fas fa-award"></i>
                        <span>MANSANCHEZ</span>
                    </div>
                    <h1 class="hero-title-modern mb-4">Construimos tus Sueños</h1>
                    <p class="hero-subtitle-modern lead mb-4">
                        Más de 15 años de experiencia en construcción residencial y comercial. 
                        Proyectos de calidad, cumplimiento de plazos y garantía total.
                    </p>
                    <div class="hero-stats-modern mb-4">
                        <div class="stat-item-modern">
                            <div class="stat-number-modern">15+</div>
                            <div class="stat-label-modern">Años de Experiencia</div>
                        </div>
                        <div class="stat-item-modern">
                            <div class="stat-number-modern">200+</div>
                            <div class="stat-label-modern">Proyectos Completados</div>
                        </div>
                        <div class="stat-item-modern">
                            <div class="stat-number-modern">100%</div>
                            <div class="stat-label-modern">Satisfacción Garantizada</div>
                        </div>
                    </div>
                    <div class="hero-actions-modern">
                        <a href="<?= base_url('contacto') ?>" class="btn-hero-modern btn-primary-modern">
                            <i class="fas fa-calculator"></i> Solicitar Cotización
                        </a>
                        <a href="<?= base_url('proyectos') ?>" class="btn-hero-modern btn-secondary-modern">
                            <i class="fas fa-eye"></i> Ver Proyectos
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image-modern text-center">
                    <div class="hero-icon-container">
                        <i class="fas fa-home" style="font-size: 8rem; color: rgba(240, 132, 26, 0.3);"></i>
                    </div>
                    <div class="floating-elements">
                        <div class="floating-icon floating-icon-1">
                            <i class="fas fa-hammer"></i>
                        </div>
                        <div class="floating-icon floating-icon-2">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="floating-icon floating-icon-3">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 80px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="cta-content-modern text-white">
                    <h3 class="cta-title-modern mb-3">
                        <i class="fas fa-phone-alt me-3"></i>
                        ¡Contáctanos Ahora!
                    </h3>
                    <p class="cta-subtitle-modern mb-0">
                        MANSANCHEZ te ofrece una Consulta Gratuita para tu Proyecto
                    </p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end text-center">
                <a href="<?= base_url('contacto') ?>" class="btn-cta-modern">
                    <i class="fas fa-comments"></i> Solicitar Consulta
                </a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #2c3e50 100%); padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-content-modern text-white">
                    <div class="about-badge">
                        <i class="fas fa-handshake"></i>
                        <span>Bienvenidos</span>
                    </div>
                    <h2 class="about-title-modern mb-4">
                        <span class="text-white">Experiencia.</span><br />
                        <span class="text-warning">Calidad.</span><br />
                        <span class="text-white">Resultados.</span>
                    </h2>
                    <div class="about-divider"></div>
                    <p class="about-description-modern lead">
                        Con más de 15 años de experiencia en el rubro de la construcción, 
                        hemos desarrollado proyectos residenciales y comerciales de alta calidad. 
                        Nuestro compromiso es entregar obras que superen las expectativas de nuestros clientes, 
                        cumpliendo con los más altos estándares de calidad y seguridad.
                    </p>
                    <div class="about-features-modern">
                        <div class="feature-item-modern">
                            <i class="fas fa-check-circle"></i>
                            <span>Materiales de Primera Calidad</span>
                        </div>
                        <div class="feature-item-modern">
                            <i class="fas fa-check-circle"></i>
                            <span>Cumplimiento de Plazos</span>
                        </div>
                        <div class="feature-item-modern">
                            <i class="fas fa-check-circle"></i>
                            <span>Garantía Total</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-image-modern text-center">
                    <div class="about-icon-container">
                        <i class="fas fa-building" style="font-size: 6rem; color: rgba(240, 132, 26, 0.3);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section-modern" style="background: #f8f9fa; padding: 100px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <div class="section-header-modern">
                    <h2 class="section-title-modern">Nuestros Servicios</h2>
                    <div class="section-divider-modern"></div>
                    <p class="section-subtitle-modern">
                        Soluciones profesionales de construcción para hacer realidad tus proyectos más ambiciosos
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row services-grid-modern">
            <?php if (!empty($servicios_destacados)): ?>
                <?php foreach (array_slice($servicios_destacados, 0, 3) as $index => $servicio): ?>
                    <div class="col-lg-4 col-md-6 service-item-modern">
                        <div class="service-card-modern">
                            <div class="service-image-modern">
                                <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-fluid">
                                <div class="service-overlay-modern">
                                    <div class="service-icon-modern">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="service-actions-overlay">
                                        <a href="<?= base_url('servicios') ?>" class="btn-overlay-modern">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="service-content-modern">
                                <h3 class="service-title-modern"><?= esc($servicio->nombre) ?></h3>
                                <p class="service-description-modern"><?= esc($servicio->descripcionCorta) ?></p>
                                <div class="service-actions-modern">
                                    <a href="<?= base_url('servicios') ?>" class="btn-service-modern">
                                        <i class="fas fa-info-circle"></i> Ver más
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Servicios por defecto -->
                <div class="col-lg-4 col-md-6 service-item-modern">
                    <div class="service-card-modern">
                        <div class="service-image-modern">
                            <img src="<?= base_url('lib/images/slider/construccion_4.jpg') ?>" alt="Construcción Residencial" class="img-fluid">
                            <div class="service-overlay-modern">
                                <div class="service-icon-modern">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="service-actions-overlay">
                                    <a href="<?= base_url('servicios') ?>" class="btn-overlay-modern">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="service-content-modern">
                            <h3 class="service-title-modern">Construcción Residencial</h3>
                            <p class="service-description-modern">Casas familiares, condominios y proyectos habitacionales con los más altos estándares de calidad.</p>
                            <div class="service-actions-modern">
                                <a href="<?= base_url('servicios') ?>" class="btn-service-modern">
                                    <i class="fas fa-info-circle"></i> Ver más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 service-item-modern">
                    <div class="service-card-modern">
                        <div class="service-image-modern">
                            <img src="<?= base_url('lib/images/slider/construccion_4.jpg') ?>" alt="Construcción Comercial" class="img-fluid">
                            <div class="service-overlay-modern">
                                <div class="service-icon-modern">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="service-actions-overlay">
                                    <a href="<?= base_url('servicios') ?>" class="btn-overlay-modern">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="service-content-modern">
                            <h3 class="service-title-modern">Construcción Comercial</h3>
                            <p class="service-description-modern">Edificios de oficinas, locales comerciales y proyectos industriales con tecnología moderna.</p>
                            <div class="service-actions-modern">
                                <a href="<?= base_url('servicios') ?>" class="btn-service-modern">
                                    <i class="fas fa-info-circle"></i> Ver más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 service-item-modern">
                    <div class="service-card-modern">
                        <div class="service-image-modern">
                            <img src="<?= base_url('lib/images/services/remodelation.jpg') ?>" alt="Remodelaciones" class="img-fluid">
                            <div class="service-overlay-modern">
                                <div class="service-icon-modern">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="service-actions-overlay">
                                    <a href="<?= base_url('servicios') ?>" class="btn-overlay-modern">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="service-content-modern">
                            <h3 class="service-title-modern">Remodelaciones</h3>
                            <p class="service-description-modern">Transformamos espacios existentes con diseños modernos y funcionales que se adaptan a tus necesidades.</p>
                            <div class="service-actions-modern">
                                <a href="<?= base_url('servicios') ?>" class="btn-service-modern">
                                    <i class="fas fa-info-circle"></i> Ver más
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="<?= base_url('servicios') ?>" class="btn-all-services-modern">
                <i class="fas fa-th-large"></i> Ver Todos los Servicios
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section-modern" style="background: linear-gradient(135deg, #1d2844 0%, #2c3e50 100%); padding: 100px 0; position: relative; overflow: hidden;">
    <div class="stats-pattern-modern"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="stats-content-modern text-white">
                    <div class="stats-badge">
                        <i class="fas fa-trophy"></i>
                        <span>Nuestros Logros</span>
                    </div>
                    <h2 class="stats-title-modern mb-4">
                        <span class="text-white">¿Qué hemos</span><br />
                        <span class="text-warning">logrado?</span>
                    </h2>
                    <div class="stats-divider"></div>
                    <p class="stats-description-modern">
                        Más de una década construyendo sueños y transformando espacios con excelencia y dedicación.
                    </p>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row stats-grid-modern">
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="150" data-speed="3000">0</span>+
                            </div>
                            <div class="stat-label-modern">Proyectos Completados</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="15" data-speed="3000">0</span>
                            </div>
                            <div class="stat-label-modern">Años de Experiencia</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-smile"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="98" data-speed="3000">0</span>%
                            </div>
                            <div class="stat-label-modern">Clientes Satisfechos</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="50" data-speed="3000">0</span>+
                            </div>
                            <div class="stat-label-modern">Empleados Especializados</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="24" data-speed="3000">0</span>/7
                            </div>
                            <div class="stat-label-modern">Soporte Disponible</div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 stat-item-modern">
                        <div class="stat-card-modern">
                            <div class="stat-icon-modern">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="stat-number-modern">
                                <span class="timer" data-to="100" data-speed="3000">0</span>%
                            </div>
                            <div class="stat-label-modern">Garantía Total</div>
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
                <div class="row projects-grid">
                    <?php foreach (array_slice($proyectos_destacados, 0, 3) as $proyecto): ?>
                        <div class="col-lg-4 col-md-6 project-item">
                            <div class="project-card">
                                <div class="project-image">
                                    <?php if (!empty($proyecto->imagen_portada)): ?>
                                        <img src="<?= base_url($proyecto->imagen_portada->ruta) ?>" alt="<?= esc($proyecto->nombre) ?>" class="img-fluid" />
                                    <?php else: ?>
                                        <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" alt="<?= esc($proyecto->nombre) ?>" class="img-fluid" />
                                    <?php endif; ?>
                                    <div class="project-overlay">
                                        <div class="project-badge">
                                            <?= ucfirst($proyecto->tipo_proyecto) ?>
                                        </div>
                                        <div class="project-actions">
                                            <a href="<?= base_url('proyectos/' . $proyecto->slug) ?>" class="btn-project">
                                                <i class="fas fa-eye"></i> Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="project-content">
                                    <h4 class="project-title"><?= esc($proyecto->nombre) ?></h4>
                                    <p class="project-description"><?= esc($proyecto->descripcion_corta) ?></p>
                                    
                                    <div class="project-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?= esc($proyecto->ubicacion) ?></span>
                                        </div>
                                        <?php if (!empty($proyecto->area_construida)): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-ruler-combined"></i>
                                            <span><?= number_format($proyecto->area_construida, 0, ',', '.') ?> m²</span>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($proyecto->fecha_finalizacion)): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-check"></i>
                                            <span><?= date('Y', strtotime($proyecto->fecha_finalizacion)) ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($proyecto->destacado): ?>
                                    <div class="project-featured">
                                        <i class="fas fa-star"></i> Proyecto Destacado
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-5">
                    <a href="<?= base_url('proyectos') ?>" class="btn-custom btn-large">
                        <i class="fas fa-th-large"></i> Ver Más Proyectos
                    </a>
                </div>
            <?php else: ?>
                <div class="col-md-12 text-center">
                    <div class="no-projects">
                        <i class="fas fa-hammer fa-3x text-muted mb-3"></i>
                        <h4>Próximamente</h4>
                        <p>Estamos preparando nuestros proyectos destacados para mostrarte.</p>
                        <a href="<?= base_url('proyectos') ?>" class="btn-custom">Ver Todos los Proyectos</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="testimonials-section-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 100px 0; position: relative; overflow: hidden;">
    <div class="testimonials-pattern-modern"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <div class="testimonials-header-modern text-white">
                    <h2 class="testimonials-title-modern">Lo que Dicen Nuestros Clientes</h2>
                    <div class="testimonials-divider-modern"></div>
                    <p class="testimonials-subtitle-modern">
                        La satisfacción de nuestros clientes es nuestra mayor recompensa
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="owl-carousel owl-theme" id="testimonial-carousel-modern">
                    <?php if (!empty($testimonios_destacados)): ?>
                        <?php foreach ($testimonios_destacados as $testimonio): ?>
                            <div class="item">
                                <div class="testimonial-card-modern">
                                    <div class="testimonial-content-modern">
                                        <div class="testimonial-quote-modern">
                                            <i class="fas fa-quote-left"></i>
                                        </div>
                                        <p class="testimonial-text-modern"><?= esc($testimonio->testimonio) ?></p>
                                        <div class="testimonial-rating-modern">
                                            <?= mostrarEstrellas($testimonio->calificacion) ?>
                                        </div>
                                    </div>
                                    <div class="testimonial-author-modern">
                                        <div class="author-info-modern">
                                            <h4 class="author-name-modern"><?= esc($testimonio->nombre) ?></h4>
                                            <p class="author-details-modern">
                                                <?= !empty($testimonio->cargo) ? esc($testimonio->cargo) : '' ?>
                                                <?= !empty($testimonio->empresa) ? ' - ' . esc($testimonio->empresa) : '' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Testimonios por defecto -->
                        <div class="item">
                            <div class="testimonial-card-modern">
                                <div class="testimonial-content-modern">
                                    <div class="testimonial-quote-modern">
                                        <i class="fas fa-quote-left"></i>
                                    </div>
                                    <p class="testimonial-text-modern">
                                        La construcción de nuestra casa superó todas nuestras expectativas. 
                                        Calidad excepcional y cumplimiento perfecto de plazos.
                                    </p>
                                    <div class="testimonial-rating-modern">
                                        <?= mostrarEstrellas(5) ?>
                                    </div>
                                </div>
                                <div class="testimonial-author-modern">
                                    <div class="author-info-modern">
                                        <h4 class="author-name-modern">María González</h4>
                                        <p class="author-details-modern">Propietaria</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimonial-card-modern">
                                <div class="testimonial-content-modern">
                                    <div class="testimonial-quote-modern">
                                        <i class="fas fa-quote-left"></i>
                                    </div>
                                    <p class="testimonial-text-modern">
                                        Remodelaron completamente nuestro local comercial. 
                                        El resultado es espectacular y el proceso fue muy profesional.
                                    </p>
                                    <div class="testimonial-rating-modern">
                                        <?= mostrarEstrellas(5) ?>
                                    </div>
                                </div>
                                <div class="testimonial-author-modern">
                                    <div class="author-info-modern">
                                        <h4 class="author-name-modern">Carlos Rodríguez</h4>
                                        <p class="author-details-modern">Empresario</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="item">
                            <div class="testimonial-card-modern">
                                <div class="testimonial-content-modern">
                                    <div class="testimonial-quote-modern">
                                        <i class="fas fa-quote-left"></i>
                                    </div>
                                    <p class="testimonial-text-modern">
                                        Construyeron nuestro edificio de oficinas con la más alta calidad. 
                                        Definitivamente los recomiendo para cualquier proyecto.
                                    </p>
                                    <div class="testimonial-rating-modern">
                                        <?= mostrarEstrellas(5) ?>
                                    </div>
                                </div>
                                <div class="testimonial-author-modern">
                                    <div class="author-info-modern">
                                        <h4 class="author-name-modern">Ana Martínez</h4>
                                        <p class="author-details-modern">Arquitecta</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== ESTILOS MODERNOS PARA HOME ===== */

/* Hero Section Moderno */
.hero-section-modern {
    position: relative;
    overflow: hidden;
    background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%) !important;
}

.hero-pattern-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 12px 25px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.hero-title-modern {
    font-size: 3.5rem;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 25px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-modern {
    font-size: 1.3rem;
    font-weight: 400;
    line-height: 1.6;
    opacity: 0.95;
    margin-bottom: 40px;
}

.hero-stats-modern {
    display: flex;
    gap: 40px;
    margin-bottom: 40px;
}

.stat-item-modern {
    text-align: center;
}

.stat-number-modern {
    font-size: 2.5rem;
    font-weight: 900;
    color: #f0841a;
    line-height: 1;
    margin-bottom: 8px;
    text-shadow: 0 2px 10px rgba(240, 132, 26, 0.3);
}

.stat-label-modern {
    font-size: 0.9rem;
    font-weight: 600;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hero-actions-modern {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.btn-hero-modern {
    padding: 18px 35px;
    border-radius: 35px;
    font-size: 1.1rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    font-weight: 700;
}

.btn-primary-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.btn-secondary-modern {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-secondary-modern:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-3px);
    color: white;
    text-decoration: none;
}

.hero-image-modern {
    position: relative;
}

.hero-icon-container {
    position: relative;
    z-index: 2;
}

.floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.floating-icon {
    position: absolute;
    width: 60px;
    height: 60px;
    background: rgba(240, 132, 26, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #f0841a;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(240, 132, 26, 0.3);
    animation: float 6s ease-in-out infinite;
}

.floating-icon-1 {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.floating-icon-2 {
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.floating-icon-3 {
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* Call to Action Moderno */
.cta-section-modern {
    position: relative;
    overflow: hidden;
}

.cta-content-modern {
    position: relative;
    z-index: 2;
}

.cta-title-modern {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.cta-subtitle-modern {
    font-size: 1.2rem;
    font-weight: 400;
    opacity: 0.95;
}

.btn-cta-modern {
    background: rgba(255, 255, 255, 0.95);
    color: #f0841a;
    padding: 18px 35px;
    border-radius: 35px;
    font-size: 1.1rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.btn-cta-modern:hover {
    background: white;
    color: #f0841a;
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    text-decoration: none;
}

/* About Section Moderno */
.about-section-modern {
    position: relative;
    overflow: hidden;
}

.about-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 12px 25px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.about-title-modern {
    font-size: 3rem;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 25px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.about-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 2px;
    margin-bottom: 30px;
}

.about-description-modern {
    font-size: 1.2rem;
    font-weight: 400;
    line-height: 1.7;
    opacity: 0.95;
    margin-bottom: 40px;
}

.about-features-modern {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.feature-item-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 1.1rem;
    font-weight: 600;
}

.feature-item-modern i {
    color: #f0841a;
    font-size: 1.3rem;
}

/* Services Section Moderno */
.services-section-modern {
    position: relative;
}

.section-header-modern {
    margin-bottom: 60px;
}

.section-title-modern {
    font-size: 3rem;
    font-weight: 900;
    color: #1d2844;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.section-divider-modern {
    width: 100px;
    height: 4px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 2px;
    margin: 0 auto 25px;
}

.section-subtitle-modern {
    font-size: 1.2rem;
    color: #666;
    font-weight: 400;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

.services-grid-modern {
    margin: 0 -20px;
}

.service-item-modern {
    padding: 0 20px;
    margin-bottom: 40px;
}

.service-card-modern {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.4s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.service-card-modern:hover {
    transform: translateY(-15px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    border-color: rgba(240, 132, 26, 0.2);
}

.service-image-modern {
    position: relative;
    overflow: hidden;
    height: 250px;
}

.service-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.service-card-modern:hover .service-image-modern img {
    transform: scale(1.08);
}

.service-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(29, 40, 68, 0.85), rgba(240, 132, 26, 0.85));
    opacity: 0;
    transition: all 0.4s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 25px;
}

.service-card-modern:hover .service-overlay-modern {
    opacity: 1;
}

.service-icon-modern {
    background: rgba(255, 255, 255, 0.95);
    color: #1d2844;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(15px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    align-self: flex-start;
}

.service-actions-overlay {
    align-self: flex-end;
}

.btn-overlay-modern {
    background: rgba(255, 255, 255, 0.95);
    color: #1d2844;
    padding: 15px 25px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(15px);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-overlay-modern:hover {
    background: white;
    color: #f0841a;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.service-content-modern {
    padding: 30px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.service-title-modern {
    font-size: 1.4rem;
    font-weight: 800;
    color: #1d2844;
    margin-bottom: 15px;
    line-height: 1.3;
}

.service-description-modern {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
    flex-grow: 1;
}

.service-actions-modern {
    margin-top: auto;
}

.btn-service-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    color: white;
    padding: 15px 25px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(29, 40, 68, 0.3);
}

.btn-service-modern:hover {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
}

.btn-all-services-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    padding: 18px 45px;
    border-radius: 35px;
    font-size: 1.2rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.btn-all-services-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

/* Stats Section Moderno */
.stats-section-modern {
    position: relative;
    overflow: hidden;
}

.stats-pattern-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="stats-grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23stats-grain)"/></svg>');
    opacity: 0.3;
}

.stats-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 12px 25px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 30px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.stats-title-modern {
    font-size: 3rem;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 25px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.stats-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 2px;
    margin-bottom: 30px;
}

.stats-description-modern {
    font-size: 1.2rem;
    font-weight: 400;
    line-height: 1.7;
    opacity: 0.95;
}

.stats-grid-modern {
    margin: 0 -15px;
}

.stat-item-modern {
    padding: 0 15px;
    margin-bottom: 30px;
}

.stat-card-modern {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    height: 100%;
}

.stat-card-modern:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(240, 132, 26, 0.3);
}

.stat-icon-modern {
    width: 60px;
    height: 60px;
    background: rgba(240, 132, 26, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #f0841a;
    margin: 0 auto 20px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.stat-number-modern {
    font-size: 2.5rem;
    font-weight: 900;
    color: white;
    line-height: 1;
    margin-bottom: 10px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.stat-label-modern {
    font-size: 0.9rem;
    font-weight: 600;
    color: white;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Testimonials Section Moderno */
.testimonials-section-modern {
    position: relative;
    overflow: hidden;
}

.testimonials-pattern-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="testimonials-grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.03)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23testimonials-grain)"/></svg>');
    opacity: 0.3;
}

.testimonials-title-modern {
    font-size: 3rem;
    font-weight: 900;
    margin-bottom: 20px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.testimonials-divider-modern {
    width: 100px;
    height: 4px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 2px;
    margin: 0 auto 25px;
}

.testimonials-subtitle-modern {
    font-size: 1.2rem;
    font-weight: 400;
    opacity: 0.95;
    line-height: 1.6;
}

.testimonial-card-modern {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 25px;
    padding: 40px;
    margin: 20px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.testimonial-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
}

.testimonial-quote-modern {
    font-size: 3rem;
    color: #f0841a;
    margin-bottom: 20px;
    opacity: 0.7;
}

.testimonial-text-modern {
    font-size: 1.2rem;
    line-height: 1.7;
    color: #333;
    margin-bottom: 25px;
    font-style: italic;
}

.testimonial-rating-modern {
    margin-bottom: 25px;
}

.testimonial-author-modern {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    padding-top: 25px;
}

.author-name-modern {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 5px;
}

.author-details-modern {
    color: #666;
    font-size: 1rem;
    margin: 0;
}

/* Fallback para testimonios sin carrusel */
.testimonials-grid-fallback {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
    margin-top: 40px;
}

.testimonials-grid-fallback .item {
    width: 100%;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title-modern {
        font-size: 3rem;
    }
    
    .about-title-modern {
        font-size: 2.5rem;
    }
    
    .section-title-modern {
        font-size: 2.5rem;
    }
    
    .stats-title-modern {
        font-size: 2.5rem;
    }
    
    .testimonials-title-modern {
        font-size: 2.5rem;
    }
}

@media (max-width: 992px) {
    .hero-stats-modern {
        gap: 30px;
    }
    
    .hero-actions-modern {
        justify-content: center;
    }
    
    .about-features-modern {
        margin-top: 30px;
    }
    
    .stats-grid-modern {
        margin-top: 40px;
    }
}

@media (max-width: 768px) {
    .hero-section-modern {
        padding: 80px 0 60px;
    }
    
    .hero-title-modern {
        font-size: 2.5rem;
    }
    
    .hero-stats-modern {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .hero-actions-modern {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-hero-modern {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .about-section-modern,
    .services-section-modern,
    .stats-section-modern,
    .testimonials-section-modern {
        padding: 80px 0;
    }
    
    .about-title-modern,
    .section-title-modern,
    .stats-title-modern,
    .testimonials-title-modern {
        font-size: 2rem;
    }
    
    .services-grid-modern {
        margin: 0 -15px;
    }
    
    .service-item-modern {
        padding: 0 15px;
        margin-bottom: 30px;
    }
    
    .service-image-modern {
        height: 220px;
    }
    
    .service-content-modern {
        padding: 25px;
    }
    
    .stats-grid-modern {
        margin: 0 -10px;
    }
    
    .stat-item-modern {
        padding: 0 10px;
        margin-bottom: 25px;
    }
    
    .stat-card-modern {
        padding: 25px 15px;
    }
    
    .stat-number-modern {
        font-size: 2rem;
    }
    
    .testimonial-card-modern {
        margin: 15px;
        padding: 30px;
    }
    
    .testimonial-text-modern {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .hero-section-modern {
        padding: 60px 0 40px;
    }
    
    .hero-title-modern {
        font-size: 2rem;
    }
    
    .hero-subtitle-modern {
        font-size: 1.1rem;
    }
    
    .stat-number-modern {
        font-size: 2rem;
    }
    
    .about-title-modern,
    .section-title-modern,
    .stats-title-modern,
    .testimonials-title-modern {
        font-size: 1.8rem;
    }
    
    .services-grid-modern {
        margin: 0 -10px;
    }
    
    .service-item-modern {
        padding: 0 10px;
        margin-bottom: 25px;
    }
    
    .service-image-modern {
        height: 200px;
    }
    
    .service-content-modern {
        padding: 20px;
    }
    
    .service-title-modern {
        font-size: 1.1rem;
    }
    
    .stats-grid-modern {
        margin: 0 -5px;
    }
    
    .stat-item-modern {
        padding: 0 5px;
        margin-bottom: 20px;
    }
    
    .stat-card-modern {
        padding: 20px 10px;
    }
    
    .stat-number-modern {
        font-size: 1.8rem;
    }
    
    .testimonial-card-modern {
        margin: 10px;
        padding: 25px;
    }
    
    .testimonial-text-modern {
        font-size: 1rem;
    }
}
.projects-grid {
    margin: 0 -20px;
}

.project-item {
    padding: 0 20px;
    margin-bottom: 40px;
}

.project-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.4s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.project-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    border-color: rgba(240, 132, 26, 0.2);
}

.project-image {
    position: relative;
    overflow: hidden;
    height: 280px;
}

.project-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.project-card:hover .project-image img {
    transform: scale(1.08);
}

.project-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(29, 40, 68, 0.85), rgba(240, 132, 26, 0.85));
    opacity: 0;
    transition: all 0.4s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 25px;
}

.project-card:hover .project-overlay {
    opacity: 1;
}

.project-badge {
    background: rgba(255, 255, 255, 0.95);
    color: #1d2844;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    align-self: flex-start;
    backdrop-filter: blur(15px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.project-actions {
    align-self: flex-end;
}

.btn-project {
    background: rgba(255, 255, 255, 0.95);
    color: #1d2844;
    padding: 15px 25px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(15px);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-project:hover {
    background: #ffffff;
    color: #f0841a;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.project-content {
    padding: 30px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.project-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: #1d2844;
    margin-bottom: 15px;
    line-height: 1.3;
}

.project-description {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
    flex-grow: 1;
}

.project-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

.meta-item i {
    color: #f0841a;
    font-size: 1rem;
}

.project-featured {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 12px 20px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    text-align: center;
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.project-featured i {
    font-size: 1rem;
}

.btn-large {
    padding: 18px 45px;
    font-size: 1.2rem;
    border-radius: 35px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-top: 20px;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
    transition: all 0.3s ease;
}

.btn-large:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
}

.no-projects {
    padding: 80px 20px;
    color: #666;
    text-align: center;
}

.no-projects i {
    color: #ddd;
    margin-bottom: 20px;
}

.no-projects h4 {
    color: #1d2844;
    font-weight: 700;
    margin-bottom: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .projects-grid {
        margin: 0 -15px;
    }
    
    .project-item {
        padding: 0 15px;
        margin-bottom: 30px;
    }
    
    .project-image {
        height: 220px;
    }
    
    .project-content {
        padding: 25px;
    }
    
    .project-title {
        font-size: 1.2rem;
    }
    
    .project-meta {
        gap: 15px;
    }
    
    .meta-item {
        font-size: 0.85rem;
    }
    
    .btn-large {
        padding: 15px 35px;
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .projects-grid {
        margin: 0 -10px;
    }
    
    .project-item {
        padding: 0 10px;
        margin-bottom: 25px;
    }
    
    .project-image {
        height: 200px;
    }
    
    .project-content {
        padding: 20px;
    }
    
    .project-title {
        font-size: 1.1rem;
    }
}
</style>

<script>
// Debug: Verificar testimonios
console.log('Testimonios recibidos:', <?= json_encode($testimonios_destacados ?? []) ?>);

// Inicializar carrusel de testimonios
window.addEventListener('load', function() {
    console.log('Página cargada, inicializando carrusel...');
    
    // Verificar si jQuery y Owl Carousel están disponibles
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.owlCarousel !== 'undefined') {
        console.log('jQuery y Owl Carousel disponibles, inicializando carrusel...');
        jQuery('#testimonial-carousel-modern').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                1200: {
                    items: 3
                }
            },
            navText: [
                '<i class="fas fa-chevron-left"></i>',
                '<i class="fas fa-chevron-right"></i>'
            ]
        });
        console.log('Carrusel inicializado correctamente');
    } else {
        // Si jQuery u Owl Carousel no están disponibles, mostrar testimonios en grid simple
        console.log('jQuery u Owl Carousel no están disponibles, mostrando testimonios en grid');
        var carousel = document.getElementById('testimonial-carousel-modern');
        if (carousel) {
            carousel.classList.add('testimonials-grid-fallback');
        }
    }
});
</script>

<?= $this->endSection() ?>

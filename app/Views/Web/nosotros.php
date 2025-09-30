<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-nosotros-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-nosotros"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-nosotros text-white">
                    <div class="hero-badge-nosotros">
                        <i class="fas fa-users"></i>
                        <span>NOSOTROS</span>
                    </div>
                    <h1 class="hero-title-nosotros mb-4">Conoce MANSANCHEZ</h1>
                    <p class="hero-subtitle-nosotros lead mb-4">
                        Más de <?= $estadisticas['anos_experiencia'] ?> años construyendo sueños y transformando espacios. 
                        Nuestra pasión por la excelencia nos ha convertido en líderes de la construcción.
                    </p>
                    <div class="hero-stats-nosotros mb-4">
                        <div class="stat-item-nosotros">
                            <div class="stat-number-nosotros"><?= $estadisticas['anos_experiencia'] ?>+</div>
                            <div class="stat-label-nosotros">Años de Experiencia</div>
                        </div>
                        <div class="stat-item-nosotros">
                            <div class="stat-number-nosotros"><?= $estadisticas['total'] ?>+</div>
                            <div class="stat-label-nosotros">Proyectos Completados</div>
                        </div>
                        <div class="stat-item-nosotros">
                            <div class="stat-number-nosotros">100%</div>
                            <div class="stat-label-nosotros">Compromiso con la Calidad</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Historia y Misión -->
<section class="historia-mision-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="historia-content">
                    <div class="section-badge">
                        <i class="fas fa-history"></i>
                        <span>NUESTRA HISTORIA</span>
                    </div>
                    <h2 class="section-title-nosotros mb-4">Construyendo el Futuro desde <?= date('Y') - $estadisticas['anos_experiencia'] ?></h2>
                    <p class="section-text-nosotros mb-4">
                        <?= !empty($empresa['descripcion']) ? esc($empresa['descripcion']) : 'MANSANCHEZ Constructor nació con la visión de transformar la industria de la construcción a través de la innovación, la calidad y el compromiso con nuestros clientes. Desde nuestros inicios, hemos mantenido los más altos estándares de excelencia en cada proyecto que emprendemos.' ?>
                    </p>
                    <p class="section-text-nosotros">
                        Nuestro equipo de profesionales altamente capacitados trabaja con pasión y dedicación para entregar resultados que superen las expectativas de nuestros clientes, utilizando las mejores técnicas y materiales del mercado.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="historia-image text-center">
                    <div class="image-container-nosotros">
                        <i class="fas fa-building" style="font-size: 8rem; color: rgba(29, 40, 68, 0.1);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Valores -->
<section class="valores-modern" style="background: #f8f9fa; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <div class="section-badge">
                    <i class="fas fa-heart"></i>
                    <span>NUESTROS VALORES</span>
                </div>
                <h2 class="section-title-nosotros mb-3">Lo que nos Define</h2>
                <p class="section-subtitle-nosotros">Los principios que guían cada uno de nuestros proyectos</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="valor-card-modern">
                    <div class="valor-icon-modern">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4 class="valor-title-modern">Excelencia</h4>
                    <p class="valor-text-modern">Nos comprometemos a entregar la más alta calidad en cada proyecto, utilizando los mejores materiales y técnicas.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="valor-card-modern">
                    <div class="valor-icon-modern">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4 class="valor-title-modern">Confianza</h4>
                    <p class="valor-text-modern">Construimos relaciones sólidas basadas en la transparencia, honestidad y cumplimiento de compromisos.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="valor-card-modern">
                    <div class="valor-icon-modern">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="valor-title-modern">Innovación</h4>
                    <p class="valor-text-modern">Incorporamos las últimas tecnologías y métodos constructivos para optimizar resultados.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="valor-card-modern">
                    <div class="valor-icon-modern">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="valor-title-modern">Equipo</h4>
                    <p class="valor-text-modern">Valoramos el trabajo en equipo y el desarrollo profesional de cada miembro de nuestra organización.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonios -->
<?php if (!empty($testimonios)): ?>
<section class="testimonios-nosotros-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 80px 0; position: relative; overflow: hidden;">
    <div class="testimonios-pattern-nosotros"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center mb-5">
                <div class="section-badge-white">
                    <i class="fas fa-quote-left"></i>
                    <span>TESTIMONIOS</span>
                </div>
                <h2 class="section-title-white mb-3">Lo que Dicen Nuestros Clientes</h2>
                <p class="section-subtitle-white">La satisfacción de nuestros clientes es nuestra mayor recompensa</p>
            </div>
        </div>
        <div class="row">
            <?php foreach (array_slice($testimonios, 0, 3) as $testimonio): ?>
            <div class="col-lg-4 mb-4">
                <div class="testimonio-card-nosotros">
                    <div class="testimonio-content-nosotros">
                        <div class="testimonio-quote-nosotros">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <p class="testimonio-text-nosotros"><?= esc($testimonio->testimonio) ?></p>
                        <div class="testimonio-rating-nosotros">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?= $i <= $testimonio->calificacion ? 'text-warning' : 'text-muted' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="testimonio-author-nosotros">
                        <h5 class="author-name-nosotros"><?= esc($testimonio->nombre) ?></h5>
                        <p class="author-details-nosotros">
                            <?= !empty($testimonio->cargo) ? esc($testimonio->cargo) : '' ?>
                            <?= !empty($testimonio->empresa) ? ' - ' . esc($testimonio->empresa) : '' ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contacto -->
<section class="contacto-nosotros-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <div class="contacto-content-nosotros">
                    <div class="section-badge">
                        <i class="fas fa-envelope"></i>
                        <span>CONTÁCTANOS</span>
                    </div>
                    <h2 class="section-title-nosotros mb-4">¿Listo para tu Próximo Proyecto?</h2>
                    <p class="section-text-nosotros mb-5">
                        Estamos aquí para hacer realidad tu proyecto de construcción. 
                        Contáctanos y descubramos juntos las infinitas posibilidades.
                    </p>
                    <div class="contacto-actions-nosotros">
                        <a href="<?= base_url('contacto') ?>" class="btn-contacto-nosotros btn-primary-nosotros">
                            <i class="fas fa-calculator"></i> Solicitar Cotización
                        </a>
                        <a href="<?= base_url('proyectos') ?>" class="btn-contacto-nosotros btn-secondary-nosotros">
                            <i class="fas fa-eye"></i> Ver Nuestros Proyectos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== NOSOTROS MODERNOS CSS ===== */

/* Hero Section Nosotros */
.hero-nosotros-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-nosotros {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-nosotros" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-nosotros)"/></svg>');
    opacity: 0.3;
}

.hero-badge-nosotros {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.hero-title-nosotros {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-nosotros {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
    line-height: 1.6;
}

.hero-stats-nosotros {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
}

.stat-item-nosotros {
    text-align: center;
}

.stat-number-nosotros {
    font-size: 2.5rem;
    font-weight: 800;
    color: #f0841a;
    margin-bottom: 5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label-nosotros {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Secciones Generales */
.section-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(240, 132, 26, 0.1);
    color: #f0841a;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.section-badge-white {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.section-title-nosotros {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
}

.section-title-white {
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.section-subtitle-nosotros {
    font-size: 1.1rem;
    color: #666;
    font-weight: 400;
}

.section-subtitle-white {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
}

.section-text-nosotros {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.7;
    margin-bottom: 20px;
}

/* Historia */
.image-container-nosotros {
    padding: 40px;
    background: rgba(29, 40, 68, 0.05);
    border-radius: 20px;
    border: 2px dashed rgba(29, 40, 68, 0.1);
}

/* Valores */
.valor-card-modern {
    background: white;
    border-radius: 20px;
    padding: 40px 30px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.valor-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.valor-icon-modern {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    color: white;
    font-size: 2rem;
}

.valor-title-modern {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
}

.valor-text-modern {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0;
}

/* Testimonios */
.testimonios-nosotros-modern {
    position: relative;
    overflow: hidden;
}

.testimonios-pattern-nosotros {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="testimonios-pattern" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23testimonios-pattern)"/></svg>');
    opacity: 0.2;
}

.testimonio-card-nosotros {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
    backdrop-filter: blur(10px);
}

.testimonio-card-nosotros:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.testimonio-quote-nosotros {
    font-size: 2rem;
    color: #f0841a;
    margin-bottom: 20px;
}

.testimonio-text-nosotros {
    color: #333;
    font-size: 1rem;
    line-height: 1.6;
    font-style: italic;
    margin-bottom: 20px;
}

.testimonio-rating-nosotros {
    margin-bottom: 20px;
}

.testimonio-rating-nosotros i {
    font-size: 1.1rem;
    margin: 0 2px;
}

.author-name-nosotros {
    color: #1d2844;
    font-weight: 700;
    margin-bottom: 5px;
}

.author-details-nosotros {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

/* Contacto */
.contacto-actions-nosotros {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.btn-contacto-nosotros {
    padding: 18px 35px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.btn-primary-nosotros {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
}

.btn-primary-nosotros:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white;
    text-decoration: none;
}

.btn-secondary-nosotros {
    background: transparent;
    color: #1d2844;
    border: 2px solid #1d2844;
}

.btn-secondary-nosotros:hover {
    background: #1d2844;
    color: white;
    transform: translateY(-3px);
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title-nosotros {
        font-size: 3rem;
    }
    
    .hero-stats-nosotros {
        gap: 30px;
    }
    
    .stat-number-nosotros {
        font-size: 2.2rem;
    }
}

@media (max-width: 992px) {
    .hero-stats-nosotros {
        flex-direction: column;
        gap: 20px;
    }
    
    .valor-card-modern {
        padding: 30px 20px;
    }
    
    .contacto-actions-nosotros {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-contacto-nosotros {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .hero-title-nosotros {
        font-size: 2.5rem;
    }
    
    .hero-subtitle-nosotros {
        font-size: 1.1rem;
    }
    
    .section-title-nosotros,
    .section-title-white {
        font-size: 2rem;
    }
    
    .valor-card-modern {
        padding: 25px 15px;
    }
    
    .valor-icon-modern {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .testimonio-card-nosotros {
        padding: 25px 20px;
    }
}

@media (max-width: 480px) {
    .hero-title-nosotros {
        font-size: 2rem;
    }
    
    .valor-card-modern {
        padding: 20px 15px;
    }
    
    .valor-title-modern {
        font-size: 1.2rem;
    }
    
    .valor-text-modern {
        font-size: 0.9rem;
    }
}
</style>

<?= $this->endSection() ?>

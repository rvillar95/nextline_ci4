<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(to bottom, #1d2844 30%, #4a5f7a 100%) !important; padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <h1 class="hero-title mb-4">Nuestros Servicios</h1>
                    <p class="hero-subtitle lead mb-4">Soluciones profesionales de construcción para hacer realidad tus proyectos más ambiciosos</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="stat-number">15+</div>
                            <div class="stat-label">Años de Experiencia</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">200+</div>
                            <div class="stat-label">Proyectos Completados</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Satisfacción Garantizada</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <i class="fas fa-tools" style="font-size: 8rem; color: rgba(240, 132, 26, 0.3);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <!-- Filter Section -->
        <div class="filter-section text-center mb-5">
            <h2 class="section-title">Explora por Categoría</h2>
            <p class="section-subtitle">Encuentra exactamente lo que necesitas para tu proyecto</p>
            <div class="title-divider mx-auto mb-4"></div>
            
            <div class="filter-buttons">
                <a href="<?= base_url('servicios') ?>" class="btn-filter-modern <?= empty($categoria_actual) ? 'active' : '' ?>">
                    <i class="fas fa-th-large"></i> 
                    <span>Todos los Servicios</span>
                </a>
                <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                        <a href="<?= base_url('servicios?categoria=' . urlencode($cat->nombre)) ?>" 
                           class="btn-filter-modern <?= $categoria_actual === $cat->nombre ? 'active' : '' ?>">
                            <?php if (!empty($cat->icono)): ?>
                                <i class="<?= esc($cat->icono) ?>"></i>
                            <?php else: ?>
                                <i class="fas fa-cog"></i>
                            <?php endif; ?>
                            <span><?= esc($cat->nombre) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="services-grid">
            <?php if (!empty($servicios)): ?>
                <?php foreach ($servicios as $servicio): ?>
                    <div class="service-card-modern">
                        <div class="service-image-modern">
                            <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-fluid">
                            <div class="service-overlay-modern">
                                <div class="service-badge">
                                    <?php if (!empty($servicio->categoria_icono)): ?>
                                        <i class="<?= esc($servicio->categoria_icono) ?>"></i>
                                    <?php else: ?>
                                        <i class="fas fa-cog"></i>
                                    <?php endif; ?>
                                    <span><?= esc($servicio->categoria_nombre ?? 'General') ?></span>
                                </div>
                                <div class="service-actions-overlay">
                                    <a href="<?= base_url('servicios/' . ($servicio->slug ?? 'servicio-' . $servicio->id)) ?>" class="btn-overlay">
                                        <i class="fas fa-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="service-content-modern">
                            <div class="service-header">
                                <h3 class="service-title"><?= esc($servicio->nombre) ?></h3>
                                <div class="service-category-modern">
                                    <i class="fas fa-tag"></i>
                                    <?= esc($servicio->categoria_nombre ?? 'General') ?>
                                </div>
                            </div>
                            
                            <p class="service-description"><?= esc($servicio->descripcionCorta) ?></p>
                            
                            <?php if (!empty($servicio->caracteristicas)): ?>
                                <div class="service-features-modern">
                                    <h5><i class="fas fa-star"></i> Características Destacadas</h5>
                                    <ul>
                                        <?php 
                                        $caracteristicas = explode("\n", $servicio->caracteristicas);
                                        foreach (array_slice($caracteristicas, 0, 3) as $caracteristica): 
                                            if (trim($caracteristica)):
                                        ?>
                                            <li><i class="fas fa-check"></i> <?= esc(trim($caracteristica)) ?></li>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="service-price-modern">
                                <?php if ($servicio->mostrar_precio === 'S' && ($servicio->precio_desde || $servicio->precio_hasta)): ?>
                                    <div class="price-container">
                                        <i class="fas fa-dollar-sign"></i>
                                        <?php if ($servicio->precio_desde && $servicio->precio_hasta): ?>
                                            <span class="price-range">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?> - $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                        <?php elseif ($servicio->precio_desde): ?>
                                            <span class="price-range">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></span>
                                        <?php elseif ($servicio->precio_hasta): ?>
                                            <span class="price-range">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="price-consult-modern">
                                        <i class="fas fa-comments"></i>
                                        <span>Consultar Precio</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="service-actions-modern">
                                <a href="<?= base_url('servicios/' . ($servicio->slug ?? 'servicio-' . $servicio->id)) ?>" class="btn-service-modern">
                                    <i class="fas fa-info-circle"></i> Ver Detalles
                                </a>
                                <a href="<?= base_url('contacto?servicio=' . $servicio->id) ?>" class="btn-quote-modern">
                                    <i class="fas fa-calculator"></i> Cotizar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-services-modern">
                    <div class="no-services-content">
                        <i class="fas fa-tools fa-4x mb-4"></i>
                        <h3>Próximamente</h3>
                        <p>Estamos preparando nuestros servicios para mostrarte. Muy pronto podrás ver toda nuestra oferta profesional.</p>
                        <a href="<?= base_url('contacto') ?>" class="btn-contact-modern">
                            <i class="fas fa-envelope"></i> Contáctanos
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Call to Action -->
        <div class="cta-section-modern">
            <div class="cta-content">
                <div class="cta-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3>¿No encuentras lo que buscas?</h3>
                <p>Contáctanos y te ayudaremos a encontrar la solución perfecta para tu proyecto. Cada proyecto es único y merece una atención personalizada.</p>
                <div class="cta-actions">
                    <a href="<?= base_url('contacto') ?>" class="btn-cta-primary">
                        <i class="fas fa-envelope"></i> Solicitar Consulta
                    </a>
                    <a href="<?= base_url('proyectos') ?>" class="btn-cta-secondary">
                        <i class="fas fa-images"></i> Ver Nuestros Proyectos
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Estilos modernos para página de servicios */

.hero-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%) !important;
}

.hero-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    line-height: 1.6;
}

.hero-stats {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #f0841a;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 0.5rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 2rem;
}

.title-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 2px;
}

.filter-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-bottom: 3rem;
}

.btn-filter-modern {
    background: #ffffff;
    border: 2px solid #e9ecef;
    color: #6c757d;
    padding: 15px 25px;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
}

.btn-filter-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    transition: left 0.3s ease;
    z-index: 0;
}

.btn-filter-modern:hover::before {
    left: 0;
}

.btn-filter-modern:hover {
    color: white !important;
    border-color: #f0841a;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
    text-decoration: none;
}

.btn-filter-modern.active {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    color: white !important;
    border-color: #1d2844;
    box-shadow: 0 8px 25px rgba(29, 40, 68, 0.3);
}

.btn-filter-modern i,
.btn-filter-modern span {
    position: relative;
    z-index: 1;
}

.btn-filter-modern i {
    margin-right: 8px;
    font-size: 1.1em;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin-bottom: 4rem;
}

.service-card-modern {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.4s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
}

.service-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    border-color: rgba(240, 132, 26, 0.2);
}

.service-image-modern {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.service-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.service-card-modern:hover .service-image-modern img {
    transform: scale(1.1);
}

.service-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(29, 40, 68, 0.9), rgba(240, 132, 26, 0.8));
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
    opacity: 0;
    transition: opacity 0.4s ease;
}

.service-card-modern:hover .service-overlay-modern {
    opacity: 1;
}

.service-badge {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border-radius: 25px;
    padding: 8px 15px;
    color: white;
    font-size: 0.9rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
}

.service-badge i {
    margin-right: 8px;
}

.service-actions-overlay {
    align-self: flex-end;
}

.btn-overlay {
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white !important;
    padding: 12px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-overlay:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white !important;
    text-decoration: none;
    transform: translateY(-2px);
}

.service-content-modern {
    padding: 30px;
}

.service-header {
    margin-bottom: 20px;
}

.service-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 10px;
    line-height: 1.3;
}

.service-category-modern {
    color: #f0841a;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}

.service-category-modern i {
    margin-right: 6px;
    font-size: 0.8rem;
}

.service-description {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 20px;
    font-size: 1rem;
}

.service-features-modern {
    margin-bottom: 20px;
}

.service-features-modern h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #1d2844;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
}

.service-features-modern h5 i {
    margin-right: 8px;
    color: #f0841a;
}

.service-features-modern ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-features-modern li {
    color: #6c757d;
    padding: 6px 0;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
}

.service-features-modern li i {
    color: #28a745;
    margin-right: 10px;
    font-size: 0.8rem;
}

.service-price-modern {
    margin-bottom: 25px;
    text-align: center;
}

.price-container {
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    border-radius: 15px;
    padding: 15px;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.price-container i {
    color: #f0841a;
    font-size: 1.2rem;
}

.price-range {
    color: #1d2844;
    font-weight: 700;
    font-size: 1.1rem;
}

.price-consult-modern {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    border-radius: 15px;
    padding: 15px;
    border: 2px solid #ffc107;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.price-consult-modern i {
    color: #856404;
    font-size: 1.2rem;
}

.price-consult-modern span {
    color: #856404;
    font-weight: 600;
    font-size: 1.1rem;
}

.service-actions-modern {
    display: flex;
    gap: 12px;
}

.btn-service-modern,
.btn-quote-modern {
    flex: 1;
    padding: 12px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-service-modern {
    background: #ffffff;
    color: #f0841a;
    border: 2px solid #f0841a;
}

.btn-service-modern:hover {
    background: #f0841a;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 132, 26, 0.3);
    text-decoration: none;
}

.btn-quote-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    color: white;
    border: 2px solid #1d2844;
}

.btn-quote-modern:hover {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-color: #f0841a;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(29, 40, 68, 0.3);
    text-decoration: none;
}

.no-services-modern {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
}

.no-services-content {
    background: #ffffff;
    border-radius: 20px;
    padding: 60px 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.no-services-content i {
    color: #f0841a;
    margin-bottom: 20px;
}

.no-services-content h3 {
    color: #1d2844;
    font-weight: 700;
    margin-bottom: 15px;
}

.no-services-content p {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.btn-contact-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
}

.btn-contact-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white;
    text-decoration: none;
}

.cta-section-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    border-radius: 25px;
    padding: 60px 40px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.cta-section-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.1;
}

.cta-content {
    position: relative;
    z-index: 1;
}

.cta-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    color: white;
    font-size: 2rem;
}

.cta-section-modern h3 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.cta-section-modern p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin-bottom: 40px;
    line-height: 1.6;
}

.cta-actions {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-cta-primary,
.btn-cta-secondary {
    padding: 18px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.btn-cta-primary {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
}

.btn-cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white;
    text-decoration: none;
}

.btn-cta-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-cta-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    text-decoration: none;
    transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 1rem;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .service-actions-modern {
        flex-direction: column;
    }
    
    .cta-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-cta-primary,
    .btn-cta-secondary {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?= $this->endSection() ?>

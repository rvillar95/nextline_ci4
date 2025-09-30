<?php $this->extend('layout/web') ?>

<?= $this->section("content") ?>

<!-- Hero Section -->
<section class="hero-service-detail-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-service-detail"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-service-detail text-white">
                    <div class="hero-badge-service-detail">
                        <i class="<?= $servicio->categoria_icono ?? $servicio->icono ?? 'fas fa-cog' ?>"></i>
                        <span><?= esc($servicio->categoria_nombre ?? $servicio->categoria ?? 'SERVICIO') ?></span>
                    </div>
                    <h1 class="hero-title-service-detail mb-4"><?= esc($servicio->nombre) ?></h1>
                    <p class="hero-subtitle-service-detail lead mb-4">
                        <?= esc($servicio->descripcionCorta) ?>
                    </p>
                    <div class="hero-breadcrumb-service-detail">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb-modern">
                                <li class="breadcrumb-item-modern">
                                    <a href="<?= base_url() ?>">
                                        <i class="fas fa-home"></i> Inicio
                                    </a>
                                </li>
                                <li class="breadcrumb-item-modern">
                                    <a href="<?= base_url('servicios') ?>">Servicios</a>
                                </li>
                                <li class="breadcrumb-item-modern active"><?= esc($servicio->nombre) ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Detail Section -->
<section class="service-detail-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-detail-content-modern">
                    <!-- Service Image -->
                    <div class="service-image-modern">
                        <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-fluid">
                        <div class="service-image-overlay-modern">
                            <div class="service-badge-modern">
                                <i class="<?= $servicio->categoria_icono ?? $servicio->icono ?? 'fas fa-cog' ?>"></i>
                                <span><?= esc($servicio->categoria_nombre ?? $servicio->categoria ?? 'General') ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Service Info -->
                    <div class="service-info-modern">
                        <div class="service-description-modern">
                            <h2 class="section-title-service-detail">Descripción del Servicio</h2>
                            <p class="section-text-service-detail"><?= esc($servicio->descripcionCorta) ?></p>
                            
                            <?php if (!empty($servicio->descripcionLarga)): ?>
                                <p class="section-text-service-detail"><?= esc($servicio->descripcionLarga) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($servicio->caracteristicas)): ?>
                            <div class="service-features-modern">
                                <h3 class="feature-title-modern">
                                    <i class="fas fa-check-circle"></i> Características Principales
                                </h3>
                                <div class="features-grid-modern">
                                    <?php 
                                    $caracteristicas = explode("\n", $servicio->caracteristicas);
                                    foreach ($caracteristicas as $caracteristica): 
                                        if (trim($caracteristica)):
                                    ?>
                                        <div class="feature-item-modern">
                                            <i class="fas fa-check"></i>
                                            <span><?= esc(trim($caracteristica)) ?></span>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($servicio->beneficios)): ?>
                            <div class="service-benefits-modern">
                                <h3 class="benefit-title-modern">
                                    <i class="fas fa-star"></i> Beneficios del Servicio
                                </h3>
                                <div class="benefits-grid-modern">
                                    <?php 
                                    $beneficios = explode("\n", $servicio->beneficios);
                                    foreach ($beneficios as $beneficio): 
                                        if (trim($beneficio)):
                                    ?>
                                        <div class="benefit-item-modern">
                                            <i class="fas fa-star"></i>
                                            <span><?= esc(trim($beneficio)) ?></span>
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($servicio->tiempo_estimado) || !empty($servicio->garantia)): ?>
                            <div class="service-details-modern">
                                <h3 class="details-title-modern">
                                    <i class="fas fa-info-circle"></i> Información Adicional
                                </h3>
                                <div class="details-grid-modern">
                                    <?php if (!empty($servicio->tiempo_estimado)): ?>
                                        <div class="detail-item-modern">
                                            <div class="detail-icon-modern">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="detail-content-modern">
                                                <h4>Tiempo Estimado</h4>
                                                <p><?= esc($servicio->tiempo_estimado) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($servicio->garantia)): ?>
                                        <div class="detail-item-modern">
                                            <div class="detail-icon-modern">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <div class="detail-content-modern">
                                                <h4>Garantía</h4>
                                                <p><?= esc($servicio->garantia) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="service-sidebar-modern">
                    <!-- Price Section -->
                    <?php if ($servicio->mostrar_precio === 'S' && ($servicio->precio_desde || $servicio->precio_hasta)): ?>
                        <div class="price-box-modern">
                            <div class="price-header-modern">
                                <i class="fas fa-dollar-sign"></i>
                                <h3>Precio del Servicio</h3>
                            </div>
                            <div class="price-content-modern">
                                <?php if ($servicio->precio_desde && $servicio->precio_hasta): ?>
                                    <div class="price-range-modern">
                                        <div class="price-from-modern">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></div>
                                        <div class="price-to-modern">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></div>
                                    </div>
                                <?php elseif ($servicio->precio_desde): ?>
                                    <div class="price-range-modern">
                                        <div class="price-from-modern">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></div>
                                    </div>
                                <?php elseif ($servicio->precio_hasta): ?>
                                    <div class="price-range-modern">
                                        <div class="price-to-modern">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></div>
                                    </div>
                                <?php endif; ?>
                                <p class="price-note-modern">* Precios sujetos a evaluación del proyecto</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Contact CTA -->
                    <div class="contact-cta-modern">
                        <div class="cta-header-modern">
                            <i class="fas fa-phone"></i>
                            <h3>¿Interesado en este servicio?</h3>
                        </div>
                        <p class="cta-text-modern">Contáctanos para obtener más información y una cotización personalizada.</p>
                        <div class="cta-actions-modern">
                            <a href="<?= base_url('contacto?servicio=' . $servicio->id) ?>" class="btn-cta-primary-modern">
                                <i class="fas fa-calculator"></i> Solicitar Cotización
                            </a>
                            <a href="<?= base_url('contacto') ?>" class="btn-cta-secondary-modern">
                                <i class="fas fa-envelope"></i> Contactar
                            </a>
                        </div>
                    </div>
                    
                    <!-- Related Services -->
                    <?php if (!empty($servicios_relacionados)): ?>
                        <div class="related-services-modern">
                            <div class="related-header-modern">
                                <i class="fas fa-link"></i>
                                <h3>Servicios Relacionados</h3>
                            </div>
                            <div class="related-content-modern">
                                <?php foreach ($servicios_relacionados as $servicio_rel): ?>
                                    <div class="related-service-item-modern">
                                        <div class="related-service-image-modern">
                                            <img src="<?= base_url($servicio_rel->foto) ?>" alt="<?= esc($servicio_rel->nombre) ?>" class="img-fluid">
                                        </div>
                                        <div class="related-service-content-modern">
                                            <h4><a href="<?= base_url('servicios/' . ($servicio_rel->slug ?? 'servicio-' . $servicio_rel->id)) ?>"><?= esc($servicio_rel->nombre) ?></a></h4>
                                            <p><?= esc($servicio_rel->descripcionCorta) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== SERVICIO DETALLE MODERNOS CSS ===== */

/* Hero Section Service Detail */
.hero-service-detail-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-service-detail {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-service-detail" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-service-detail)"/></svg>');
    opacity: 0.3;
}

.hero-badge-service-detail {
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

.hero-title-service-detail {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-service-detail {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
    line-height: 1.6;
}

.hero-breadcrumb-service-detail {
    margin-top: 30px;
}

.breadcrumb-modern {
    background: none;
    padding: 0;
    margin: 0;
    list-style: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    flex-wrap: wrap;
}

.breadcrumb-item-modern {
    display: flex;
    align-items: center;
}

.breadcrumb-item-modern:not(:last-child):after {
    content: ">";
    margin: 0 15px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: bold;
}

.breadcrumb-item-modern a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.breadcrumb-item-modern a:hover {
    color: #f0841a;
}

.breadcrumb-item-modern.active {
    color: white;
    font-weight: 500;
}

/* Service Detail Content */
.service-detail-content-modern {
    margin-bottom: 40px;
}

.service-image-modern {
    position: relative;
    margin-bottom: 40px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.service-image-modern img {
    width: 100%;
    height: 450px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.service-image-modern:hover img {
    transform: scale(1.05);
}

.service-image-overlay-modern {
    position: absolute;
    top: 20px;
    right: 20px;
}

.service-badge-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(29, 40, 68, 0.9);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.service-info-modern {
    margin-top: 40px;
}

.section-title-service-detail {
    font-size: 2.2rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
}

.section-text-service-detail {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.7;
    margin-bottom: 20px;
}

/* Features */
.feature-title-modern,
.benefit-title-modern,
.details-title-modern {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1d2844;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.features-grid-modern,
.benefits-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-bottom: 40px;
}

.feature-item-modern,
.benefit-item-modern {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border-left: 4px solid #f0841a;
    transition: all 0.3s ease;
}

.feature-item-modern:hover,
.benefit-item-modern:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.feature-item-modern i,
.benefit-item-modern i {
    color: #f0841a;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.feature-item-modern span,
.benefit-item-modern span {
    color: #333;
    font-weight: 500;
}

/* Details */
.details-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.detail-item-modern {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.detail-item-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.detail-icon-modern {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.detail-content-modern h4 {
    color: #1d2844;
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 1rem;
}

.detail-content-modern p {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
}

/* Sidebar */
.service-sidebar-modern {
    position: sticky;
    top: 100px;
}

.price-box-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    color: white;
    border-radius: 20px;
    margin-bottom: 30px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(29, 40, 68, 0.3);
}

.price-header-modern {
    background: rgba(240, 132, 26, 0.2);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.price-header-modern i {
    color: #f0841a;
    font-size: 1.2rem;
}

.price-header-modern h3 {
    color: white;
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.price-content-modern {
    padding: 25px;
    text-align: center;
}

.price-range-modern {
    margin-bottom: 20px;
}

.price-from-modern,
.price-to-modern {
    display: block;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.price-note-modern {
    font-size: 0.85rem;
    opacity: 0.8;
    margin: 0;
    font-style: italic;
}

.contact-cta-modern {
    background: white;
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.cta-header-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
}

.cta-header-modern i {
    color: #f0841a;
    font-size: 1.2rem;
}

.cta-header-modern h3 {
    color: #1d2844;
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.cta-text-modern {
    color: #666;
    margin-bottom: 25px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.cta-actions-modern {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn-cta-primary-modern,
.btn-cta-secondary-modern {
    padding: 15px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-cta-primary-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 5px 15px rgba(240, 132, 26, 0.3);
}

.btn-cta-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
}

.btn-cta-secondary-modern {
    background: transparent;
    color: #1d2844;
    border: 2px solid #1d2844;
}

.btn-cta-secondary-modern:hover {
    background: #1d2844;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

/* Related Services */
.related-services-modern {
    background: white;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.related-header-modern {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.related-header-modern i {
    color: #f0841a;
    font-size: 1.1rem;
}

.related-header-modern h3 {
    color: #1d2844;
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.related-service-item-modern {
    display: flex;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
    transition: all 0.3s ease;
}

.related-service-item-modern:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.related-service-item-modern:hover {
    transform: translateX(5px);
}

.related-service-image-modern {
    width: 70px;
    height: 70px;
    border-radius: 12px;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.related-service-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-service-content-modern h4 {
    font-size: 1rem;
    margin-bottom: 8px;
    font-weight: 600;
}

.related-service-content-modern h4 a {
    color: #1d2844;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-service-content-modern h4 a:hover {
    color: #f0841a;
}

.related-service-content-modern p {
    font-size: 0.85rem;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title-service-detail {
        font-size: 3rem;
    }
}

@media (max-width: 992px) {
    .service-sidebar-modern {
        position: static;
        margin-top: 40px;
    }
    
    .features-grid-modern,
    .benefits-grid-modern {
        grid-template-columns: 1fr;
    }
    
    .details-grid-modern {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .hero-title-service-detail {
        font-size: 2.5rem;
    }
    
    .hero-subtitle-service-detail {
        font-size: 1.1rem;
    }
    
    .service-image-modern img {
        height: 300px;
    }
    
    .section-title-service-detail {
        font-size: 1.8rem;
    }
    
    .breadcrumb-modern {
        font-size: 0.8rem;
    }
    
    .breadcrumb-item-modern:not(:last-child):after {
        margin: 0 10px;
    }
}

@media (max-width: 480px) {
    .hero-title-service-detail {
        font-size: 2rem;
    }
    
    .service-image-modern img {
        height: 250px;
    }
    
    .feature-item-modern,
    .benefit-item-modern {
        padding: 12px 15px;
    }
    
    .detail-item-modern {
        padding: 15px;
    }
    
    .detail-icon-modern {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
}
</style>

<?= $this->endSection() ?>

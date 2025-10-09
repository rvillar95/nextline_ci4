<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-galeria-detalle-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 60px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-galeria-detalle"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-galeria-detalle text-white">
                    <div class="hero-badge-galeria-detalle">
                        <i class="fas fa-image"></i>
                        <span>GALERÍA</span>
                    </div>
                    <h1 class="hero-title-galeria-detalle mb-4"><?= esc($galeria->nombre) ?></h1>
                    <?php if (!empty($galeria->categoria_nombre)): ?>
                        <div class="hero-category-galeria-detalle">
                            <i class="fas fa-folder"></i>
                            <span><?= esc($galeria->categoria_nombre) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Imagen Principal -->
<section class="main-image-modern" style="background: white; padding: 60px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="main-image-container-modern">
                    <?php if (!empty($galeria->portada) && $galeria->portada): ?>
                        <img src="<?= base_url($galeria->portada) ?>" 
                             alt="<?= esc($galeria->nombre) ?>" 
                             class="img-fluid main-image-modern-img">
                    <?php else: ?>
                        <div class="no-image-main-modern">
                            <i class="fas fa-image"></i>
                            <span>Imagen no disponible</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="image-actions-modern">
                        <?php if (!empty($galeria->portada) && $galeria->portada): ?>
                            <a href="#" 
                               class="btn-image-action-modern btn-zoom-main-modern" 
                               onclick="openImageModal('<?= base_url($galeria->portada) ?>', '<?= esc($galeria->nombre) ?>')"
                               title="Ampliar Imagen">
                                <i class="fas fa-search-plus" style="color: white;"></i>
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('galeria') ?>" 
                           class="btn-image-action-modern btn-back-modern" 
                           title="Volver a Galería">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Información de la Imagen -->
<?php if (!empty($galeria->descripcion) || !empty($galeria->categoria_nombre)): ?>
<section class="gallery-info-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="gallery-info-card">
                    <!-- Header -->
                    <div class="gallery-info-header">
                        <div class="info-header-content">
                            <div class="info-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="info-title-content">
                                <h2 class="info-title text-white">Información de la Imagen</h2>
                                <p class="info-subtitle">Detalles y características de esta obra</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="gallery-info-content">
                        <div class="info-grid">
                            <!-- Descripción -->
                            <?php if (!empty($galeria->descripcion)): ?>
                            <div class="info-item description-item">
                                <div class="item-header">
                                    <div class="item-icon">
                                        <i class="fas fa-align-left"></i>
                                    </div>
                                    <h3 class="item-title">Descripción</h3>
                                </div>
                                <div class="item-content">
                                    <p class="item-text">
                                        <?= nl2br(esc($galeria->descripcion)) ?>
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Categoría -->
                            <?php if (!empty($galeria->categoria_nombre)): ?>
                            <div class="info-item category-item">
                                <div class="item-header">
                                    <div class="item-icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <h3 class="item-title">Categoría</h3>
                                </div>
                                <div class="item-content">
                                    <div class="category-badge">
                                        <span class="category-text"><?= esc($galeria->categoria_nombre) ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Galerías Relacionadas -->
<?php if (!empty($galerias_relacionadas)): ?>
<section class="related-gallery-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="related-header-modern text-center mb-5">
                    <h2 class="related-title-modern">
                        <i class="fas fa-images"></i>
                        Imágenes Relacionadas
                    </h2>
                    <p class="related-subtitle-modern">
                        Descubre más imágenes de la misma categoría
                    </p>
                </div>
                
                <div class="row">
                    <?php foreach ($galerias_relacionadas as $galeria_relacionada): ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="related-card-modern">
                                <div class="related-image-modern">
                                    <?php if (!empty($galeria_relacionada->portada) && $galeria_relacionada->portada): ?>
                                        <img src="<?= base_url($galeria_relacionada->portada) ?>" 
                                             alt="<?= esc($galeria_relacionada->nombre) ?>" 
                                             class="img-fluid">
                                    <?php else: ?>
                                        <div class="no-image-related-modern">
                                            <i class="fas fa-image" style="color: white;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="related-overlay-modern">
                                        <a href="<?= base_url('galeria/detalle/' . $galeria_relacionada->id) ?>" 
                                           class="btn-related-modern">
                                            <i class="fas fa-eye" style="color: white;"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="related-content-modern">
                                    <h3 class="related-card-title-modern">
                                        <a href="<?= base_url('galeria/detalle/' . $galeria_relacionada->id) ?>">
                                            <?= esc($galeria_relacionada->nombre) ?>
                                        </a>
                                    </h3>
                                    
                                    <?php if (!empty($galeria_relacionada->descripcion)): ?>
                                        <p class="related-description-modern">
                                            <?= esc(substr($galeria_relacionada->descripcion, 0, 100)) ?>
                                            <?= strlen($galeria_relacionada->descripcion) > 100 ? '...' : '' ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-galeria-detalle-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 80px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="cta-content-galeria-detalle text-white">
                    <h2 class="cta-title-galeria-detalle mb-3">¿Te Gustó Esta Imagen?</h2>
                    <p class="cta-description-galeria-detalle mb-0">
                        Contáctanos para hacer realidad tu proyecto de construcción.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="cta-buttons-galeria-detalle">
                    <a href="<?= base_url('contacto') ?>" class="btn-cta-galeria-detalle btn-primary-modern">
                        <i class="fas fa-phone"></i> Solicitar Cotización
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== GALERÍA DETALLE MODERNOS CSS ===== */

/* Hero Section */
.hero-galeria-detalle-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-galeria-detalle {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-galeria-detalle" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(240,132,26,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-galeria-detalle)"/></svg>');
    opacity: 0.3;
}

.hero-badge-galeria-detalle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.hero-title-galeria-detalle {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-category-galeria-detalle {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

/* Main Image */
.main-image-container-modern {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
}

.main-image-modern-img {
    width: 100%;
    height: auto;
    display: block;
}

.no-image-main-modern {
    width: 100%;
    height: 400px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
}

.no-image-main-modern i {
    font-size: 4rem;
    margin-bottom: 15px;
}

.no-image-main-modern span {
    font-size: 1.2rem;
    font-weight: 500;
}

.image-actions-modern {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
}

.btn-image-action-modern {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-zoom-main-modern {
    background: rgba(240, 132, 26, 0.9);
    color: white;
}

.btn-zoom-main-modern:hover {
    background: rgba(240, 132, 26, 1);
    color: white;
    transform: scale(1.1);
}

.btn-back-modern {
    background: rgba(29, 40, 68, 0.9);
    color: white;
}

.btn-back-modern:hover {
    background: rgba(29, 40, 68, 1);
    color: white;
    transform: scale(1.1);
}

/* Gallery Info Section */
.gallery-info-section {
    background: #f8f9fa;
    padding: 80px 0;
    position: relative;
}

.gallery-info-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-info" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(29,40,68,0.05)"/><circle cx="75" cy="75" r="1" fill="rgba(29,40,68,0.05)"/><circle cx="50" cy="10" r="0.5" fill="rgba(29,40,68,0.03)"/><circle cx="10" cy="60" r="0.5" fill="rgba(29,40,68,0.03)"/><circle cx="90" cy="40" r="0.5" fill="rgba(29,40,68,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-info)"/></svg>');
    opacity: 0.5;
}

.gallery-info-card {
    background: white;
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    z-index: 2;
}

/* Header */
.gallery-info-header {
    background: linear-gradient(135deg, #1d2844 0%, #4a5f7a 100%);
    padding: 40px 50px;
    position: relative;
    overflow: hidden;
}

.gallery-info-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-header-info" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(240,132,26,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-header-info)"/></svg>');
    opacity: 0.3;
}

.info-header-content {
    display: flex;
    align-items: center;
    gap: 25px;
    position: relative;
    z-index: 2;
}

.info-icon {
    width: 80px;
    height: 80px;
    background: rgba(240, 132, 26, 0.2);
    border: 3px solid rgba(240, 132, 26, 0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 2rem;
    color: #f0841a;
}

.info-title-content {
    flex-grow: 1;
}

.info-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
    margin: 0 0 10px 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.info-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-weight: 400;
}

/* Content */
.gallery-info-content {
    padding: 60px 50px;
    background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 40px;
}

.info-item {
    background: white;
    border-radius: 25px;
    padding: 35px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.info-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 25px 25px 0 0;
}

.info-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.item-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 25px;
}

.item-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(240, 132, 26, 0.3);
    position: relative;
    overflow: hidden;
}

.item-icon::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
    animation: shimmer 3s ease-in-out infinite;
}

.item-icon i {
    font-size: 1.5rem;
    color: white;
    position: relative;
    z-index: 2;
}

.item-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: #1d2844;
    margin: 0;
}

.item-content {
    margin-top: 20px;
}

.item-text {
    font-size: 1.1rem;
    color: #555;
    line-height: 1.8;
    margin: 0;
    font-weight: 400;
}

/* Category Badge */
.category-badge {
    display: inline-block;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1rem;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
    transition: all 0.3s ease;
}

.category-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
}

.category-text {
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

@keyframes shimmer {
    0%, 100% { transform: translateX(-100%) translateY(-100%) rotate(30deg); }
    50% { transform: translateX(100%) translateY(100%) rotate(30deg); }
}

/* Related Gallery */
.related-header-modern {
    margin-bottom: 50px;
}

.related-title-modern {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1d2844;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.related-title-modern i {
    color: #f0841a;
}

.related-subtitle-modern {
    font-size: 1.2rem;
    color: #666;
    margin: 0;
}

.related-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.related-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.related-image-modern {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.related-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.related-card-modern:hover .related-image-modern img {
    transform: scale(1.1);
}

.no-image-related-modern {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
    font-size: 2rem;
}

.related-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(29, 40, 68, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.related-card-modern:hover .related-overlay-modern {
    opacity: 1;
}

.btn-related-modern {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #f0841a;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.btn-related-modern:hover {
    background: #e67e00;
    color: white;
    transform: scale(1.1);
}

.related-content-modern {
    padding: 25px;
}

.related-card-title-modern {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
    line-height: 1.4;
}

.related-card-title-modern a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-card-title-modern a:hover {
    color: #f0841a;
    text-decoration: none;
}

.related-description-modern {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
}

/* CTA Section */
.cta-galeria-detalle-modern {
    position: relative;
    overflow: hidden;
}

.cta-title-galeria-detalle {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.cta-description-galeria-detalle {
    font-size: 1.2rem;
    opacity: 0.9;
}

.btn-cta-galeria-detalle {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: white;
    color: #f0841a;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.btn-cta-galeria-detalle:hover {
    background: #f8f9fa;
    color: #e67e00;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title-galeria-detalle {
        font-size: 2.2rem;
    }
    
    .main-image-container-modern {
        border-radius: 15px;
    }
    
    .image-actions-modern {
        top: 15px;
        right: 15px;
    }
    
    .btn-image-action-modern {
        width: 45px;
        height: 45px;
        font-size: 1rem;
    }
    
    .image-info-content-modern {
        padding: 30px 20px;
    }
    
    .image-description-modern {
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .image-details-modern {
        padding: 25px;
    }
    
    .detail-item-modern {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px;
    }
    
    .detail-icon-modern {
        width: 60px;
        height: 60px;
    }
    
    .detail-icon-modern i {
        font-size: 1.5rem;
    }
    
    .related-title-modern {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .cta-title-galeria-detalle {
        font-size: 2rem;
    }
    
    .cta-buttons-galeria-detalle {
        text-align: center;
        margin-top: 20px;
    }
}

@media (max-width: 576px) {
    .hero-title-galeria-detalle {
        font-size: 1.8rem;
    }
    
    .image-info-header-modern {
        padding: 25px 20px;
    }
    
    .image-info-title-modern {
        font-size: 1.6rem;
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .image-info-title-modern i {
        font-size: 1.4rem;
        padding: 10px;
    }
    
    .image-info-content-modern {
        padding: 30px 20px;
    }
    
    .image-description-modern {
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .description-title-modern {
        font-size: 1.2rem;
    }
    
    .image-details-modern {
        padding: 20px;
    }
    
    .detail-item-modern {
        padding: 20px;
        gap: 15px;
    }
    
    .detail-icon-modern {
        width: 50px;
        height: 50px;
    }
    
    .detail-icon-modern i {
        font-size: 1.3rem;
    }
    
    .detail-title-modern {
        font-size: 1.1rem;
    }
    
    .detail-text-modern {
        font-size: 1rem;
    }
    
    .related-image-modern {
        height: 150px;
    }
    
    .related-content-modern {
        padding: 20px;
    }
    
    .cta-title-galeria-detalle {
        font-size: 1.6rem;
    }
    
    /* Gallery Info Responsive */
    .gallery-info-section {
        padding: 60px 0;
    }
    
    .gallery-info-header {
        padding: 30px 30px;
    }
    
    .info-header-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .info-icon {
        width: 70px;
        height: 70px;
    }
    
    .info-icon i {
        font-size: 1.8rem;
    }
    
    .info-title {
        font-size: 2rem;
    }
    
    .info-subtitle {
        font-size: 1.1rem;
    }
    
    .gallery-info-content {
        padding: 40px 30px;
    }
    
    .info-item {
        padding: 25px;
    }
    
    .item-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .item-icon {
        width: 50px;
        height: 50px;
    }
    
    .item-icon i {
        font-size: 1.3rem;
    }
    
    .item-title {
        font-size: 1.4rem;
    }
    
    .item-text {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .hero-title-galeria-detalle {
        font-size: 1.8rem;
    }
    
    .main-image-container-modern {
        border-radius: 15px;
    }
    
    .image-actions-modern {
        top: 15px;
        right: 15px;
    }
    
    .btn-image-action-modern {
        width: 45px;
        height: 45px;
        font-size: 1rem;
    }
    
    .image-info-content-modern {
        padding: 30px 20px;
    }
    
    .image-description-modern {
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .image-details-modern {
        padding: 25px;
    }
    
    .detail-item-modern {
        flex-direction: column;
        text-align: center;
        gap: 20px;
        padding: 25px;
    }
    
    .detail-icon-modern {
        width: 60px;
        height: 60px;
    }
    
    .detail-icon-modern i {
        font-size: 1.5rem;
    }
    
    .related-title-modern {
        font-size: 2rem;
        flex-direction: column;
        gap: 10px;
    }
    
    .cta-title-galeria-detalle {
        font-size: 2rem;
    }
    
    .cta-buttons-galeria-detalle {
        text-align: center;
        margin-top: 20px;
    }
    
    /* Gallery Info Responsive */
    .gallery-info-section {
        padding: 40px 0;
    }
    
    .gallery-info-header {
        padding: 25px 20px;
    }
    
    .info-icon {
        width: 60px;
        height: 60px;
    }
    
    .info-icon i {
        font-size: 1.5rem;
    }
    
    .info-title {
        font-size: 1.8rem;
    }
    
    .info-subtitle {
        font-size: 1rem;
    }
    
    .gallery-info-content {
        padding: 30px 20px;
    }
    
    .info-item {
        padding: 20px;
    }
    
    .item-icon {
        width: 45px;
        height: 45px;
    }
    
    .item-icon i {
        font-size: 1.2rem;
    }
    
    .item-title {
        font-size: 1.3rem;
    }
    
    .item-text {
        font-size: 0.95rem;
    }
    
    .category-badge {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
}
</style>

<!-- Image Modal -->
<div class="image-modal-modern" id="imageModal">
    <div class="image-modal-backdrop-modern" onclick="closeImageModal()"></div>
    <div class="image-modal-container-modern">
        <div class="image-modal-header-modern">
            <h3 class="image-modal-title-modern" id="modalImageTitle"></h3>
            <button class="image-modal-close-modern" onclick="closeImageModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="image-modal-content-modern">
            <img id="modalImage" src="" alt="" class="image-modal-img-modern">
        </div>
        <div class="image-modal-footer-modern">
            <!--button class="btn-modal-modern btn-download-modern" onclick="downloadImage()">
                <i class="fas fa-download"></i> Descargar
            </button-->
            <button class="btn-modal-modern btn-share-modern" onclick="shareImage()">
                <i class="fas fa-share"></i> Compartir
            </button>
        </div>
    </div>
</div>

<style>
/* Image Modal Styles */
.image-modal-modern {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-modal-modern.show {
    display: flex;
    opacity: 1;
}

.image-modal-backdrop-modern {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(10px);
}

.image-modal-container-modern {
    position: relative;
    width: 90%;
    max-width: 1200px;
    max-height: 90%;
    margin: auto;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.image-modal-modern.show .image-modal-container-modern {
    transform: scale(1);
}

.image-modal-header-modern {
    background: linear-gradient(135deg, #1d2844, #4a5f7a);
    padding: 20px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.image-modal-title-modern {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    flex-grow: 1;
}

.image-modal-close-modern {
    background: rgba(240, 132, 26, 0.2);
    border: 2px solid rgba(240, 132, 26, 0.3);
    color: #f0841a;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-modal-close-modern:hover {
    background: #f0841a;
    color: white;
    transform: scale(1.1);
}

.image-modal-content-modern {
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
}

.image-modal-img-modern {
    max-width: 100%;
    max-height: 70vh;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.image-modal-img-modern:hover {
    transform: scale(1.02);
}

.image-modal-footer-modern {
    background: white;
    padding: 20px 30px;
    display: flex;
    gap: 15px;
    justify-content: center;
    border-top: 1px solid #eee;
}

.btn-modal-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-download-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.btn-download-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
}

.btn-share-modern {
    background: transparent;
    color: #1d2844;
    border: 2px solid #1d2844;
}

.btn-share-modern:hover {
    background: #1d2844;
    color: white;
    transform: translateY(-2px);
}

/* Responsive Modal */
@media (max-width: 768px) {
    .image-modal-container-modern {
        width: 95%;
        max-height: 95%;
    }
    
    .image-modal-header-modern {
        padding: 15px 20px;
    }
    
    .image-modal-title-modern {
        font-size: 1.2rem;
    }
    
    .image-modal-close-modern {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .image-modal-content-modern {
        padding: 15px;
    }
    
    .image-modal-footer-modern {
        padding: 15px 20px;
        flex-direction: column;
    }
    
    .btn-modal-modern {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .image-modal-container-modern {
        width: 98%;
        max-height: 98%;
    }
    
    .image-modal-header-modern {
        padding: 12px 15px;
    }
    
    .image-modal-title-modern {
        font-size: 1rem;
    }
    
    .image-modal-content-modern {
        padding: 10px;
    }
    
    .image-modal-img-modern {
        max-height: 60vh;
    }
}
</style>

<script>
// Image Modal Functions
function openImageModal(imageSrc, imageTitle) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalImageTitle');
    
    modalImage.src = imageSrc;
    modalImage.alt = imageTitle;
    modalTitle.textContent = imageTitle;
    modalTitle.style.color = 'white';
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Store current image for download/share
    window.currentModalImage = {
        src: imageSrc,
        title: imageTitle
    };
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
    
    // Clear current image
    window.currentModalImage = null;
}

function downloadImage() {
    if (window.currentModalImage) {
        const link = document.createElement('a');
        link.href = window.currentModalImage.src;
        link.download = window.currentModalImage.title.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function shareImage() {
    if (window.currentModalImage && navigator.share) {
        navigator.share({
            title: window.currentModalImage.title,
            text: 'Mira esta imagen de NextLine Constructor',
            url: window.currentModalImage.src
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.currentModalImage.src).then(() => {
            alert('Enlace copiado al portapapeles');
        });
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('imageModal');
    if (e.target === modal) {
        closeImageModal();
    }
});
</script>

<?= $this->endSection() ?>


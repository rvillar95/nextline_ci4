<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-galeria-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-galeria"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-galeria text-white">
                    <div class="hero-badge-galeria">
                        <i class="fas fa-images"></i>
                        <span>GALERÍA DE OBRAS</span>
                    </div>
                    <h1 class="hero-title-galeria mb-4"><?= $titulo_pagina ?></h1>
                    <p class="hero-subtitle-galeria lead mb-4">
                        Descubre nuestros proyectos construidos con excelencia y calidad en Linares, Maule.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtros de Categorías -->
<?php if (!empty($categorias)): ?>
<section class="filters-galeria-modern" style="background: white; padding: 40px 0; border-bottom: 1px solid #eee;">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="filter-buttons-modern text-center">
                    <a href="<?= base_url('galeria') ?>" 
                       class="btn-filter-modern <?= !$categoria_actual ? 'active' : '' ?>">
                        <i class="fas fa-th"></i>
                        <span>Todas las Categorías</span>
                    </a>
                    <?php foreach ($categorias as $categoria): ?>
                        <a href="<?= base_url('galeria?categoria=' . $categoria->slug) ?>" 
                           class="btn-filter-modern <?= $categoria_actual && $categoria_actual->id == $categoria->id ? 'active' : '' ?>">
                            <?php if (!empty($categoria->icono)): ?>
                                <i class="<?= $categoria->icono ?>"></i>
                            <?php else: ?>
                                <i class="fas fa-folder"></i>
                            <?php endif; ?>
                            <span><?= $categoria->nombre ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Galería de Imágenes -->
<section class="gallery-modern" style="background: #f8f9fa; padding: 80px 0;">
    <div class="container">
        <?php if (!empty($galerias)): ?>
            <div class="row">
                <?php foreach ($galerias as $galeria): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="gallery-card-modern">
                            <div class="gallery-image-modern">
                                <?php if (!empty($galeria->portada) && $galeria->portada): ?>
                                    <img src="<?= base_url($galeria->portada) ?>" 
                                         alt="<?= esc($galeria->nombre) ?>" 
                                         class="img-fluid">
                                <?php else: ?>
                                    <div class="no-image-modern">
                                        <i class="fas fa-image"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="gallery-overlay-modern">
                                    <div class="gallery-actions-modern">
                                        <a href="<?= base_url('galeria/detalle/' . $galeria->id) ?>" 
                                           class="btn-gallery-modern btn-view-modern" 
                                           title="Ver Detalles">
                                            <i class="fas fa-eye" style="color: white;"></i>
                                        </a>
                                        <?php if (!empty($galeria->portada) && $galeria->portada): ?>
                                            <a href="#" 
                                               class="btn-gallery-modern btn-zoom-modern" 
                                               onclick="openImageModal('<?= base_url($galeria->portada) ?>', '<?= esc($galeria->nombre) ?>')"
                                               title="Ampliar">
                                                <i class="fas fa-search-plus"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="gallery-content-modern">
                                <div class="gallery-category-modern">
                                    <?php if (!empty($galeria->categoria_nombre)): ?>
                                        <span class="category-badge-modern">
                                            <?= esc($galeria->categoria_nombre) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 class="gallery-title-modern">
                                    <a href="<?= base_url('galeria/detalle/' . $galeria->id) ?>">
                                        <?= esc($galeria->nombre) ?>
                                    </a>
                                </h3>
                                
                                <?php if (!empty($galeria->descripcion)): ?>
                                    <p class="gallery-description-modern">
                                        <?= esc(substr($galeria->descripcion, 0, 120)) ?>
                                        <?= strlen($galeria->descripcion) > 120 ? '...' : '' ?>
                                    </p>
                                <?php endif; ?>
                                
                                <div class="gallery-footer-modern">
                                    <div class="gallery-date-modern">
                                        <i class="fas fa-calendar"></i>
                                        <span><?= date('d/m/Y', strtotime($galeria->fcreacion)) ?></span>
                                    </div>
                                    <a href="<?= base_url('galeria/detalle/' . $galeria->id) ?>" 
                                       class="btn-read-more-modern">
                                        Ver Detalles <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Estado Vacío -->
            <div class="empty-state-modern text-center">
                <div class="empty-icon-modern">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="empty-title-modern">No hay imágenes disponibles</h3>
                <p class="empty-description-modern">
                    <?php if ($categoria_actual): ?>
                        No hay imágenes en la categoría "<?= esc($categoria_actual->nombre) ?>".
                    <?php else: ?>
                        Pronto agregaremos más imágenes a nuestra galería.
                    <?php endif; ?>
                </p>
                <a href="<?= base_url('galeria') ?>" class="btn-empty-modern">
                    <i class="fas fa-arrow-left"></i> Ver Todas las Categorías
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-galeria-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 80px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="cta-content-galeria text-white">
                    <h2 class="cta-title-galeria mb-3">¿Te Gustó Nuestro Trabajo?</h2>
                    <p class="cta-description-galeria mb-0">
                        Contáctanos para hacer realidad tu proyecto de construcción.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="cta-buttons-galeria">
                    <a href="<?= base_url('contacto') ?>" class="btn-cta-galeria btn-primary-modern">
                        <i class="fas fa-phone"></i> Solicitar Cotización
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== GALERÍA MODERNOS CSS ===== */

/* Hero Section */
.hero-galeria-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-galeria {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-galeria" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(240,132,26,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-galeria)"/></svg>');
    opacity: 0.3;
}

.hero-badge-galeria {
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

.hero-title-galeria {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-galeria {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 30px;
    line-height: 1.6;
}

/* Filtros */
.filter-buttons-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    align-items: center;
}

.btn-filter-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: white;
    color: #666;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.95rem;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.btn-filter-modern:hover {
    background: #1d2844;
    color: white;
    border-color: #1d2844;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(29, 40, 68, 0.3);
    text-decoration: none;
}

.btn-filter-modern.active {
    background: linear-gradient(135deg, #1d2844, #4a5f7a);
    color: white;
    border-color: #1d2844;
    box-shadow: 0 5px 20px rgba(29, 40, 68, 0.3);
}

.btn-filter-modern i {
    font-size: 1rem;
}

/* Gallery Cards */
.gallery-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.gallery-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.gallery-image-modern {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.gallery-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card-modern:hover .gallery-image-modern img {
    transform: scale(1.1);
}

.no-image-modern {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #6c757d;
}

.no-image-modern i {
    font-size: 3rem;
    margin-bottom: 10px;
}

.gallery-overlay-modern {
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

.gallery-card-modern:hover .gallery-overlay-modern {
    opacity: 1;
}

.gallery-actions-modern {
    display: flex;
    gap: 15px;
}

.btn-gallery-modern {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.btn-view-modern {
    background: #f0841a;
    color: white;
}

.btn-view-modern:hover {
    background: #e67e00;
    color: white;
    transform: scale(1.1);
}

.btn-zoom-modern {
    background: white;
    color: #1d2844;
}

.btn-zoom-modern:hover {
    background: #f8f9fa;
    color: #1d2844;
    transform: scale(1.1);
}

/* Gallery Content */
.gallery-content-modern {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.gallery-category-modern {
    margin-bottom: 15px;
}

.category-badge-modern {
    display: inline-block;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 6px 15px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.gallery-title-modern {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
    line-height: 1.4;
}

.gallery-title-modern a {
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.gallery-title-modern a:hover {
    color: #f0841a;
    text-decoration: none;
}

.gallery-description-modern {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 20px;
    flex-grow: 1;
}

.gallery-footer-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.gallery-date-modern {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #999;
    font-size: 0.9rem;
}

.gallery-date-modern i {
    font-size: 0.8rem;
}

.btn-read-more-modern {
    color: #f0841a;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.btn-read-more-modern:hover {
    color: #e67e00;
    text-decoration: none;
}

.btn-read-more-modern i {
    margin-left: 5px;
    transition: transform 0.3s ease;
}

.btn-read-more-modern:hover i {
    transform: translateX(3px);
}

/* Empty State */
.empty-state-modern {
    padding: 80px 20px;
}

.empty-icon-modern {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    box-shadow: 0 10px 30px rgba(240, 132, 26, 0.3);
}

.empty-icon-modern i {
    font-size: 3rem;
    color: white;
}

.empty-title-modern {
    font-size: 2rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
}

.empty-description-modern {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.btn-empty-modern {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    background: #1d2844;
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-empty-modern:hover {
    background: #2c3e50;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
}

/* CTA Section */
.cta-galeria-modern {
    position: relative;
    overflow: hidden;
}

.cta-title-galeria {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.cta-description-galeria {
    font-size: 1.2rem;
    opacity: 0.9;
}

.btn-cta-galeria {
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

.btn-cta-galeria:hover {
    background: #f8f9fa;
    color: #e67e00;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title-galeria {
        font-size: 2.2rem;
    }
    
    .filter-buttons-modern {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-filter-modern {
        justify-content: center;
    }
    
    .gallery-image-modern {
        height: 200px;
    }
    
    .gallery-content-modern {
        padding: 20px;
    }
    
    .gallery-footer-modern {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .cta-title-galeria {
        font-size: 2rem;
    }
    
    .cta-buttons-galeria {
        text-align: center;
        margin-top: 20px;
    }
}

@media (max-width: 576px) {
    .hero-title-galeria {
        font-size: 1.8rem;
    }
    
    .gallery-image-modern {
        height: 180px;
    }
    
    .gallery-content-modern {
        padding: 15px;
    }
    
    .gallery-title-modern {
        font-size: 1.1rem;
    }
    
    .empty-title-modern {
        font-size: 1.6rem;
    }
    
    .cta-title-galeria {
        font-size: 1.6rem;
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

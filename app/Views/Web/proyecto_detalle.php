<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-project-detail-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-project-detail"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-project-detail text-white">
                    <div class="hero-badge-project-detail">
                        <i class="fas fa-building"></i>
                        <span>PROYECTO</span>
                    </div>
                    <h1 class="hero-title-project-detail mb-4"><?= esc($proyecto->nombre) ?></h1>
                    <p class="hero-subtitle-project-detail lead mb-4">
                        <?= !empty($proyecto->descripcion_corta) ? esc($proyecto->descripcion_corta) : 'Un proyecto de construcción excepcional que refleja nuestra pasión por la excelencia y la innovación.' ?>
                    </p>
                    <nav aria-label="breadcrumb" class="breadcrumb-modern">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('proyectos') ?>">Proyectos</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($proyecto->nombre) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Proyecto Detalle Moderno -->
<section class="project-detail-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <div class="project-detail-content-modern">
                    <!-- Header del Proyecto -->
                    <div class="project-header-modern">
                        <!-- Badges -->
                        <div class="project-badges-modern">
                            <span class="badge-modern badge-tipo-modern">
                                <i class="fas fa-tools"></i>
                                <?= ucfirst($proyecto->tipo_proyecto) ?>
                            </span>
                            <span class="badge-modern badge-estado-modern">
                                <i class="fas fa-check-circle"></i>
                                <?= ucfirst(str_replace('_', ' ', $proyecto->estado)) ?>
                            </span>
                            <?php if ($proyecto->destacado): ?>
                                <span class="badge-modern badge-destacado-modern">
                                    <i class="fas fa-star"></i>
                                    Destacado
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información del Proyecto -->
                        <div class="project-info-grid-modern">
                            <?php if ($proyecto->cliente): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Cliente</span>
                                        <span class="info-value-modern"><?= esc($proyecto->cliente) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->ubicacion): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Ubicación</span>
                                        <span class="info-value-modern"><?= esc($proyecto->ubicacion) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->area_construida): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-ruler-combined"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Área Construida</span>
                                        <span class="info-value-modern"><?= number_format($proyecto->area_construida, 0, ',', '.') ?> m²</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-ruler-combined"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Área Construida</span>
                                        <span class="info-value-modern"><em>Por especificar</em></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->presupuesto && $proyecto->mostrar_presupuesto): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Presupuesto</span>
                                        <span class="info-value-modern">$<?= number_format($proyecto->presupuesto, 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->fecha_inicio): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Fecha de Inicio</span>
                                        <span class="info-value-modern"><?= date('d/m/Y', strtotime($proyecto->fecha_inicio)) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->fecha_finalizacion): ?>
                                <div class="info-item-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="info-content-modern">
                                        <span class="info-label-modern">Fecha de Finalización</span>
                                        <span class="info-value-modern"><?= date('d/m/Y', strtotime($proyecto->fecha_finalizacion)) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Descripción Corta -->
                    <?php if ($proyecto->descripcion_corta): ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-clipboard-list"></i>
                                Resumen del Proyecto
                            </h2>
                            <div class="section-content-modern">
                                <p class="lead-modern"><?= esc($proyecto->descripcion_corta) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Descripción Detallada -->
                    <?php if ($proyecto->descripcion_detallada): ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-file-alt"></i>
                                Descripción Detallada
                            </h2>
                            <div class="section-content-modern">
                                <p><?= nl2br(esc($proyecto->descripcion_detallada)) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Características Técnicas -->
                    <?php if ($proyecto->caracteristicas_tecnicas): ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-cogs"></i>
                                Características Técnicas
                            </h2>
                            <div class="section-content-modern">
                                <p><?= nl2br(esc($proyecto->caracteristicas_tecnicas)) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-cogs"></i>
                                Características Técnicas
                            </h2>
                            <div class="section-content-modern">
                                <p><em>Las características técnicas de este proyecto se pueden agregar desde el panel de administración.</em></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Materiales -->
                    <?php if ($proyecto->materiales_principales): ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-hammer"></i>
                                Materiales Principales
                            </h2>
                            <div class="section-content-modern">
                                <p><?= esc($proyecto->materiales_principales) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="content-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-hammer"></i>
                                Materiales Principales
                            </h2>
                            <div class="section-content-modern">
                                <p><em>Los materiales principales utilizados en este proyecto se pueden especificar desde el panel de administración.</em></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Testimonio del Cliente -->
                    <?php if ($proyecto->testimonio_cliente && $proyecto->nombre_cliente): ?>
                        <div class="testimonial-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-quote-left"></i>
                                Testimonio del Cliente
                            </h2>
                            <div class="testimonial-card-modern">
                                <div class="testimonial-content-modern">
                                    <i class="fas fa-quote-left testimonial-quote-modern"></i>
                                    <p class="testimonial-text-modern">"<?= esc($proyecto->testimonio_cliente) ?>"</p>
                                    <div class="testimonial-author-modern">
                                        <strong><?= esc($proyecto->nombre_cliente) ?></strong>
                                        <?php if ($proyecto->cliente): ?>
                                            <span class="author-role-modern">Cliente del proyecto</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="testimonial-section-modern">
                            <h2 class="section-title-modern">
                                <i class="fas fa-quote-left"></i>
                                Testimonio del Cliente
                            </h2>
                            <div class="testimonial-card-modern">
                                <div class="testimonial-content-modern">
                                    <i class="fas fa-quote-left testimonial-quote-modern"></i>
                                    <p class="testimonial-text-modern"><em>El testimonio del cliente se puede agregar desde el panel de administración.</em></p>
                                    <div class="testimonial-author-modern">
                                        <strong>Cliente</strong>
                                        <span class="author-role-modern">Testimonio pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="project-sidebar-modern">
                    <!-- Galería de Imágenes -->
                    <?php if (!empty($imagenes)): ?>
                        <div class="sidebar-widget-modern gallery-widget-modern">
                            <h3 class="widget-title-modern">
                                <i class="fas fa-images"></i>
                                Galería del Proyecto
                            </h3>
                            
                            <!-- Imagen Principal -->
                            <?php 
                            $imagenPrincipal = array_filter($imagenes, function($img) { return $img->es_portada; });
                            $imagenPrincipal = !empty($imagenPrincipal) ? reset($imagenPrincipal) : $imagenes[0];
                            ?>
                            <div class="main-gallery-image-modern">
                                <img src="<?= base_url($imagenPrincipal->ruta) ?>" class="img-fluid" alt="<?= esc($proyecto->nombre) ?>" id="main-image">
                                <div class="gallery-overlay-modern">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </div>
                            
                            <!-- Thumbnails -->
                            <?php if (count($imagenes) > 1): ?>
                                <div class="gallery-thumbnails-modern">
                                    <?php foreach ($imagenes as $imagen): ?>
                                        <img src="<?= base_url($imagen->ruta) ?>" 
                                             class="gallery-thumbnail-modern <?= $imagen->id === $imagenPrincipal->id ? 'active' : '' ?>" 
                                             alt="Imagen del proyecto"
                                             onclick="changeMainImage('<?= base_url($imagen->ruta) ?>', this)">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Información de Contacto -->
                    <div class="sidebar-widget-modern contact-widget-modern">
                        <h3 class="widget-title-modern">
                            <i class="fas fa-envelope"></i>
                            ¿Te Interesa Este Proyecto?
                        </h3>
                        <p class="widget-description-modern">Contáctanos para conocer más detalles o solicitar una cotización similar.</p>
                        <div class="contact-buttons-modern">
                            <a href="<?= base_url('contacto') ?>" class="btn-contact-modern btn-primary-modern">
                                <i class="fas fa-envelope"></i> Contactar
                            </a>
                            <a href="<?= base_url('servicios') ?>" class="btn-contact-modern btn-secondary-modern">
                                <i class="fas fa-tools"></i> Ver Servicios
                            </a>
                        </div>
                    </div>
                    
                    <!-- Proyectos Relacionados -->
                    <?php if (!empty($proyectos_relacionados)): ?>
                        <div class="sidebar-widget-modern related-widget-modern">
                            <h3 class="widget-title-modern">
                                <i class="fas fa-project-diagram"></i>
                                Proyectos Similares
                            </h3>
                            <div class="related-projects-modern">
                                <?php foreach ($proyectos_relacionados as $proyectoRel): ?>
                                    <div class="related-project-item-modern">
                                        <div class="related-project-image-modern">
                                            <?php if ($proyectoRel->imagen_portada): ?>
                                                <img src="<?= base_url($proyectoRel->imagen_portada->ruta) ?>" alt="<?= esc($proyectoRel->nombre) ?>">
                                            <?php else: ?>
                                                <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" alt="Proyecto sin imagen">
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-project-content-modern">
                                            <h4><a href="<?= base_url('proyectos/' . $proyectoRel->slug) ?>"><?= esc($proyectoRel->nombre) ?></a></h4>
                                            <p><?= esc($proyectoRel->descripcion_corta) ?></p>
                                            <span class="related-project-type-modern"><?= ucfirst($proyectoRel->tipo_proyecto) ?></span>
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
/* ===== PROYECTO DETALLE MODERNOS CSS ===== */

/* Hero Section */
.hero-project-detail-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-project-detail {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-project-detail" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(240,132,26,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-project-detail)"/></svg>');
    opacity: 0.3;
}

.hero-badge-project-detail {
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

.hero-title-project-detail {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-project-detail {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 30px;
    line-height: 1.6;
}

.breadcrumb-modern {
    margin-bottom: 0;
}

.breadcrumb-modern .breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 25px;
    padding: 10px 20px;
    margin: 0;
    backdrop-filter: blur(10px);
}

.breadcrumb-modern .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-modern .breadcrumb-item a:hover {
    color: #f0841a;
}

.breadcrumb-modern .breadcrumb-item.active {
    color: white;
}

/* Contenido Principal */
.project-detail-content-modern {
    padding: 40px 0;
}

.project-header-modern {
    margin-bottom: 50px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f0f0f0;
}

.project-badges-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 30px;
}

.badge-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-tipo-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.badge-estado-modern {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.badge-destacado-modern {
    background: linear-gradient(135deg, #ffc107, #ff8c00);
    color: #1d2844;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.project-info-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.info-item-modern {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
    border-left: 4px solid #f0841a;
    transition: all 0.3s ease;
}

.info-item-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    background: #ffffff;
}

.info-icon-modern {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.info-content-modern {
    display: flex;
    flex-direction: column;
}

.info-label-modern {
    font-size: 0.85rem;
    color: #666;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.info-value-modern {
    font-size: 1.1rem;
    color: #1d2844;
    font-weight: 700;
}

/* Secciones de Contenido */
.content-section-modern {
    margin-bottom: 50px;
    padding: 30px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.section-title-modern {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #f0841a;
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
}

.section-title-modern i {
    color: #f0841a;
    font-size: 1.5rem;
}

.section-title-modern::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 50px;
    height: 3px;
    background: #ff6b35;
}

.section-content-modern {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.lead-modern {
    font-size: 1.3rem;
    font-weight: 500;
    color: #1d2844;
    line-height: 1.6;
    margin-bottom: 0;
    background: linear-gradient(135deg, #f8f9fa, #ffffff);
    padding: 25px;
    border-radius: 15px;
    border-left: 4px solid #f0841a;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

/* Testimonio */
.testimonial-section-modern {
    margin-bottom: 50px;
}

.testimonial-card-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    border-radius: 20px;
    padding: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.testimonial-card-modern::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(240, 132, 26, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.testimonial-content-modern {
    position: relative;
    z-index: 2;
}

.testimonial-quote-modern {
    font-size: 3rem;
    color: #f0841a;
    margin-bottom: 20px;
    opacity: 0.7;
}

.testimonial-text-modern {
    font-size: 1.2rem;
    line-height: 1.8;
    font-style: italic;
    margin-bottom: 25px;
    color: #f8f9fa;
}

.testimonial-author-modern {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.testimonial-author-modern strong {
    font-size: 1.1rem;
    color: #ffffff;
}

.author-role-modern {
    font-size: 0.9rem;
    color: #f0841a;
    font-weight: 500;
}

/* Sidebar */
.project-sidebar-modern {
    padding: 40px 0;
}

.sidebar-widget-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.sidebar-widget-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.widget-title-modern {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0841a;
    display: flex;
    align-items: center;
    gap: 10px;
}

.widget-title-modern i {
    color: #f0841a;
    font-size: 1.2rem;
}

.widget-description-modern {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

/* Galería */
.main-gallery-image-modern {
    margin-bottom: 20px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    position: relative;
}

.main-gallery-image-modern img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.main-gallery-image-modern:hover .gallery-overlay-modern {
    opacity: 1;
}

.gallery-overlay-modern i {
    color: white;
    font-size: 2rem;
}

.gallery-thumbnails-modern {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.gallery-thumbnail-modern {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.gallery-thumbnail-modern:hover,
.gallery-thumbnail-modern.active {
    border-color: #f0841a;
    transform: scale(1.1);
}

/* Botones de Contacto */
.contact-buttons-modern {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.btn-contact-modern {
    padding: 15px 25px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
    background: linear-gradient(135deg, #e67e00, #e55a00);
}

.btn-secondary-modern {
    background: transparent;
    color: #1d2844;
    border: 2px solid #1d2844;
}

.btn-secondary-modern:hover {
    background: #1d2844;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

/* Proyectos Relacionados */
.related-projects-modern {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.related-project-item-modern {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.related-project-item-modern:hover {
    background: #f8f9fa;
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.related-project-image-modern {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
}

.related-project-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-project-content-modern {
    flex-grow: 1;
}

.related-project-content-modern h4 {
    margin-bottom: 8px;
    font-size: 1rem;
    font-weight: 600;
}

.related-project-content-modern h4 a {
    color: #1d2844;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-project-content-modern h4 a:hover {
    color: #f0841a;
}

.related-project-content-modern p {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
    margin-bottom: 8px;
}

.related-project-type-modern {
    font-size: 0.8rem;
    color: #f0841a;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title-project-detail {
        font-size: 2rem;
    }
    
    .project-info-grid-modern {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .content-section-modern {
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .section-title-modern {
        font-size: 1.5rem;
    }
    
    .testimonial-card-modern {
        padding: 25px;
    }
    
    .sidebar-widget-modern {
        padding: 20px;
    }
    
    .main-gallery-image-modern img {
        height: 250px;
    }
    
    .gallery-thumbnail-modern {
        width: 60px;
        height: 60px;
    }
    
    .related-project-item-modern {
        flex-direction: column;
        text-align: center;
    }
    
    .related-project-image-modern {
        width: 100%;
        height: 120px;
        margin-bottom: 10px;
    }
}

@media (max-width: 576px) {
    .project-detail-content-modern {
        padding: 20px 0;
    }
    
    .hero-title-project-detail {
        font-size: 1.8rem;
    }
    
    .project-badges-modern {
        gap: 8px;
    }
    
    .badge-modern {
        padding: 8px 16px;
        font-size: 0.8rem;
    }
    
    .info-item-modern {
        padding: 15px;
    }
    
    .info-icon-modern {
        width: 40px;
        height: 40px;
        font-size: 1rem;
        margin-right: 10px;
    }
    
    .content-section-modern {
        padding: 15px;
    }
    
    .sidebar-widget-modern {
        padding: 15px;
    }
}
</style>

<script>
function changeMainImage(src, thumbnail) {
    // Cambiar imagen principal
    document.getElementById('main-image').src = src;
    
    // Actualizar thumbnail activo
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}
</script>

<?= $this->endSection() ?>

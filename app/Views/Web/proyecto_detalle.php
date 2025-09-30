<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<section id="page-header" class="no-bottom page-content" data-bgimage="url(<?= base_url('lib/images/slider/construccion_2.jpg') ?>)">
    <div class="mask">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="spacer-single"></div>
                    <div class="spacer-single"></div>
                    <nav aria-label="breadcrumb">
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

<!-- Proyecto Detalle -->
<section id="section-project-detail" class="no-top">
    <div class="container">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <div class="project-detail-content">
                    <!-- Header del Proyecto -->
                    <div class="project-header">
                        <h1 class="project-title"><?= esc($proyecto->nombre) ?></h1>
                        
                        <!-- Badges -->
                        <div class="project-badges">
                            <span class="badge badge-tipo"><?= ucfirst($proyecto->tipo_proyecto) ?></span>
                            <span class="badge badge-estado"><?= ucfirst(str_replace('_', ' ', $proyecto->estado)) ?></span>
                            <?php if ($proyecto->destacado): ?>
                                <span class="badge badge-destacado">⭐ Destacado</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información del Proyecto -->
                        <div class="project-info-grid">
                            <?php if ($proyecto->cliente): ?>
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <div class="info-content">
                                        <span class="info-label">Cliente</span>
                                        <span class="info-value"><?= esc($proyecto->cliente) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->ubicacion): ?>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div class="info-content">
                                        <span class="info-label">Ubicación</span>
                                        <span class="info-value"><?= esc($proyecto->ubicacion) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->area_construida): ?>
                                <div class="info-item">
                                    <i class="fas fa-ruler-combined"></i>
                                    <div class="info-content">
                                        <span class="info-label">Área Construida</span>
                                        <span class="info-value"><?= number_format($proyecto->area_construida, 0, ',', '.') ?> m²</span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Mostrar área construida de ejemplo si está vacía -->
                                <div class="info-item">
                                    <i class="fas fa-ruler-combined"></i>
                                    <div class="info-content">
                                        <span class="info-label">Área Construida</span>
                                        <span class="info-value"><em>Por especificar</em></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->presupuesto && $proyecto->mostrar_presupuesto): ?>
                                <div class="info-item">
                                    <i class="fas fa-dollar-sign"></i>
                                    <div class="info-content">
                                        <span class="info-label">Presupuesto</span>
                                        <span class="info-value">$<?= number_format($proyecto->presupuesto, 0, ',', '.') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->fecha_inicio): ?>
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div class="info-content">
                                        <span class="info-label">Fecha de Inicio</span>
                                        <span class="info-value"><?= date('d/m/Y', strtotime($proyecto->fecha_inicio)) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($proyecto->fecha_finalizacion): ?>
                                <div class="info-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <div class="info-content">
                                        <span class="info-label">Fecha de Finalización</span>
                                        <span class="info-value"><?= date('d/m/Y', strtotime($proyecto->fecha_finalizacion)) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Descripción Corta -->
                    <?php if ($proyecto->descripcion_corta): ?>
                        <div class="content-section">
                            <h2 class="section-title">Resumen del Proyecto</h2>
                            <div class="section-content">
                                <p class="lead"><?= esc($proyecto->descripcion_corta) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Descripción Detallada -->
                    <?php if ($proyecto->descripcion_detallada): ?>
                        <div class="content-section">
                            <h2 class="section-title">Descripción Detallada</h2>
                            <div class="section-content">
                                <p><?= nl2br(esc($proyecto->descripcion_detallada)) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Características Técnicas -->
                    <?php if ($proyecto->caracteristicas_tecnicas): ?>
                        <div class="content-section">
                            <h2 class="section-title">Características Técnicas</h2>
                            <div class="section-content">
                                <p><?= nl2br(esc($proyecto->caracteristicas_tecnicas)) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Mostrar sección de ejemplo si está vacía -->
                        <div class="content-section">
                            <h2 class="section-title">Características Técnicas</h2>
                            <div class="section-content">
                                <p><em>Las características técnicas de este proyecto se pueden agregar desde el panel de administración.</em></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Materiales -->
                    <?php if ($proyecto->materiales_principales): ?>
                        <div class="content-section">
                            <h2 class="section-title">Materiales Principales</h2>
                            <div class="section-content">
                                <p><?= esc($proyecto->materiales_principales) ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Mostrar sección de ejemplo si está vacía -->
                        <div class="content-section">
                            <h2 class="section-title">Materiales Principales</h2>
                            <div class="section-content">
                                <p><em>Los materiales principales utilizados en este proyecto se pueden especificar desde el panel de administración.</em></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Testimonio del Cliente -->
                    <?php if ($proyecto->testimonio_cliente && $proyecto->nombre_cliente): ?>
                        <div class="testimonial-section">
                            <h2 class="section-title">Testimonio del Cliente</h2>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <i class="fas fa-quote-left testimonial-quote"></i>
                                    <p class="testimonial-text">"<?= esc($proyecto->testimonio_cliente) ?>"</p>
                                    <div class="testimonial-author">
                                        <strong><?= esc($proyecto->nombre_cliente) ?></strong>
                                        <?php if ($proyecto->cliente): ?>
                                            <span class="author-role">Cliente del proyecto</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Mostrar sección de ejemplo si está vacía -->
                        <div class="testimonial-section">
                            <h2 class="section-title">Testimonio del Cliente</h2>
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <i class="fas fa-quote-left testimonial-quote"></i>
                                    <p class="testimonial-text"><em>El testimonio del cliente se puede agregar desde el panel de administración.</em></p>
                                    <div class="testimonial-author">
                                        <strong>Cliente</strong>
                                        <span class="author-role">Testimonio pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="project-sidebar">
                    <!-- Galería de Imágenes -->
                    <?php if (!empty($imagenes)): ?>
                        <div class="sidebar-widget gallery-widget">
                            <h3 class="widget-title">Galería del Proyecto</h3>
                            
                            <!-- Imagen Principal -->
                            <?php 
                            $imagenPrincipal = array_filter($imagenes, function($img) { return $img->es_portada; });
                            $imagenPrincipal = !empty($imagenPrincipal) ? reset($imagenPrincipal) : $imagenes[0];
                            ?>
                            <div class="main-gallery-image">
                                <img src="<?= base_url($imagenPrincipal->ruta) ?>" class="img-fluid" alt="<?= esc($proyecto->nombre) ?>" id="main-image">
                            </div>
                            
                            <!-- Thumbnails -->
                            <?php if (count($imagenes) > 1): ?>
                                <div class="gallery-thumbnails">
                                    <?php foreach ($imagenes as $imagen): ?>
                                        <img src="<?= base_url($imagen->ruta) ?>" 
                                             class="gallery-thumbnail <?= $imagen->id === $imagenPrincipal->id ? 'active' : '' ?>" 
                                             alt="Imagen del proyecto"
                                             onclick="changeMainImage('<?= base_url($imagen->ruta) ?>', this)">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Información de Contacto -->
                    <div class="sidebar-widget contact-widget">
                        <h3 class="widget-title">¿Te Interesa Este Proyecto?</h3>
                        <p class="widget-description">Contáctanos para conocer más detalles o solicitar una cotización similar.</p>
                        <div class="contact-buttons">
                            <a href="<?= base_url('contacto') ?>" class="btn-contact btn-primary">
                                <i class="fas fa-envelope"></i> Contactar
                            </a>
                            <a href="<?= base_url('servicios') ?>" class="btn-contact btn-secondary">
                                <i class="fas fa-tools"></i> Ver Servicios
                            </a>
                        </div>
                    </div>
                    
                    <!-- Proyectos Relacionados -->
                    <?php if (!empty($proyectos_relacionados)): ?>
                        <div class="sidebar-widget related-widget">
                            <h3 class="widget-title">Proyectos Similares</h3>
                            <div class="related-projects">
                                <?php foreach ($proyectos_relacionados as $proyectoRel): ?>
                                    <div class="related-project-item">
                                        <div class="related-project-image">
                                            <?php if ($proyectoRel->imagen_portada): ?>
                                                <img src="<?= base_url($proyectoRel->imagen_portada->ruta) ?>" alt="<?= esc($proyectoRel->nombre) ?>">
                                            <?php else: ?>
                                                <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" alt="Proyecto sin imagen">
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-project-content">
                                            <h4><a href="<?= base_url('proyectos/' . $proyectoRel->slug) ?>"><?= esc($proyectoRel->nombre) ?></a></h4>
                                            <p><?= esc($proyectoRel->descripcion_corta) ?></p>
                                            <span class="related-project-type"><?= ucfirst($proyectoRel->tipo_proyecto) ?></span>
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
/* Estilos modernos para página de detalle de proyecto */
.project-detail-content {
    padding: 40px 0;
}

.project-header {
    margin-bottom: 50px;
    padding-bottom: 30px;
    border-bottom: 2px solid #f0f0f0;
}

.project-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1d2844;
    margin-bottom: 25px;
    line-height: 1.2;
}

.project-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 30px;
}

.badge {
    padding: 10px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-tipo {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.badge-estado {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.badge-destacado {
    background: linear-gradient(135deg, #ffc107, #ff8c00);
    color: #1d2844;
    box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.project-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
    border-left: 4px solid #f0841a;
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    background: #ffffff;
}

.info-item i {
    font-size: 1.5rem;
    color: #f0841a;
    margin-right: 15px;
    width: 30px;
    text-align: center;
}

.info-content {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.85rem;
    color: #666;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.info-value {
    font-size: 1.1rem;
    color: #1d2844;
    font-weight: 700;
}

.content-section {
    margin-bottom: 50px;
    padding: 30px;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 3px solid #f0841a;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 50px;
    height: 3px;
    background: #ff6b35;
}

.section-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #555;
}

.section-content .lead {
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

.testimonial-section {
    margin-bottom: 50px;
}

.testimonial-card {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    border-radius: 20px;
    padding: 40px;
    color: white;
    position: relative;
    overflow: hidden;
}

.testimonial-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(240, 132, 26, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.testimonial-content {
    position: relative;
    z-index: 2;
}

.testimonial-quote {
    font-size: 3rem;
    color: #f0841a;
    margin-bottom: 20px;
    opacity: 0.7;
}

.testimonial-text {
    font-size: 1.2rem;
    line-height: 1.8;
    font-style: italic;
    margin-bottom: 25px;
    color: #f8f9fa;
}

.testimonial-author {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.testimonial-author strong {
    font-size: 1.1rem;
    color: #ffffff;
}

.author-role {
    font-size: 0.9rem;
    color: #f0841a;
    font-weight: 500;
}

/* Sidebar Styles */
.project-sidebar {
    padding: 40px 0;
}

.sidebar-widget {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.sidebar-widget:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.widget-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0841a;
}

.widget-description {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

/* Gallery Widget */
.main-gallery-image {
    margin-bottom: 20px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.main-gallery-image img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.main-gallery-image:hover img {
    transform: scale(1.05);
}

.gallery-thumbnails {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.gallery-thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s ease;
}

.gallery-thumbnail:hover,
.gallery-thumbnail.active {
    border-color: #f0841a;
    transform: scale(1.1);
}

/* Contact Widget */
.contact-buttons {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.btn-contact {
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

.btn-contact.btn-primary {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.btn-contact.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
    background: linear-gradient(135deg, #e67e00, #e55a00);
}

.btn-contact.btn-secondary {
    background: transparent;
    color: #1d2844;
    border: 2px solid #1d2844;
}

.btn-contact.btn-secondary:hover {
    background: #1d2844;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

/* Related Projects */
.related-projects {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.related-project-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
}

.related-project-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.related-project-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
}

.related-project-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-project-content {
    flex-grow: 1;
}

.related-project-content h4 {
    margin-bottom: 8px;
    font-size: 1rem;
    font-weight: 600;
}

.related-project-content h4 a {
    color: #1d2844;
    text-decoration: none;
    transition: color 0.3s ease;
}

.related-project-content h4 a:hover {
    color: #f0841a;
}

.related-project-content p {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
    margin-bottom: 8px;
}

.related-project-type {
    font-size: 0.8rem;
    color: #f0841a;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Responsive */
@media (max-width: 768px) {
    .project-title {
        font-size: 2rem;
    }
    
    .project-info-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .content-section {
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .testimonial-card {
        padding: 25px;
    }
    
    .sidebar-widget {
        padding: 20px;
    }
    
    .main-gallery-image img {
        height: 250px;
    }
    
    .gallery-thumbnail {
        width: 60px;
        height: 60px;
    }
    
    .related-project-item {
        flex-direction: column;
        text-align: center;
    }
    
    .related-project-image {
        width: 100%;
        height: 120px;
        margin-bottom: 10px;
    }
}

@media (max-width: 576px) {
    .project-detail-content {
        padding: 20px 0;
    }
    
    .project-title {
        font-size: 1.8rem;
    }
    
    .project-badges {
        gap: 8px;
    }
    
    .badge {
        padding: 8px 16px;
        font-size: 0.8rem;
    }
    
    .info-item {
        padding: 15px;
    }
    
    .info-item i {
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    .content-section {
        padding: 15px;
    }
    
    .sidebar-widget {
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

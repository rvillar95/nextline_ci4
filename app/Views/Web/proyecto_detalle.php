<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<section id="page-header" class="no-bottom page-content" data-bgimage="url(<?= base_url('lib/images/slider/construction1.jpg') ?>)">
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
                <div class="row">
                    <div class="col-lg-12">
                        <h1><?= esc($proyecto->nombre) ?></h1>
                        <div class="spacer-10"></div>
                        
                        <!-- Badges -->
                        <div class="mb-3">
                            <span class="badge badge-primary me-2"><?= ucfirst($proyecto->tipo_proyecto) ?></span>
                            <span class="badge badge-success me-2"><?= ucfirst(str_replace('_', ' ', $proyecto->estado)) ?></span>
                            <?php if ($proyecto->destacado): ?>
                                <span class="badge badge-warning">Destacado</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Información del Proyecto -->
                        <div class="row mb-4">
                            <?php if ($proyecto->cliente): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-user"></i> Cliente:</strong> <?= esc($proyecto->cliente) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($proyecto->ubicacion): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-map-marker"></i> Ubicación:</strong> <?= esc($proyecto->ubicacion) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($proyecto->area_construida): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-home"></i> Área Construida:</strong> <?= $proyecto->area_construida ?> m²
                                </div>
                            <?php endif; ?>
                            <?php if ($proyecto->presupuesto && $proyecto->mostrar_presupuesto): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-dollar"></i> Presupuesto:</strong> $<?= number_format($proyecto->presupuesto, 0, ',', '.') ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($proyecto->fecha_inicio): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-calendar"></i> Inicio:</strong> <?= date('d/m/Y', strtotime($proyecto->fecha_inicio)) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($proyecto->fecha_finalizacion): ?>
                                <div class="col-md-6 mb-2">
                                    <strong><i class="fa fa-calendar-check"></i> Finalización:</strong> <?= date('d/m/Y', strtotime($proyecto->fecha_finalizacion)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Descripción -->
                        <?php if ($proyecto->descripcion_detallada): ?>
                            <h3>Descripción del Proyecto</h3>
                            <div class="spacer-10"></div>
                            <p><?= nl2br(esc($proyecto->descripcion_detallada)) ?></p>
                            <div class="spacer-20"></div>
                        <?php endif; ?>
                        
                        <!-- Características Técnicas -->
                        <?php if ($proyecto->caracteristicas_tecnicas): ?>
                            <h3>Características Técnicas</h3>
                            <div class="spacer-10"></div>
                            <p><?= nl2br(esc($proyecto->caracteristicas_tecnicas)) ?></p>
                            <div class="spacer-20"></div>
                        <?php endif; ?>
                        
                        <!-- Materiales -->
                        <?php if ($proyecto->materiales_principales): ?>
                            <h3>Materiales Principales</h3>
                            <div class="spacer-10"></div>
                            <p><?= esc($proyecto->materiales_principales) ?></p>
                            <div class="spacer-20"></div>
                        <?php endif; ?>
                        
                        <!-- Testimonio del Cliente -->
                        <?php if ($proyecto->testimonio_cliente && $proyecto->nombre_cliente): ?>
                            <div class="testimonial-box">
                                <h3>Testimonio del Cliente</h3>
                                <div class="spacer-10"></div>
                                <blockquote>
                                    <p>"<?= esc($proyecto->testimonio_cliente) ?>"</p>
                                    <footer>
                                        <strong><?= esc($proyecto->nombre_cliente) ?></strong>
                                        <?php if ($proyecto->cliente): ?>
                                            <br><small>Cliente del proyecto</small>
                                        <?php endif; ?>
                                    </footer>
                                </blockquote>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar">
                    <!-- Galería de Imágenes -->
                    <?php if (!empty($imagenes)): ?>
                        <div class="widget">
                            <h4>Galería del Proyecto</h4>
                            <div class="spacer-10"></div>
                            
                            <!-- Imagen Principal -->
                            <?php 
                            $imagenPrincipal = array_filter($imagenes, function($img) { return $img->es_portada; });
                            $imagenPrincipal = !empty($imagenPrincipal) ? reset($imagenPrincipal) : $imagenes[0];
                            ?>
                            <div class="main-image mb-3">
                                <img src="<?= base_url($imagenPrincipal->ruta) ?>" class="img-fluid rounded" alt="<?= esc($proyecto->nombre) ?>" id="main-image">
                            </div>
                            
                            <!-- Thumbnails -->
                            <?php if (count($imagenes) > 1): ?>
                                <div class="thumbnails">
                                    <?php foreach ($imagenes as $imagen): ?>
                                        <img src="<?= base_url($imagen->ruta) ?>" 
                                             class="thumbnail <?= $imagen->id === $imagenPrincipal->id ? 'active' : '' ?>" 
                                             alt="Imagen del proyecto"
                                             onclick="changeMainImage('<?= base_url($imagen->ruta) ?>', this)">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Información de Contacto -->
                    <div class="widget">
                        <h4>¿Te Interesa Este Proyecto?</h4>
                        <div class="spacer-10"></div>
                        <p>Contáctanos para conocer más detalles o solicitar una cotización similar.</p>
                        <div class="spacer-10"></div>
                        <a href="<?= base_url('contacto') ?>" class="btn-custom btn-full">Contactar</a>
                        <div class="spacer-10"></div>
                        <a href="<?= base_url('servicios') ?>" class="btn-custom btn-outline btn-full">Ver Servicios</a>
                    </div>
                    
                    <!-- Proyectos Relacionados -->
                    <?php if (!empty($proyectos_relacionados)): ?>
                        <div class="widget">
                            <h4>Proyectos Similares</h4>
                            <div class="spacer-10"></div>
                            <?php foreach ($proyectos_relacionados as $proyectoRel): ?>
                                <div class="related-project mb-3">
                                    <div class="row">
                                        <div class="col-4">
                                            <?php if ($proyectoRel->imagen_portada): ?>
                                                <img src="<?= base_url($proyectoRel->imagen_portada->ruta) ?>" class="img-fluid rounded" alt="<?= esc($proyectoRel->nombre) ?>">
                                            <?php else: ?>
                                                <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" class="img-fluid rounded" alt="Proyecto sin imagen">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-8">
                                            <h6><a href="<?= base_url('proyectos/' . $proyectoRel->slug) ?>"><?= esc($proyectoRel->nombre) ?></a></h6>
                                            <p class="small"><?= esc($proyectoRel->descripcion_corta) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.badge {
    font-size: 12px;
    padding: 5px 10px;
}

.badge-primary {
    background-color: var(--primary-color);
    color: white;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.testimonial-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
}

.testimonial-box blockquote {
    margin: 0;
    padding: 0;
    border: none;
}

.testimonial-box blockquote p {
    font-style: italic;
    font-size: 16px;
    margin-bottom: 15px;
}

.testimonial-box blockquote footer {
    font-size: 14px;
    color: #666;
}

.sidebar .widget {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.main-image img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.thumbnails {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
}

.thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 5px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.3s ease;
}

.thumbnail.active,
.thumbnail:hover {
    border-color: var(--primary-color);
}

.btn-full {
    width: 100%;
    text-align: center;
}

.related-project {
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.related-project:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.related-project img {
    width: 100%;
    height: 60px;
    object-fit: cover;
}

.related-project h6 {
    margin-bottom: 5px;
}

.related-project h6 a {
    color: var(--primary-color);
    text-decoration: none;
}

.related-project h6 a:hover {
    text-decoration: underline;
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

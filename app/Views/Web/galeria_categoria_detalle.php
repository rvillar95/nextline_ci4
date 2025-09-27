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
                            <li class="breadcrumb-item"><a href="<?= base_url('galeria-categorias') ?>">Categorías</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= esc($categoria->nombre) ?></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categoría Header -->
<section id="section-category-header" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="category-header">
                    <div class="category-icon-large">
                        <i class="<?= esc($categoria->icono) ?>" style="color: <?= esc($categoria->color) ?>"></i>
                    </div>
                    <div class="category-info">
                        <h1><?= esc($categoria->nombre) ?></h1>
                        <p class="lead"><?= esc($categoria->descripcion) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="category-stats-box">
                    <h4>Obras en esta Categoría</h4>
                    <div class="stat-number"><?= count($galerias) ?></div>
                    <p>Proyectos completados</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Galería de la Categoría -->
<section id="section-category-gallery" class="no-top">
    <div class="container">
        <div class="row">
            <?php if (empty($galerias)): ?>
                <div class="col-lg-12 text-center">
                    <div class="spacer-single"></div>
                    <h3>No hay obras disponibles</h3>
                    <p>Próximamente agregaremos obras en esta categoría.</p>
                    <a href="<?= base_url('galeria') ?>" class="btn-custom">Ver Toda la Galería</a>
                </div>
            <?php else: ?>
                <?php foreach ($galerias as $galeria): ?>
                    <div class="col-lg-4 col-md-6 mb30">
                        <div class="gallery-card">
                            <?php if ($galeria->imagen): ?>
                                <div class="gallery-image">
                                    <img src="<?= base_url($galeria->imagen) ?>" alt="<?= esc($galeria->nombre) ?>">
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <h4><?= esc($galeria->nombre) ?></h4>
                                            <p><?= esc($galeria->descripcion_corta) ?></p>
                                            <div class="gallery-meta">
                                                <?php if ($galeria->ubicacion): ?>
                                                    <span><i class="fa fa-map-marker"></i> <?= esc($galeria->ubicacion) ?></span>
                                                <?php endif; ?>
                                                <?php if ($galeria->area_construida): ?>
                                                    <span><i class="fa fa-home"></i> <?= $galeria->area_construida ?> m²</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="gallery-image">
                                    <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" alt="Obra sin imagen">
                                    <div class="gallery-overlay">
                                        <div class="gallery-info">
                                            <h4><?= esc($galeria->nombre) ?></h4>
                                            <p><?= esc($galeria->descripcion_corta) ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="gallery-content">
                                <h4><?= esc($galeria->nombre) ?></h4>
                                <p><?= esc($galeria->descripcion_corta) ?></p>
                                
                                <div class="gallery-features">
                                    <?php if ($galeria->destacado): ?>
                                        <span class="badge badge-warning">Destacado</span>
                                    <?php endif; ?>
                                    <span class="badge badge-success"><?= ucfirst(str_replace('_', ' ', $galeria->estado)) ?></span>
                                </div>
                                
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('galeria/' . $galeria->slug) ?>" class="btn-custom btn-sm">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Otras Categorías -->
<?php if (!empty($otras_categorias)): ?>
<section id="section-other-categories" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <h2>Otras Categorías</h2>
                    <p>Explora más obras en nuestras otras categorías</p>
                </div>
                <div class="spacer-20"></div>
                
                <div class="row">
                    <?php foreach ($otras_categorias as $otraCategoria): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb20">
                            <div class="other-category-card">
                                <div class="other-category-icon">
                                    <i class="<?= esc($otraCategoria->icono) ?>" style="color: <?= esc($otraCategoria->color) ?>"></i>
                                </div>
                                <h6><a href="<?= base_url('galeria-categorias/' . $otraCategoria->slug) ?>"><?= esc($otraCategoria->nombre) ?></a></h6>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action -->
<section id="section-cta" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <div class="spacer-single"></div>
                    <h2>¿Te gusta nuestro trabajo?</h2>
                    <p class="lead">Contáctanos para hacer realidad tu proyecto de construcción</p>
                    <div class="spacer-10"></div>
                    <a href="<?= base_url('contacto') ?>" class="btn-custom">Solicitar Cotización</a>
                    <a href="<?= base_url('galeria') ?>" class="btn-custom btn-outline">Ver Toda la Galería</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.category-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
}

.category-icon-large {
    font-size: 64px;
    margin-right: 30px;
    flex-shrink: 0;
}

.category-info h1 {
    color: var(--primary-color);
    margin-bottom: 10px;
}

.category-stats-box {
    background: var(--primary-color);
    color: white;
    padding: 30px;
    border-radius: 15px;
    text-align: center;
}

.category-stats-box h4 {
    margin-bottom: 15px;
}

.stat-number {
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 10px;
}

.gallery-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.gallery-card:hover {
    transform: translateY(-5px);
}

.gallery-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.gallery-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-card:hover .gallery-image img {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.8) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    align-items: flex-end;
    padding: 20px;
}

.gallery-card:hover .gallery-overlay {
    opacity: 1;
}

.gallery-info h4 {
    color: white;
    margin-bottom: 5px;
}

.gallery-info p {
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    margin-bottom: 10px;
}

.gallery-meta {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.gallery-meta span {
    color: rgba(255,255,255,0.8);
    font-size: 12px;
}

.gallery-meta i {
    margin-right: 5px;
}

.gallery-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.gallery-content h4 {
    color: var(--primary-color);
    margin-bottom: 10px;
}

.gallery-content p {
    color: #666;
    margin-bottom: 15px;
    flex: 1;
}

.gallery-features {
    margin-bottom: 15px;
}

.badge {
    font-size: 12px;
    padding: 4px 8px;
    margin-right: 5px;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.other-category-card {
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.other-category-card:hover {
    transform: translateY(-5px);
}

.other-category-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.other-category-card h6 {
    margin-bottom: 0;
}

.other-category-card h6 a {
    color: var(--primary-color);
    text-decoration: none;
}

.other-category-card h6 a:hover {
    text-decoration: underline;
}
</style>

<?= $this->endSection() ?>

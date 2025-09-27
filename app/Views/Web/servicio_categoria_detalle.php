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
                            <li class="breadcrumb-item"><a href="<?= base_url('servicios-categorias') ?>">Categorías</a></li>
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
                    <h4>Servicios Disponibles</h4>
                    <div class="stat-number"><?= count($servicios) ?></div>
                    <p>En esta categoría</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Servicios de la Categoría -->
<section id="section-category-services" class="no-top">
    <div class="container">
        <div class="row">
            <?php if (empty($servicios)): ?>
                <div class="col-lg-12 text-center">
                    <div class="spacer-single"></div>
                    <h3>No hay servicios disponibles</h3>
                    <p>Próximamente agregaremos servicios en esta categoría.</p>
                    <a href="<?= base_url('servicios') ?>" class="btn-custom">Ver Todos los Servicios</a>
                </div>
            <?php else: ?>
                <?php foreach ($servicios as $servicio): ?>
                    <div class="col-lg-4 col-md-6 mb30">
                        <div class="service-card">
                            <?php if ($servicio->imagen): ?>
                                <div class="service-image">
                                    <img src="<?= base_url($servicio->imagen) ?>" alt="<?= esc($servicio->nombre) ?>">
                                </div>
                            <?php endif; ?>
                            <div class="service-content">
                                <h4><?= esc($servicio->nombre) ?></h4>
                                <p><?= esc($servicio->descripcion_corta) ?></p>
                                
                                <?php if ($servicio->mostrar_precio && $servicio->precio): ?>
                                    <div class="service-price">
                                        <span class="price">$<?= number_format($servicio->precio, 0, ',', '.') ?></span>
                                        <?php if ($servicio->tipo_precio): ?>
                                            <span class="price-type">/ <?= esc($servicio->tipo_precio) ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="service-features">
                                    <?php if ($servicio->destacado): ?>
                                        <span class="badge badge-warning">Destacado</span>
                                    <?php endif; ?>
                                    <?php if ($servicio->garantia): ?>
                                        <span class="badge badge-info">Garantía</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('servicios/' . $servicio->slug) ?>" class="btn-custom btn-sm">Ver Detalles</a>
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
                    <p>Explora más servicios en nuestras otras categorías</p>
                </div>
                <div class="spacer-20"></div>
                
                <div class="row">
                    <?php foreach ($otras_categorias as $otraCategoria): ?>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb20">
                            <div class="other-category-card">
                                <div class="other-category-icon">
                                    <i class="<?= esc($otraCategoria->icono) ?>" style="color: <?= esc($otraCategoria->color) ?>"></i>
                                </div>
                                <h6><a href="<?= base_url('servicios-categorias/' . $otraCategoria->slug) ?>"><?= esc($otraCategoria->nombre) ?></a></h6>
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
                    <h2>¿Necesitas más información?</h2>
                    <p class="lead">Contáctanos para obtener una cotización personalizada</p>
                    <div class="spacer-10"></div>
                    <a href="<?= base_url('contacto') ?>" class="btn-custom">Solicitar Cotización</a>
                    <a href="<?= base_url('servicios') ?>" class="btn-custom btn-outline">Ver Todos los Servicios</a>
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

.service-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-image {
    height: 200px;
    overflow: hidden;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.service-content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.service-content h4 {
    color: var(--primary-color);
    margin-bottom: 10px;
}

.service-content p {
    color: #666;
    margin-bottom: 15px;
    flex: 1;
}

.service-price {
    margin-bottom: 15px;
}

.price {
    font-size: 24px;
    font-weight: bold;
    color: var(--primary-color);
}

.price-type {
    color: #666;
    font-size: 14px;
}

.service-features {
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

.badge-info {
    background-color: #17a2b8;
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

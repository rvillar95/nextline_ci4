<?php $this->extend('layout/web') ?>

<?= $this->section("content") ?>

<!-- Page Header -->
<section class="page-header page-content">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb-wrapper">
                    <h2><?= esc($servicio->nombre) ?></h2>
                    <ol class="breadcrumb">
                        <li><a href="<?= base_url() ?>">Inicio</a></li>
                        <li><a href="<?= base_url('servicios') ?>">Servicios</a></li>
                        <li class="active"><?= esc($servicio->nombre) ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Detail Section -->
<section class="service-detail-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="service-detail-content">
                    <!-- Service Image -->
                    <div class="service-image">
                        <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-fluid">
                    </div>
                    
                    <!-- Service Info -->
                    <div class="service-info">
                        <div class="service-category">
                            <i class="<?= $servicio->categoria_icono ?? $servicio->icono ?? 'icofont-home' ?>"></i>
                            <?= esc($servicio->categoria_nombre ?? $servicio->categoria ?? 'General') ?>
                        </div>
                        
                        <h1><?= esc($servicio->nombre) ?></h1>
                        
                        <div class="service-description">
                            <h3>Descripción</h3>
                            <p><?= esc($servicio->descripcionCorta) ?></p>
                            
                            <?php if (!empty($servicio->descripcionLarga)): ?>
                                <p><?= esc($servicio->descripcionLarga) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($servicio->caracteristicas)): ?>
                            <div class="service-features">
                                <h3>Características</h3>
                                <ul>
                                    <?php 
                                    $caracteristicas = explode("\n", $servicio->caracteristicas);
                                    foreach ($caracteristicas as $caracteristica): 
                                        if (trim($caracteristica)):
                                    ?>
                                        <li><?= esc(trim($caracteristica)) ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($servicio->beneficios)): ?>
                            <div class="service-benefits">
                                <h3>Beneficios</h3>
                                <ul>
                                    <?php 
                                    $beneficios = explode("\n", $servicio->beneficios);
                                    foreach ($beneficios as $beneficio): 
                                        if (trim($beneficio)):
                                    ?>
                                        <li><?= esc(trim($beneficio)) ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($servicio->tiempo_estimado) || !empty($servicio->garantia)): ?>
                            <div class="service-details">
                                <h3>Detalles del Servicio</h3>
                                <div class="row">
                                    <?php if (!empty($servicio->tiempo_estimado)): ?>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <i class="icofont-clock-time"></i>
                                                <strong>Tiempo Estimado:</strong>
                                                <span><?= esc($servicio->tiempo_estimado) ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($servicio->garantia)): ?>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <i class="icofont-shield"></i>
                                                <strong>Garantía:</strong>
                                                <span><?= esc($servicio->garantia) ?></span>
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
                <div class="service-sidebar">
                    <!-- Price Section -->
                    <?php if ($servicio->mostrar_precio === 'S' && ($servicio->precio_desde || $servicio->precio_hasta)): ?>
                        <div class="price-box">
                            <h3>Precio</h3>
                            <?php if ($servicio->precio_desde && $servicio->precio_hasta): ?>
                                <div class="price-range">
                                    <span class="price-from">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></span>
                                    <span class="price-to">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                </div>
                            <?php elseif ($servicio->precio_desde): ?>
                                <div class="price-range">
                                    <span class="price-from">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></span>
                                </div>
                            <?php elseif ($servicio->precio_hasta): ?>
                                <div class="price-range">
                                    <span class="price-to">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                </div>
                            <?php endif; ?>
                            <p class="price-note">* Precios sujetos a evaluación del proyecto</p>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Contact CTA -->
                    <div class="contact-cta">
                        <h3>¿Interesado en este servicio?</h3>
                        <p>Contáctanos para obtener más información y una cotización personalizada.</p>
                        <a href="<?= base_url('contacto?servicio=' . $servicio->id) ?>" class="btn btn-primary btn-block">
                            <i class="icofont-phone"></i> Solicitar Cotización
                        </a>
                        <a href="<?= base_url('contacto') ?>" class="btn btn-outline-primary btn-block">
                            <i class="icofont-envelope"></i> Contactar
                        </a>
                    </div>
                    
                    <!-- Related Services -->
                    <?php if (!empty($servicios_relacionados)): ?>
                        <div class="related-services">
                            <h3>Servicios Relacionados</h3>
                            <?php foreach ($servicios_relacionados as $servicio_rel): ?>
                                <div class="related-service-item">
                                    <div class="related-service-image">
                                        <img src="<?= base_url($servicio_rel->foto) ?>" alt="<?= esc($servicio_rel->nombre) ?>" class="img-fluid">
                                    </div>
                                    <div class="related-service-content">
                                        <h4><a href="<?= base_url('servicios/' . ($servicio_rel->slug ?? 'servicio-' . $servicio_rel->id)) ?>"><?= esc($servicio_rel->nombre) ?></a></h4>
                                        <p><?= esc($servicio_rel->descripcionCorta) ?></p>
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
.service-detail-section {
    padding: 80px 0;
}

.service-detail-content {
    margin-bottom: 40px;
}

.service-image {
    margin-bottom: 30px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.service-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
}

.service-category {
    display: inline-flex;
    align-items: center;
    background: #667eea;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 20px;
}

.service-category i {
    margin-right: 8px;
    font-size: 1.2rem;
}

.service-info h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 30px;
}

.service-info h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    margin-top: 30px;
}

.service-info p {
    color: #666;
    line-height: 1.8;
    margin-bottom: 20px;
}

.service-features ul,
.service-benefits ul {
    list-style: none;
    padding: 0;
}

.service-features li,
.service-benefits li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    position: relative;
    padding-left: 25px;
}

.service-features li:before,
.service-benefits li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #667eea;
    font-weight: bold;
}

.service-details {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    margin-top: 30px;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.detail-item i {
    color: #667eea;
    font-size: 1.2rem;
    margin-right: 10px;
    width: 20px;
}

.service-sidebar {
    position: sticky;
    top: 100px;
}

.price-box {
    background: #667eea;
    color: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

.price-box h3 {
    color: white;
    margin-bottom: 20px;
}

.price-range {
    margin-bottom: 15px;
}

.price-from,
.price-to {
    display: block;
    font-size: 1.5rem;
    font-weight: 700;
}

.price-note {
    font-size: 0.9rem;
    opacity: 0.8;
    margin: 0;
}

.contact-cta {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

.contact-cta h3 {
    color: #333;
    margin-bottom: 15px;
}

.contact-cta p {
    color: #666;
    margin-bottom: 20px;
}

.btn-block {
    width: 100%;
    margin-bottom: 10px;
}

.related-services {
    background: white;
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 20px;
}

.related-services h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 1.2rem;
}

.related-service-item {
    display: flex;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.related-service-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.related-service-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    margin-right: 15px;
    flex-shrink: 0;
}

.related-service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-service-content h4 {
    font-size: 1rem;
    margin-bottom: 8px;
}

.related-service-content h4 a {
    color: #333;
    text-decoration: none;
}

.related-service-content h4 a:hover {
    color: #667eea;
}

.related-service-content p {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .service-info h1 {
        font-size: 2rem;
    }
    
    .service-image img {
        height: 250px;
    }
    
    .service-sidebar {
        position: static;
        margin-top: 40px;
    }
}

/* Estilos para breadcrumbs */
.breadcrumb {
    background: none;
    padding: 0;
    margin: 0;
    list-style: none;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

.breadcrumb li {
    display: flex;
    align-items: center;
}

.breadcrumb li:not(:last-child):after {
    content: ">";
    margin: 0 10px;
    color: #667eea;
    font-weight: bold;
}

.breadcrumb li a {
    color: #667eea;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb li a:hover {
    color: #333;
}

.breadcrumb li.active {
    color: #333;
    font-weight: 500;
}
</style>

<?= $this->endSection() ?>

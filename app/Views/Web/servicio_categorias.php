<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section id="page-header" class="no-bottom page-content" data-bgimage="url(<?= base_url('lib/images/slider/construction2.jpg') ?>)">
    <div class="mask">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="spacer-single"></div>
                    <div class="spacer-single"></div>
                    <h1 class="text-center text-white">Categorías de Servicios</h1>
                    <p class="text-center text-white lead">Explora nuestros servicios organizados por especialidad</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categorías Grid -->
<section id="section-categories" class="no-top">
    <div class="container">
        <div class="row">
            <?php if (empty($categorias)): ?>
                <div class="col-lg-12 text-center">
                    <div class="spacer-single"></div>
                    <h3>No hay categorías disponibles</h3>
                    <p>Próximamente agregaremos más categorías de servicios.</p>
                </div>
            <?php else: ?>
                <?php foreach ($categorias as $categoria): ?>
                    <div class="col-lg-4 col-md-6 mb30">
                        <div class="category-card">
                            <div class="category-icon">
                                <i class="<?= esc($categoria->icono) ?>" style="color: <?= esc($categoria->color) ?>"></i>
                            </div>
                            <div class="category-content">
                                <h4><?= esc($categoria->nombre) ?></h4>
                                <p><?= esc($categoria->descripcion) ?></p>
                                <div class="category-stats">
                                    <span class="service-count">
                                        <i class="fa fa-cog"></i> 
                                        <?= count($servicios_por_categoria[$categoria->id] ?? []) ?> servicios
                                    </span>
                                </div>
                                <div class="spacer-10"></div>
                                <a href="<?= base_url('servicios-categorias/' . $categoria->slug) ?>" class="btn-custom btn-sm">Ver Servicios</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Call to Action -->
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="spacer-single"></div>
                <h2>¿No encuentras lo que buscas?</h2>
                <p class="lead">Contáctanos y te ayudaremos a encontrar la solución perfecta para tu proyecto</p>
                <div class="spacer-10"></div>
                <a href="<?= base_url('contacto') ?>" class="btn-custom">Contactar</a>
                <a href="<?= base_url('servicios') ?>" class="btn-custom btn-outline">Ver Todos los Servicios</a>
            </div>
        </div>
    </div>
</section>

<style>
.category-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.category-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.category-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.category-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.category-content h4 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 22px;
}

.category-content p {
    color: #666;
    margin-bottom: 20px;
    flex: 1;
}

.category-stats {
    margin-bottom: 20px;
}

.service-count {
    background: #f8f9fa;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    color: #666;
}

.service-count i {
    margin-right: 5px;
    color: var(--primary-color);
}

.btn-sm {
    padding: 8px 20px;
    font-size: 14px;
}
</style>

<?= $this->endSection() ?>

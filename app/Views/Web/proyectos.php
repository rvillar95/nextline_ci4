<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section id="page-header" class="no-bottom page-content" data-bgimage="url(<?= base_url('lib/images/slider/construction3.jpg') ?>)">
    <div class="mask">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="spacer-single"></div>
                    <div class="spacer-single"></div>
                    <h1 class="text-center text-white">Nuestros Proyectos</h1>
                    <p class="text-center text-white lead">Conoce algunos de nuestros trabajos más destacados</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Estadísticas -->
<section id="section-stats" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-6 mb30">
                <div class="de_count text-center">
                    <h3 class="timer" data-to="<?= $estadisticas['total'] ?>" data-speed="2000">0</h3>
                    <h4>Proyectos Completados</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb30">
                <div class="de_count text-center">
                    <h3 class="timer" data-to="<?= $estadisticas['completados'] ?>" data-speed="2000">0</h3>
                    <h4>Obras Terminadas</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb30">
                <div class="de_count text-center">
                    <h3 class="timer" data-to="<?= $estadisticas['en_progreso'] ?>" data-speed="2000">0</h3>
                    <h4>En Construcción</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 mb30">
                <div class="de_count text-center">
                    <h3 class="timer" data-to="<?= $estadisticas['destacados'] ?>" data-speed="2000">0</h3>
                    <h4>Proyectos Destacados</h4>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filtros -->
<section id="section-filters" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <div class="spacer-single"></div>
                    <h2>Explora Nuestros Proyectos</h2>
                    <p>Filtra por tipo de proyecto o busca por nombre, ubicación o descripción</p>
                </div>
                
                <!-- Formulario de filtros -->
                <form method="GET" action="<?= base_url('proyectos') ?>" class="row g-3 justify-content-center">
                    <div class="col-md-4">
                        <select name="tipo" class="form-select">
                            <?php foreach ($tipos_proyecto as $key => $value): ?>
                                <option value="<?= $key ?>" <?= $tipo_actual === $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="busqueda" class="form-control" placeholder="Buscar proyectos..." value="<?= esc($busqueda_actual) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-custom">Filtrar</button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= base_url('proyectos') ?>" class="btn-custom btn-outline">Limpiar</a>
                    </div>
                </form>
                
                <div class="spacer-single"></div>
            </div>
        </div>
    </div>
</section>

<!-- Proyectos Grid -->
<section id="section-projects" class="no-top">
    <div class="container">
        <div class="row">
            <?php if (empty($proyectos)): ?>
                <div class="col-lg-12 text-center">
                    <div class="spacer-single"></div>
                    <h3>No se encontraron proyectos</h3>
                    <p>Intenta con otros filtros o términos de búsqueda.</p>
                    <a href="<?= base_url('proyectos') ?>" class="btn-custom">Ver Todos los Proyectos</a>
                </div>
            <?php else: ?>
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="col-lg-4 col-md-6 mb30">
                        <div class="de-item">
                            <div class="d-overlay">
                                <div class="d-label">
                                    <?= ucfirst($proyecto->tipo_proyecto) ?>
                                    <?php if ($proyecto->destacado): ?>
                                        <span class="badge badge-warning ms-2">Destacado</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-text">
                                    <h4><?= esc($proyecto->nombre) ?></h4>
                                    <p><?= esc($proyecto->descripcion_corta) ?></p>
                                    <div class="d-label-bottom">
                                        <span class="d-label-price">
                                            <?php if ($proyecto->ubicacion): ?>
                                                <i class="fa fa-map-marker"></i> <?= esc($proyecto->ubicacion) ?>
                                            <?php endif; ?>
                                        </span>
                                        <span class="d-label-price">
                                            <?php if ($proyecto->area_construida): ?>
                                                <i class="fa fa-home"></i> <?= $proyecto->area_construida ?> m²
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($proyecto->imagen_portada): ?>
                                <img src="<?= base_url($proyecto->imagen_portada->ruta) ?>" class="img-fluid" alt="<?= esc($proyecto->nombre) ?>">
                            <?php else: ?>
                                <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" class="img-fluid" alt="Proyecto sin imagen">
                            <?php endif; ?>
                            <div class="d-overlay-link">
                                <a href="<?= base_url('proyectos/' . $proyecto->slug) ?>" class="btn-custom">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Paginación si es necesaria -->
        <?php if (count($proyectos) >= 12): ?>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="spacer-single"></div>
                    <button class="btn-custom" id="load-more">Cargar Más Proyectos</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section id="section-cta" class="no-top">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <div class="spacer-single"></div>
                    <h2>¿Tienes un Proyecto en Mente?</h2>
                    <p class="lead">Contáctanos y hagamos realidad tu proyecto de construcción</p>
                    <div class="spacer-10"></div>
                    <a href="<?= base_url('contacto') ?>" class="btn-custom">Solicitar Cotización</a>
                    <a href="<?= base_url('servicios') ?>" class="btn-custom btn-outline">Ver Servicios</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.de-item {
    position: relative;
    overflow: hidden;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.de-item:hover {
    transform: translateY(-5px);
}

.de-item img {
    width: 100%;
    height: 250px;
    object-fit: cover;
}

.d-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.8) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
}

.de-item:hover .d-overlay {
    opacity: 1;
}

.d-label {
    background: var(--primary-color);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    align-self: flex-start;
}

.d-text h4 {
    color: white;
    font-size: 18px;
    margin-bottom: 10px;
}

.d-text p {
    color: rgba(255,255,255,0.9);
    font-size: 14px;
    margin-bottom: 15px;
}

.d-label-bottom {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.d-label-price {
    color: rgba(255,255,255,0.8);
    font-size: 12px;
}

.d-overlay-link {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.de-item:hover .d-overlay-link {
    opacity: 1;
}

.badge {
    font-size: 10px;
    padding: 3px 8px;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}
</style>

<?= $this->endSection() ?>
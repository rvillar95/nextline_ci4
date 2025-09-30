<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-projects-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-projects"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-projects text-white">
                    <div class="hero-badge-projects">
                        <i class="fas fa-building"></i>
                        <span>PROYECTOS</span>
                    </div>
                    <h1 class="hero-title-projects mb-4">Nuestros Proyectos</h1>
                    <p class="hero-subtitle-projects lead mb-4">
                        Descubre nuestra cartera de proyectos exitosos. Cada obra representa nuestro compromiso 
                        con la excelencia y la satisfacción del cliente.
                    </p>
                    <div class="hero-stats-projects mb-4">
                        <div class="stat-item-projects">
                            <div class="stat-number-projects"><?= $estadisticas['total'] ?? 0 ?>+</div>
                            <div class="stat-label-projects">Proyectos Completados</div>
                        </div>
                        <div class="stat-item-projects">
                            <div class="stat-number-projects"><?= $estadisticas['destacados'] ?? 0 ?>+</div>
                            <div class="stat-label-projects">Proyectos Destacados</div>
                        </div>
                        <div class="stat-item-projects">
                            <div class="stat-number-projects">100%</div>
                            <div class="stat-label-projects">Satisfacción Garantizada</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Filtros Modernos -->
<section class="filters-section-modern" style="background: #f8f9fa; padding: 60px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="filters-header-modern text-center mb-5">
                    <h2 class="filters-title-modern mb-3">Explora por Categoría</h2>
                    <p class="filters-subtitle-modern">Filtra nuestros proyectos por tipo de construcción</p>
                </div>
                
                <!-- Botones de filtro modernos -->
                <div class="filter-buttons-modern">
                    <a href="<?= base_url('proyectos') ?>" class="btn-filter-modern <?= empty($categoria_actual) ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i>
                        <span>Todos</span>
                    </a>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $cat): ?>
                            <a href="<?= base_url('proyectos?categoria=' . urlencode($cat->nombre)) ?>" 
                               class="btn-filter-modern <?= $categoria_actual === $cat->nombre ? 'active' : '' ?>">
                                <?php if (!empty($cat->icono)): ?>
                                    <i class="<?= esc($cat->icono) ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-cog"></i>
                                <?php endif; ?>
                                <span><?= esc($cat->nombre) ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Proyectos Grid Moderno -->
<section class="projects-grid-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row">
            <?php if (empty($proyectos)): ?>
                <div class="col-lg-12 text-center">
                    <div class="empty-projects-modern">
                        <div class="empty-icon-modern">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="empty-title-modern">No se encontraron proyectos</h3>
                        <p class="empty-subtitle-modern">Intenta con otros filtros o términos de búsqueda.</p>
                        <a href="<?= base_url('proyectos') ?>" class="btn-empty-modern">
                            <i class="fas fa-refresh"></i> Ver Todos los Proyectos
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($proyectos as $proyecto): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="project-card-modern">
                            <div class="project-image-modern">
                                <?php if ($proyecto->imagen_portada): ?>
                                    <img src="<?= base_url($proyecto->imagen_portada->ruta) ?>" alt="<?= esc($proyecto->nombre) ?>">
                                <?php else: ?>
                                    <img src="<?= base_url('lib/images/placeholder-project.jpg') ?>" alt="Proyecto sin imagen">
                                <?php endif; ?>
                                
                                <!-- Overlay con información -->
                                <div class="project-overlay-modern">
                                    <div class="project-badges-modern">
                                        <span class="badge-type-modern"><?= ucfirst($proyecto->tipo_proyecto) ?></span>
                                        <?php if ($proyecto->destacado): ?>
                                            <span class="badge-featured-modern">
                                                <i class="fas fa-star"></i> Destacado
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="project-actions-modern">
                                        <a href="<?= base_url('proyectos/' . $proyecto->slug) ?>" class="btn-project-modern text-white">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="project-content-modern">
                                <h3 class="project-title-modern"><?= esc($proyecto->nombre) ?></h3>
                                <p class="project-description-modern"><?= esc($proyecto->descripcion_corta) ?></p>
                                
                                <div class="project-meta-modern">
                                    <?php if ($proyecto->ubicacion): ?>
                                        <div class="meta-item-modern">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?= esc($proyecto->ubicacion) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($proyecto->area_construida): ?>
                                        <div class="meta-item-modern">
                                            <i class="fas fa-home"></i>
                                            <span><?= $proyecto->area_construida ?> m²</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($proyecto->fecha_finalizacion): ?>
                                        <div class="meta-item-modern">
                                            <i class="fas fa-calendar"></i>
                                            <span><?= date('Y', strtotime($proyecto->fecha_finalizacion)) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Paginación moderna -->
        <?php if (count($proyectos) >= 12): ?>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="load-more-section-modern">
                        <button class="btn-load-more-modern" id="load-more">
                            <i class="fas fa-plus"></i> Cargar Más Proyectos
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action Moderno -->
<section class="cta-projects-modern" style="background: linear-gradient(135deg, #f0841a 0%, #ff6b35 100%); padding: 80px 0; position: relative; overflow: hidden;">
    <div class="cta-pattern-projects"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <div class="cta-content-projects text-white">
                    <div class="cta-icon-projects">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h2 class="cta-title-projects mb-4">¿Tienes un Proyecto en Mente?</h2>
                    <p class="cta-subtitle-projects lead mb-5">
                        Contáctanos y hagamos realidad tu proyecto de construcción. 
                        Nuestro equipo de expertos está listo para ayudarte.
                    </p>
                    <div class="cta-actions-projects">
                        <a href="<?= base_url('contacto') ?>" class="btn-cta-primary-projects">
                            <i class="fas fa-calculator"></i> Solicitar Cotización
                        </a>
                        <a href="<?= base_url('servicios') ?>" class="btn-cta-secondary-projects">
                            <i class="fas fa-tools"></i> Ver Servicios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== PROYECTOS MODERNOS CSS ===== */

/* Hero Section Proyectos */
.hero-projects-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-projects {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-badge-projects {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(240, 132, 26, 0.2);
    color: #f0841a;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(240, 132, 26, 0.3);
}

.hero-title-projects {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-projects {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
    line-height: 1.6;
}

.hero-stats-projects {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 40px;
}

.stat-item-projects {
    text-align: center;
}

.stat-number-projects {
    font-size: 2.5rem;
    font-weight: 800;
    color: #f0841a;
    margin-bottom: 5px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label-projects {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Filtros Modernos */
.filters-title-modern {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
}

.filters-subtitle-modern {
    font-size: 1.1rem;
    color: #666;
    font-weight: 400;
}

.filter-buttons-modern {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    max-width: 1200px;
    margin: 0 auto;
}

.btn-filter-modern {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px 25px;
    background: white;
    border: 2px solid #e9ecef;
    color: #666;
    text-decoration: none;
    border-radius: 15px;
    transition: all 0.3s ease;
    font-weight: 600;
    font-size: 0.95rem;
    min-width: 120px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.btn-filter-modern i {
    font-size: 1.5rem;
    color: #1d2844;
    transition: all 0.3s ease;
}

.btn-filter-modern span {
    font-size: 0.9rem;
    font-weight: 600;
}

.btn-filter-modern:hover {
    background: #1d2844;
    border-color: #1d2844;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(29, 40, 68, 0.2);
    text-decoration: none;
}

.btn-filter-modern:hover i {
    color: #f0841a;
}

.btn-filter-modern.active {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    border-color: #1d2844;
    color: white;
    box-shadow: 0 8px 25px rgba(29, 40, 68, 0.3);
}

.btn-filter-modern.active i {
    color: #f0841a;
}

/* Tarjetas de Proyectos Modernas */
.project-card-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.project-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.project-image-modern {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.project-image-modern img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.project-card-modern:hover .project-image-modern img {
    transform: scale(1.05);
}

.project-overlay-modern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 20px;
}

.project-card-modern:hover .project-overlay-modern {
    opacity: 1;
}

.project-badges-modern {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
}

.badge-type-modern {
    background: rgba(240, 132, 26, 0.9);
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-featured-modern {
    background: rgba(255, 193, 7, 0.9);
    color: #000;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}

.project-actions-modern {
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-project-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.btn-project-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 132, 26, 0.4);
    color: white !important;
    text-decoration: none;
}

.project-content-modern {
    padding: 25px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.project-title-modern {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 12px;
    line-height: 1.3;
}

.project-description-modern {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 20px;
    flex: 1;
}

.project-meta-modern {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.meta-item-modern {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 0.9rem;
}

.meta-item-modern i {
    color: #f0841a;
    font-size: 0.9rem;
    width: 16px;
}

/* Estado Vacío */
.empty-projects-modern {
    padding: 80px 20px;
    text-align: center;
}

.empty-icon-modern {
    font-size: 4rem;
    color: #ccc;
    margin-bottom: 20px;
}

.empty-title-modern {
    font-size: 1.8rem;
    font-weight: 700;
    color: #666;
    margin-bottom: 10px;
}

.empty-subtitle-modern {
    color: #999;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.btn-empty-modern {
    background: linear-gradient(135deg, #1d2844, #2c3e50);
    color: white;
    padding: 15px 30px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-empty-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(29, 40, 68, 0.3);
    color: white;
    text-decoration: none;
}

/* Cargar Más */
.load-more-section-modern {
    margin-top: 60px;
}

.btn-load-more-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    padding: 18px 40px;
    border: none;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
}

.btn-load-more-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
}

/* Call to Action Proyectos */
.cta-projects-modern {
    position: relative;
    overflow: hidden;
}

.cta-pattern-projects {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="cta-pattern" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23cta-pattern)"/></svg>');
    opacity: 0.2;
}

.cta-icon-projects {
    font-size: 3rem;
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 20px;
}

.cta-title-projects {
    font-size: 2.8rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.cta-subtitle-projects {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 400;
    line-height: 1.6;
}

.cta-actions-projects {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.btn-cta-primary-projects {
    background: white;
    color: #f0841a;
    padding: 18px 35px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
}

.btn-cta-primary-projects:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(255, 255, 255, 0.3);
    color: #f0841a;
    text-decoration: none;
}

.btn-cta-secondary-projects {
    background: rgba(255, 255, 255, 0.1);
    color: white !important;
    padding: 18px 35px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 30px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.btn-cta-secondary-projects:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-3px);
    color: white !important;
    text-decoration: none;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .hero-title-projects {
        font-size: 3rem;
    }
    
    .hero-stats-projects {
        gap: 30px;
    }
    
    .stat-number-projects {
        font-size: 2.2rem;
    }
}

@media (max-width: 992px) {
    .hero-stats-projects {
        flex-direction: column;
        gap: 20px;
    }
    
    .filter-buttons-modern {
        gap: 12px;
    }
    
    .btn-filter-modern {
        min-width: 100px;
        padding: 15px 20px;
    }
}

@media (max-width: 768px) {
    .hero-title-projects {
        font-size: 2.5rem;
    }
    
    .hero-subtitle-projects {
        font-size: 1.1rem;
    }
    
    .filters-title-modern {
        font-size: 2rem;
    }
    
    .cta-title-projects {
        font-size: 2.2rem;
    }
    
    .cta-actions-projects {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-cta-primary-projects,
    .btn-cta-secondary-projects {
        width: 100%;
        max-width: 300px;
        justify-content: center;
    }
    
    .project-image-modern {
        height: 200px;
    }
    
    .project-content-modern {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .hero-title-projects {
        font-size: 2rem;
    }
    
    .filter-buttons-modern {
        gap: 8px;
    }
    
    .btn-filter-modern {
        min-width: 80px;
        padding: 12px 15px;
    }
    
    .btn-filter-modern i {
        font-size: 1.2rem;
    }
    
    .btn-filter-modern span {
        font-size: 0.8rem;
    }
}
</style>

<?= $this->endSection() ?>
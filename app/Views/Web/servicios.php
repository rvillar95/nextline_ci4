<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<section class="page-header page-content">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center">
                    <h1>Nuestros Servicios</h1>
                    <div class="small-border"></div>
                    <p>Ofrecemos una amplia gama de servicios de construcción profesional para satisfacer todas tus necesidades</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <!-- Filter Buttons -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <div class="filter-buttons">
                    <button class="btn-filter active" data-filter="all">Todos</button>
                    <button class="btn-filter" data-filter="residencial">Residencial</button>
                    <button class="btn-filter" data-filter="comercial">Comercial</button>
                    <button class="btn-filter" data-filter="remodelacion">Remodelación</button>
                    <button class="btn-filter" data-filter="ampliacion">Ampliación</button>
                </div>
            </div>
        </div>

        <!-- Services Grid -->
        <div class="row" id="services-grid">
            <?php if (!empty($servicios)): ?>
                <?php foreach ($servicios as $servicio): ?>
                    <?php 
                    // Mapear categorías a valores de filtro
                    $categoria = $servicio->categoria_nombre ?? $servicio->categoria ?? 'general';
                    $filterValue = 'general';
                    
                    if (stripos($categoria, 'residencial') !== false) {
                        $filterValue = 'residencial';
                    } elseif (stripos($categoria, 'comercial') !== false) {
                        $filterValue = 'comercial';
                    } elseif (stripos($categoria, 'remodelación') !== false || stripos($categoria, 'remodelacion') !== false) {
                        $filterValue = 'remodelacion';
                    } elseif (stripos($categoria, 'ampliación') !== false || stripos($categoria, 'ampliacion') !== false) {
                        $filterValue = 'ampliacion';
                    }
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4 service-item" data-category="<?= $filterValue ?>">
                        <div class="service-card">
                            <div class="service-image">
                                <img src="<?= base_url($servicio->foto) ?>" alt="<?= esc($servicio->nombre) ?>" class="img-fluid">
                                <div class="service-overlay">
                                    <div class="service-icon">
                                        <i class="<?= $servicio->icono ?? 'icofont-home' ?>"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="service-content">
                                <div class="service-category"><?= esc($servicio->categoria_nombre ?? $servicio->categoria ?? 'General') ?></div>
                                <h3><?= esc($servicio->nombre) ?></h3>
                                <p><?= esc($servicio->descripcionCorta) ?></p>
                                
                                <?php if (!empty($servicio->caracteristicas)): ?>
                                    <div class="service-features">
                                        <h5>Características:</h5>
                                        <ul>
                                            <?php 
                                            $caracteristicas = explode("\n", $servicio->caracteristicas);
                                            foreach (array_slice($caracteristicas, 0, 3) as $caracteristica): 
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

                                <div class="service-footer">
                                    <div class="service-price">
                                        <?php if ($servicio->mostrar_precio === 'S' && ($servicio->precio_desde || $servicio->precio_hasta)): ?>
                                            <?php if ($servicio->precio_desde && $servicio->precio_hasta): ?>
                                                <span class="price-range">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?> - $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                            <?php elseif ($servicio->precio_desde): ?>
                                                <span class="price-range">Desde $<?= number_format($servicio->precio_desde, 0, ',', '.') ?></span>
                                            <?php elseif ($servicio->precio_hasta): ?>
                                                <span class="price-range">Hasta $<?= number_format($servicio->precio_hasta, 0, ',', '.') ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="price-consult">Consultar precio</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="service-actions">
                                        <a href="<?= base_url('servicios/' . ($servicio->slug ?? 'servicio-' . $servicio->id)) ?>" class="btn-service">Ver Detalles</a>
                                        <a href="<?= base_url('contacto?servicio=' . $servicio->id) ?>" class="btn-quote">Cotizar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <div class="no-services">
                        <i class="icofont-tools fa-3x mb-3"></i>
                        <h3>Próximamente</h3>
                        <p>Estamos preparando nuestros servicios para mostrarte. Muy pronto podrás ver toda nuestra oferta.</p>
                        <a href="<?= base_url('contacto') ?>" class="btn-custom">Contáctanos</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <div class="cta-section">
                    <h3>¿No encuentras lo que buscas?</h3>
                    <p>Contáctanos y te ayudaremos a encontrar la solución perfecta para tu proyecto</p>
                    <a href="<?= base_url('contacto') ?>" class="btn-custom btn-large">Solicitar Consulta</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 100px 0 80px;
    margin-top: 0;
}

.page-header h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.filter-buttons {
    margin-bottom: 2rem;
}

.btn-filter {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
    padding: 10px 20px;
    margin: 0 5px;
    border-radius: 25px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-filter:hover,
.btn-filter.active {
    background: #667eea;
    color: white;
}

.service-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.service-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.service-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.service-card:hover .service-image img {
    transform: scale(1.1);
}

.service-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(102, 126, 234, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.service-card:hover .service-overlay {
    opacity: 1;
}

.service-icon {
    color: white;
    font-size: 3rem;
}

.service-content {
    padding: 30px;
}

.service-category {
    color: #667eea;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.service-content h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
}

.service-content p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 20px;
}

.service-features {
    margin-bottom: 20px;
}

.service-features h5 {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 10px;
}

.service-features ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.service-features li {
    color: #666;
    padding: 5px 0;
    position: relative;
    padding-left: 20px;
}

.service-features li:before {
    content: "✓";
    position: absolute;
    left: 0;
    color: #667eea;
    font-weight: bold;
}

.service-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.service-price {
    font-weight: 600;
}

.price-range,
.price {
    color: #667eea;
    font-size: 1.1rem;
}

.price-consult {
    color: #f39c12;
    font-size: 1.1rem;
    font-weight: 600;
    font-style: italic;
}

.service-actions {
    display: flex;
    gap: 10px;
}

.btn-service,
.btn-quote {
    padding: 8px 16px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-service {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
}

.btn-service:hover {
    background: #667eea;
    color: white;
}

.btn-quote {
    background: #667eea;
    color: white;
    border: 2px solid #667eea;
}

.btn-quote:hover {
    background: transparent;
    color: #667eea;
}

.no-services {
    padding: 60px 20px;
    color: #666;
}

.cta-section {
    background: #f8f9fa;
    padding: 60px 40px;
    border-radius: 15px;
    margin-top: 40px;
}

.cta-section h3 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #333;
}

.cta-section p {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 30px;
}

.btn-large {
    padding: 15px 40px;
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .service-footer {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .service-actions {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.btn-filter');
    const serviceItems = document.querySelectorAll('.service-item');
    
    console.log('Filter buttons found:', filterButtons.length);
    console.log('Service items found:', serviceItems.length);
    
    filterButtons.forEach(button => {
        console.log('Button:', button.textContent, 'Filter:', button.getAttribute('data-filter'));
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            console.log('Filter clicked:', filter);
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter services
            serviceItems.forEach(item => {
                const category = item.getAttribute('data-category');
                console.log('Item category:', category, 'Filter:', filter);
                
                if (filter === 'all' || category === filter) {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.5s ease';
                    console.log('Showing item');
                } else {
                    item.style.display = 'none';
                    console.log('Hiding item');
                }
            });
        });
    });
});

// Add fadeIn animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);
</script>

<?= $this->endSection() ?>

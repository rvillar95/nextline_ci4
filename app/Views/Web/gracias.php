<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section Moderno -->
<section class="hero-gracias-modern" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%); padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="hero-pattern-gracias"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="hero-content-gracias text-white">
                    <div class="hero-badge-gracias">
                        <i class="fas fa-check-circle"></i>
                        <span>MENSAJE ENVIADO</span>
                    </div>
                    <h1 class="hero-title-gracias mb-4">¡Gracias por Contactarnos!</h1>
                    <p class="hero-subtitle-gracias lead mb-4">
                        Hemos recibido tu mensaje y nos pondremos en contacto contigo pronto.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Message Moderno -->
<section class="success-message-modern" style="background: white; padding: 80px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="success-content-modern text-center">
                    <div class="success-icon-modern mb-5">
                        <div class="success-circle-modern">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    
                    <h2 class="success-title-modern mb-4">Mensaje Enviado Exitosamente</h2>
                    
                    <p class="success-description-modern mb-5">
                        Gracias por tu interés en nuestros servicios. Hemos recibido tu consulta y 
                        nuestro equipo se pondrá en contacto contigo en las próximas 24 horas.
                    </p>
                    
                    <div class="info-cards-modern mb-5">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="info-card-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h4 class="info-title-modern">Tiempo de Respuesta</h4>
                                    <p class="info-text-modern">Máximo 24 horas</p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="info-card-modern">
                                    <div class="info-icon-modern">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <h4 class="info-title-modern">Contacto Directo</h4>
                                    <p class="info-text-modern">+56 9 1234 5678</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons-modern">
                        <a href="<?= base_url() ?>" class="btn-action-modern btn-primary-modern">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                        <a href="<?= base_url('servicios') ?>" class="btn-action-modern btn-secondary-modern">
                            <i class="fas fa-tools"></i> Ver Servicios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* ===== GRACIAS MODERNOS CSS ===== */

/* Hero Section */
.hero-gracias-modern {
    position: relative;
    overflow: hidden;
}

.hero-pattern-gracias {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain-gracias" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(240,132,26,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(240,132,26,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(240,132,26,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain-gracias)"/></svg>');
    opacity: 0.3;
}

.hero-badge-gracias {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    padding: 8px 20px;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 20px;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.hero-title-gracias {
    font-size: 3rem;
    font-weight: 800;
    color: white;
    margin-bottom: 20px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle-gracias {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 30px;
    line-height: 1.6;
}

/* Success Content */
.success-content-modern {
    position: relative;
}

.success-icon-modern {
    animation: successBounce 1.5s ease-in-out;
}

.success-circle-modern {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    position: relative;
    overflow: hidden;
}

.success-circle-modern::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    animation: rotate 3s linear infinite;
}

.success-circle-modern i {
    font-size: 3rem;
    color: white;
    position: relative;
    z-index: 2;
}

.success-title-modern {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 20px;
}

.success-description-modern {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto;
}

/* Info Cards */
.info-cards-modern {
    margin-top: 50px;
}

.info-card-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
    text-align: center;
}

.info-card-modern:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.info-icon-modern {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 5px 15px rgba(240, 132, 26, 0.3);
}

.info-icon-modern i {
    font-size: 1.8rem;
    color: white;
}

.info-title-modern {
    font-size: 1.3rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 15px;
}

.info-text-modern {
    font-size: 1.1rem;
    color: #666;
    margin: 0;
    font-weight: 500;
}

/* Action Buttons */
.action-buttons-modern {
    margin-top: 50px;
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-action-modern {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 15px 30px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    min-width: 200px;
    justify-content: center;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white !important;
    box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);
}

.btn-primary-modern:hover {
    transform: translateY(-3px);
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
    transform: translateY(-3px);
    text-decoration: none;
    box-shadow: 0 8px 25px rgba(29, 40, 68, 0.3);
}

/* Animations */
@keyframes successBounce {
    0% { 
        transform: scale(0.3); 
        opacity: 0; 
    }
    50% { 
        transform: scale(1.1); 
    }
    70% { 
        transform: scale(0.9); 
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
    }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title-gracias {
        font-size: 2.2rem;
    }
    
    .success-title-modern {
        font-size: 2rem;
    }
    
    .success-circle-modern {
        width: 100px;
        height: 100px;
    }
    
    .success-circle-modern i {
        font-size: 2.5rem;
    }
    
    .info-card-modern {
        padding: 25px;
    }
    
    .info-icon-modern {
        width: 60px;
        height: 60px;
    }
    
    .info-icon-modern i {
        font-size: 1.5rem;
    }
    
    .action-buttons-modern {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-action-modern {
        width: 100%;
        max-width: 300px;
    }
}

@media (max-width: 576px) {
    .hero-title-gracias {
        font-size: 1.8rem;
    }
    
    .success-title-modern {
        font-size: 1.6rem;
    }
    
    .success-description-modern {
        font-size: 1rem;
    }
    
    .info-card-modern {
        padding: 20px;
    }
    
    .info-title-modern {
        font-size: 1.1rem;
    }
    
    .info-text-modern {
        font-size: 1rem;
    }
}
</style>

<?= $this->endSection() ?>

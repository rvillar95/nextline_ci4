<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(to bottom, #1d2844 0%, #4a5f7a 100%) !important; padding: 100px 0 80px; margin-top: 0; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <h1 class="hero-title mb-4">Contáctanos</h1>
                    <p class="hero-subtitle lead mb-4">¿Tienes un proyecto en mente? Estamos aquí para ayudarte a hacerlo realidad.</p>
                    <div class="hero-features">
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3"></i>
                            <span>Consulta gratuita</span>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3"></i>
                            <span>Respuesta en 24 horas</span>
                        </div>
                        <div class="feature-item d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-warning me-3"></i>
                            <span>Cotización personalizada</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <i class="fas fa-envelope-open-text" style="font-size: 8rem; color: rgba(240, 132, 26, 0.3);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="contact-form-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="contact-form-container">
                    <div class="form-header text-center mb-5">
                        <h2 class="form-title">Envíanos tu Consulta</h2>
                        <p class="form-subtitle text-muted">Completa el formulario y nos pondremos en contacto contigo</p>
                        <div class="title-divider mx-auto"></div>
                    </div>

                    <?php if ($msg = session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-modern">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= esc($msg) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($err = session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-modern">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php if (is_array($err)): ?>
                                <ul class="mb-0"><?php foreach ($err as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                            <?php else: ?><?= esc($err) ?><?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('contacto/enviar') ?>" class="contact-form">
                        <?= csrf_field() ?>
                        <!-- honeypot anti-bot -->
                        <input type="text" name="company" value="" style="position:absolute;left:-9999px;height:1px;width:1px;opacity:0;" tabindex="-1" autocomplete="off">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-user"></i> Nombre Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="nombre" class="form-control-modern" required value="<?= old('nombre') ?>" placeholder="Ingresa tu nombre completo">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-envelope"></i> Correo Electrónico <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="correo" class="form-control-modern" required value="<?= old('correo') ?>" placeholder="tu@email.com">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-phone"></i> Teléfono <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="telefono" class="form-control-modern" required value="<?= old('telefono') ?>" placeholder="+56 9 1234 5678">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-tools"></i> Servicio de Interés <span class="text-danger">*</span>
                                    </label>
                                    <select name="servicio_id" class="form-control-modern" required>
                                        <option value="">Selecciona un servicio...</option>
                                        <?php if (!empty($servicios)): foreach ($servicios as $s): ?>
                                            <option value="<?= (int)$s['id'] ?>" <?= old('servicio_id') == $s['id'] ? 'selected' : '' ?>>
                                                <?= esc($s['nombre']) ?>
                                            </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                    <small class="form-help">Selecciona el servicio que más te interesa</small>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">
                                        <i class="fas fa-comment-dots"></i> Mensaje <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="mensaje" class="form-control-modern" rows="6" required placeholder="Cuéntanos sobre tu proyecto, necesidades específicas, presupuesto estimado, fechas importantes..."><?= old('mensaje') ?></textarea>
                                    <small class="form-help">Mientras más detalles nos proporciones, mejor podremos ayudarte (mínimo 10 caracteres)</small>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <!-- Campo oculto para reCAPTCHA v3 -->
                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                
                                <div class="form-submit-section text-center">
                                    <button type="submit" class="btn-submit-modern" id="submit-btn">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        Enviar Consulta
                                    </button>
                                    <p class="submit-note mt-3">
                                        <i class="fas fa-shield-alt text-success me-1"></i>
                                        Tus datos están seguros y no serán compartidos con terceros
                                    </p>
                                    <p class="submit-note mt-2 text-muted small">
                                        <i class="fas fa-lock me-1"></i>
                                        Protegido por reCAPTCHA de Google
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Section -->
<section class="contact-info-section" style="padding: 80px 0; background: #ffffff;">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h4>Teléfono</h4>
                        <p>+56 9 1234 5678</p>
                        <small>Lunes a Viernes: 8:00 - 18:00</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h4>Email</h4>
                        <p>contacto@mansanchez.cl</p>
                        <small>Respuesta en 24 horas</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="contact-info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Ubicación</h4>
                        <p>Linares, Región del Maule</p>
                        <small>Servicio en toda la región</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Estilos modernos para formulario de contacto */

.hero-section {
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.9;
    line-height: 1.6;
}

.hero-features .feature-item {
    font-size: 1.1rem;
    font-weight: 500;
}

.contact-form-container {
    background: #ffffff;
    border-radius: 25px;
    padding: 50px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.form-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1d2844;
    margin-bottom: 1rem;
}

.form-subtitle {
    font-size: 1.2rem;
    margin-bottom: 2rem;
}

.title-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 2px;
}

.form-group-modern {
    position: relative;
    margin-bottom: 2rem;
}

.form-label-modern {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #1d2844;
    margin-bottom: 0.5rem;
    font-size: 1rem;
}

.form-label-modern i {
    margin-right: 8px;
    color: #f0841a;
    width: 16px;
}

.form-control-modern {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #ffffff;
    color: #333;
}

.form-control-modern:focus {
    outline: none;
    border-color: #f0841a;
    box-shadow: 0 0 0 3px rgba(240, 132, 26, 0.1);
    transform: translateY(-1px);
}

.form-control-modern::placeholder {
    color: #adb5bd;
    font-style: italic;
}

.form-help {
    display: block;
    margin-top: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
    font-style: italic;
}

.btn-submit-modern {
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    color: white;
    border: none;
    padding: 18px 40px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3);
    position: relative;
    overflow: hidden;
}

.btn-submit-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240, 132, 26, 0.4);
    color: white;
}

.btn-submit-modern:active {
    transform: translateY(-1px);
}

.submit-note {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

.contact-info-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 30px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
}

.contact-info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.info-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #f0841a, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: white;
    font-size: 1.8rem;
}

.info-content h4 {
    color: #1d2844;
    font-weight: 700;
    margin-bottom: 10px;
}

.info-content p {
    color: #333;
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.info-content small {
    color: #6c757d;
    font-style: italic;
}

.alert-modern {
    border: none;
    border-radius: 15px;
    padding: 20px 25px;
    font-weight: 500;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .contact-form-container {
        padding: 30px 20px;
        margin: 0 15px;
    }
    
    .form-title {
        font-size: 2rem;
    }
    
    .btn-submit-modern {
        padding: 15px 30px;
        font-size: 1rem;
    }
}
</style>

<!-- Google reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=6LfzneMrAAAAALCg8CWYl0aAdXgfKActSs6qip2_"></script>
<script>
    // Configurar reCAPTCHA v3
    grecaptcha.ready(function() {
        // Generar token al enviar el formulario
        document.querySelector('.contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submit-btn');
            const originalText = submitBtn.innerHTML;
            
            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';
            
            // Obtener token de reCAPTCHA
            grecaptcha.execute('6LfzneMrAAAAALCg8CWYl0aAdXgfKActSs6qip2_', {action: 'submit'})
                .then(function(token) {
                    // Insertar token en el campo oculto
                    document.getElementById('g-recaptcha-response').value = token;
                    
                    // Cambiar texto del botón
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
                    
                    // Enviar formulario
                    form.submit();
                })
                .catch(function(error) {
                    console.error('Error reCAPTCHA:', error);
                    
                    // Restaurar botón
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    // Mostrar error
                    alert('Error en la verificación de seguridad. Por favor, recarga la página e intenta nuevamente.');
                });
        });
    });
</script>

<?= $this->endSection() ?>

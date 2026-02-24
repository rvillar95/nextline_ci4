<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero NutriSync -->
<section class="nutrisync-hero" style="background: linear-gradient(135deg, var(--nutrisync-primary) 0%, var(--nutrisync-muted) 100%); padding: 100px 0 80px; position: relative; overflow: hidden;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge-ns mb-3">
                    <i class="fas fa-leaf"></i>
                    <span>Plataforma para nutricionistas</span>
                </div>
                <h1 class="hero-title-ns text-white mb-4" style="font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 800; line-height: 1.2;">
                    Gestiona tu consulta en un solo lugar
                </h1>
                <p class="hero-subtitle-ns text-white mb-4" style="font-size: 1.15rem; opacity: 0.95; max-width: 520px;">
                    Agenda de citas, historiales clínicos, planes alimentarios, pagos y recordatorios por WhatsApp. Todo integrado para que te enfoques en tus pacientes.
                </p>
                <div class="hero-actions-ns d-flex flex-wrap gap-3">
                    <a href="<?= base_url('reservar') ?>" class="btn-hero-primary">
                        <i class="fas fa-calendar-check me-2"></i> Reservar hora
                    </a>
                    <a href="<?= base_url('login') ?>" class="btn-hero-secondary">
                        <i class="fas fa-sign-in-alt me-2"></i> Iniciar sesión
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <div class="hero-visual-ns">
                    <i class="fas fa-clipboard-list" style="font-size: 10rem; color: rgba(255,255,255,0.25);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Funcionalidades del sistema -->
<section id="funcionalidades" class="py-5" style="background: var(--nutrisync-bg); padding: 80px 0 !important;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="d-inline-block mb-2" style="color: var(--nutrisync-primary); font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">El sistema</span>
            <h2 class="mb-3" style="color: #2d3748; font-weight: 800; font-size: clamp(1.75rem, 4vw, 2.5rem);">Todo lo que necesitas en una sola plataforma</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">Funcionalidades pensadas para consultas nutricionales: desde la primera cita hasta el seguimiento y los pagos.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">Agenda de citas</h3>
                        <p class="text-muted mb-0 small">Gestiona horarios, disponibilidad y citas presenciales u online. Sincronización con Google y Microsoft Calendar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">Pacientes</h3>
                        <p class="text-muted mb-0 small">Ficha del paciente, datos de contacto, historial de consultas y documentos en un solo lugar.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">Historial clínico</h3>
                        <p class="text-muted mb-0 small">Registro de consultas, mediciones, notas clínicas y evolución. Comparativa entre fechas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">Plan alimentario</h3>
                        <p class="text-muted mb-0 small">Planes por porciones, intercambios y calorimetría. Generación de documentos para el paciente.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">Pagos</h3>
                        <p class="text-muted mb-0 small">Integración con Mercado Pago. Cobra consultas y planes desde la plataforma con link de pago.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card card-feature-ns h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="feature-icon-ns mb-3">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2" style="color: #2d3748;">WhatsApp y recordatorios</h3>
                        <p class="text-muted mb-0 small">Confirmación y cancelación de citas por WhatsApp. Recordatorios automáticos configurable.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5" style="background: linear-gradient(135deg, var(--nutrisync-primary) 0%, var(--nutrisync-muted) 100%); padding: 70px 0 !important;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <h2 class="mb-2 fw-bold" style="font-size: clamp(1.5rem, 3.5vw, 2rem);">¿Listo para simplificar tu consulta?</h2>
                <p class="mb-0 opacity-90">Reserva una hora como paciente o contacta para conocer planes para tu consultorio.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="<?= base_url('reservar') ?>" class="btn-cta-ns me-2 mb-2">
                    <i class="fas fa-calendar-check me-2"></i> Reservar
                </a>
                <a href="<?= base_url('contacto') ?>" class="btn-cta-outline-ns mb-2">
                    <i class="fas fa-envelope me-2"></i> Contacto
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* NutriSync landing */
.nutrisync-hero { position: relative; }
.hero-badge-ns {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 8px 18px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
}
.hero-badge-ns i { font-size: 1rem; }
/* Enlaces con aspecto de botón, sin .btn para evitar ripple */
a.btn-hero-primary,
a.btn-hero-secondary {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
a.btn-hero-primary {
    background: #fafeff;
    color: var(--nutrisync-primary);
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 700;
    border: none;
    transition: background 0.2s, color 0.2s, transform 0.2s, box-shadow 0.2s;
}
a.btn-hero-primary:hover {
    background: var(--nutrisync-muted);
    color: var(--nutrisync-primary);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    text-decoration: none;
}
a.btn-hero-secondary {
    background: transparent;
    color: #fff;
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 700;
    border: 2px solid rgba(255,255,255,0.8);
    transition: background 0.2s, color 0.2s, border-color 0.2s, transform 0.2s;
}
a.btn-hero-secondary:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border-color: #fff;
    transform: translateY(-2px);
    text-decoration: none;
}
.card-feature-ns {
    border-radius: 16px;
    transition: all 0.3s ease;
    background: #fff;
}
.card-feature-ns:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(77, 203, 165, 0.15) !important;
    border: 1px solid var(--nutrisync-muted);
}
.feature-icon-ns {
    width: 52px;
    height: 52px;
    background: var(--nutrisync-muted);
    color: var(--nutrisync-primary);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
}
a.btn-cta-ns,
a.btn-cta-outline-ns {
    cursor: pointer;
}
.btn-cta-ns {
    background: #fafeff;
    color: var(--nutrisync-primary);
    padding: 14px 24px;
    border-radius: 50px;
    font-weight: 700;
    border: none;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-cta-ns:hover {
    background: var(--nutrisync-muted);
    color: var(--nutrisync-primary);
    transform: translateY(-2px);
    text-decoration: none;
}
.btn-cta-outline-ns {
    background: transparent;
    color: #fafeff;
    padding: 14px 24px;
    border-radius: 50px;
    font-weight: 700;
    border: 2px solid rgba(255,255,255,0.8);
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}
.btn-cta-outline-ns:hover {
    background: rgba(255,255,255,0.2);
    color: #fff;
    border-color: #fff;
    transform: translateY(-2px);
    text-decoration: none;
}
</style>

<?= $this->endSection() ?>

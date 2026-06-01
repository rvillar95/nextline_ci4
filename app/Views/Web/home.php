<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<!-- Hero NutriNext -->
<section class="nutrinext-hero" style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%); padding: 100px 0 80px; position: relative; overflow: hidden;">
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

<!-- Funcionalidades de la plataforma -->
<section id="funcionalidades" class="py-5" style="background: var(--nutrinext-bg); padding: 80px 0 !important;">
    <div class="container">
        <div class="section-header-ns mb-5">
            <p class="section-eyebrow-ns">Funcionalidades</p>
            <h2 class="section-title-ns">Todo lo que necesitas en una sola plataforma</h2>
            <p class="section-lead-ns text-muted">Pensado para consultas nutricionales: desde la primera cita hasta el seguimiento y los pagos.</p>
        </div>

        <div class="text-center mb-4">
            <a href="<?= base_url('funcionalidades') ?>" class="btn btn-outline-success btn-sm">Ver todas las funcionalidades</a>
        </div>
        <link href="<?= base_url('lib/css/nutrinext-funcionalidades.css') ?>" rel="stylesheet" type="text/css" />
        <div class="func-cards-grid">
            <?php if (! empty($funcionalidades_home)): ?>
                <?php foreach ($funcionalidades_home as $s): ?>
                    <?= view('Web/partials/card_funcionalidad', ['s' => $s]) ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted mb-0">Las funcionalidades se mostrarán aquí cuando estén visibles en la web.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5" style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%); padding: 70px 0 !important;">
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
                <a href="<?= base_url('login') ?>" class="btn-cta-outline-ns mb-2">
                    <i class="fas fa-sign-in-alt me-2"></i> Iniciar sesión
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* NutriNext landing */
.nutrinext-hero { position: relative; }
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
.section-header-ns {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100%;
}
.section-eyebrow-ns {
    display: inline-block;
    margin: 0 0 1rem;
    padding: 6px 16px;
    background: rgba(var(--nutrinext-primary-rgb), 0.12);
    color: var(--nutrinext-primary);
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    line-height: 1.4;
}
.section-title-ns {
    margin: 0 0 1rem;
    color: var(--text-main);
    font-weight: 800;
    font-size: clamp(1.75rem, 4vw, 2.5rem);
    max-width: 720px;
}
.section-lead-ns {
    margin: 0;
    max-width: 600px;
    font-size: 1.05rem;
    line-height: 1.6;
}
/* Enlaces con aspecto de botón, sin .btn para evitar ripple */
a.btn-hero-primary,
a.btn-hero-secondary {
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
}
a.btn-hero-primary {
    background: #fff;
    color: var(--brand-green-primary);
    padding: 14px 28px;
    border-radius: 50px;
    font-weight: 700;
    border: none;
    transition: background 0.2s, color 0.2s, transform 0.2s, box-shadow 0.2s;
}
a.btn-hero-primary:hover {
    background: var(--surface-green-soft);
    color: var(--brand-green-dark);
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
    background: var(--bg-card);
    border: 1px solid var(--border-light);
}
.card-feature-ns:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 40px rgba(34, 197, 94, 0.12) !important;
    border: 1px solid var(--border-light);
}
.feature-icon-ns {
    width: 52px;
    height: 52px;
    background: var(--surface-green-soft);
    color: var(--brand-green-primary);
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
    background: #fff;
    color: var(--brand-green-primary);
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
    background: var(--surface-green-soft);
    color: var(--brand-green-dark);
    transform: translateY(-2px);
    text-decoration: none;
}
.btn-cta-outline-ns {
    background: transparent;
    color: #fff;
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

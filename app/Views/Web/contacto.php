<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<link href="<?= base_url('lib/css/nutrinext-contacto.css') ?>" rel="stylesheet" type="text/css">

<section class="nutrinext-hero" style="background: linear-gradient(135deg, var(--brand-green-primary) 0%, var(--brand-green-dark) 100%); padding: 100px 0 70px;">
    <div class="container text-center text-white">
        <p class="section-eyebrow-ns mb-2">NutriNext</p>
        <h1 class="hero-title-ns mb-3" style="font-weight: 800;">Hablemos de tu consulta</h1>
        <p class="mb-0 mx-auto" style="max-width: 640px; opacity: 0.95;">
            Solicita una demo, cotización o información sobre nuestros planes para nutricionistas en Chile.
        </p>
    </div>
</section>

<section class="contacto-page-section page-content" style="padding-top: 3rem !important;">
    <div class="container">
        <div class="row contacto-layout g-4">
            <div class="col-lg-7">
                <div class="contacto-form-card">
                    <p class="section-eyebrow-ns mb-1">Formulario</p>
                    <h2 class="section-title-ns">Envíanos tu consulta</h2>
                    <p class="text-muted mb-4">Te respondemos en un plazo máximo de 24 horas hábiles.</p>

                    <div id="contacto-alertas">
                    <?php if ($msg = session()->getFlashdata('success')): ?>
                        <div class="alert contacto-alert contacto-alert-success mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?= esc($msg) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($errMsg = session()->getFlashdata('error')): ?>
                        <div class="alert contacto-alert contacto-alert-danger mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?= esc($errMsg) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($err = session()->getFlashdata('errors')): ?>
                        <div class="alert contacto-alert contacto-alert-danger mb-4" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php if (is_array($err)): ?>
                                <ul class="mb-0 ps-3"><?php foreach ($err as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                            <?php else: ?><?= esc($err) ?><?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div id="contacto-alert-js" class="alert contacto-alert contacto-alert-danger mb-4 d-none" role="alert"></div>
                    </div>

                    <form method="post" action="<?= base_url('contacto/enviar') ?>" class="contact-form" id="formContactoWeb">
                        <?= csrf_field() ?>
                        <input type="text" name="company" value="" tabindex="-1" autocomplete="off"
                               style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;" aria-hidden="true">

                        <?php if (!empty($recaptcha_enabled)): ?>
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="contacto-field">
                                    <label for="nombre"><i class="fas fa-user"></i> Nombre completo <span class="text-danger">*</span></label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" required
                                           value="<?= esc(old('nombre')) ?>" placeholder="Tu nombre">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contacto-field">
                                    <label for="correo"><i class="fas fa-envelope"></i> Correo <span class="text-danger">*</span></label>
                                    <input type="email" name="correo" id="correo" class="form-control" required
                                           value="<?= esc(old('correo')) ?>" placeholder="tu@correo.cl">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contacto-field">
                                    <label for="telefono"><i class="fas fa-phone"></i> Teléfono <span class="text-danger">*</span></label>
                                    <input type="tel" name="telefono" id="telefono" class="form-control" required
                                           value="<?= esc(old('telefono')) ?>" placeholder="+56 9 1234 5678">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="contacto-field">
                                    <label for="plan_interes"><i class="fas fa-layer-group"></i> Plan de interés <span class="text-danger">*</span></label>
                                    <select name="plan_interes" id="plan_interes" class="form-select" required>
                                        <option value="">Selecciona un plan...</option>
                                        <?php
                                        $planOld = old('plan_interes', $plan_seleccionado ?? '');
                                        foreach ($planes ?? [] as $plan):
                                            $slug = (string) ($plan->slug ?? '');
                                            $precio = (float) ($plan->precio_mensual ?? 0);
                                            $esClinica = ($slug === 'nutri-clinica');
                                            $selected = ($planOld === $slug) ? 'selected' : '';
                                        ?>
                                        <option value="<?= esc($slug) ?>" <?= $selected ?> class="contacto-plan-option">
                                            <?= esc($plan->nombre ?? '') ?>
                                            <?php if ($esClinica): ?>
                                                — Cotización
                                            <?php elseif ($precio > 0): ?>
                                                — $<?= number_format($precio, 0, ',', '.') ?>/mes
                                            <?php endif; ?>
                                        </option>
                                        <?php endforeach; ?>
                                        <option value="otro" <?= $planOld === 'otro' ? 'selected' : '' ?>>Otro / Consulta general</option>
                                    </select>
                                    <span class="form-help">Puedes elegir un plan NutriNext, sitio web o una consulta general.</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="contacto-field">
                                    <label for="mensaje"><i class="fas fa-comment-dots"></i> Mensaje <span class="text-danger">*</span></label>
                                    <textarea name="mensaje" id="mensaje" class="form-control" rows="5" required
                                              placeholder="Cuéntanos si buscas demo, cantidad de pacientes, add-ons de antropometría, etc."><?= esc(old('mensaje')) ?></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg px-4" id="submit-btn">
                                    <i class="fas fa-paper-plane me-2"></i> Enviar consulta
                                </button>
                                <p class="form-help mt-3 mb-0">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Tus datos se usan solo para responder tu solicitud.
                                    <?php if (!empty($recaptcha_enabled)): ?>
                                        Protegido con reCAPTCHA.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="contacto-aside">
                    <div class="contacto-info-card">
                        <div class="contacto-info-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <h3>Teléfono</h3>
                            <p><?= esc($empresa_contacto['telefono'] ?? '') ?></p>
                            <small>Lunes a viernes, horario laboral</small>
                        </div>
                    </div>
                    <div class="contacto-info-card">
                        <div class="contacto-info-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h3>Correo</h3>
                            <p><a href="mailto:<?= esc($empresa_contacto['email'] ?? '') ?>" class="text-decoration-none"><?= esc($empresa_contacto['email'] ?? '') ?></a></p>
                            <small>Respuesta en 24 h hábiles</small>
                        </div>
                    </div>
                    <div class="contacto-info-card">
                        <div class="contacto-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h3>Ubicación</h3>
                            <p><?= esc($empresa_contacto['direccion'] ?? 'Chile') ?></p>
                            <small>Atención 100% online disponible</small>
                        </div>
                    </div>
                    <div class="contacto-benefits">
                        <h3><i class="fas fa-leaf me-2"></i>¿Por qué NutriNext?</h3>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Agenda, plan alimentario y ficha clínica integrados</li>
                            <li><i class="fas fa-check-circle"></i> Reserva web y recordatorios para tus pacientes</li>
                            <li><i class="fas fa-check-circle"></i> Planes desde consulta individual hasta clínica</li>
                            <li><i class="fas fa-check-circle"></i> <a href="<?= base_url('precios') ?>" class="text-white fw-semibold">Ver planes y precios</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($recaptcha_enabled) && !empty($recaptcha_site_key)): ?>
<?php
$recaptchaScript = !empty($recaptcha_enterprise)
    ? 'https://www.google.com/recaptcha/enterprise.js?render=' . esc($recaptcha_site_key)
    : 'https://www.google.com/recaptcha/api.js?render=' . esc($recaptcha_site_key);
$recaptchaAction = esc($recaptcha_action ?? 'contacto');
?>
<script src="<?= $recaptchaScript ?>"></script>
<script>
(function() {
    var siteKey = <?= json_encode($recaptcha_site_key) ?>;
    var action = <?= json_encode($recaptcha_action ?? 'contacto') ?>;
    var isEnterprise = <?= !empty($recaptcha_enterprise) ? 'true' : 'false' ?>;
    var form = document.getElementById('formContactoWeb');
    if (!form) return;

    var alertJs = document.getElementById('contacto-alert-js');
    var alertBox = document.getElementById('contacto-alertas');

    function showFormError(msg) {
        if (alertJs) {
            alertJs.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>' + msg;
            alertJs.classList.remove('d-none');
        }
        if (alertBox) {
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function runCaptcha() {
        return new Promise(function(resolve, reject) {
            var timeout = setTimeout(function() {
                reject(new Error('Tiempo de espera agotado'));
            }, 12000);

            function done(token) {
                clearTimeout(timeout);
                resolve(token);
            }
            function fail(err) {
                clearTimeout(timeout);
                reject(err);
            }

            if (isEnterprise) {
                if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
                    fail(new Error('reCAPTCHA Enterprise no cargó'));
                    return;
                }
                grecaptcha.enterprise.ready(async function() {
                    try {
                        done(await grecaptcha.enterprise.execute(siteKey, { action: action }));
                    } catch (err) {
                        fail(err);
                    }
                });
                return;
            }
            if (typeof grecaptcha === 'undefined') {
                fail(new Error('reCAPTCHA no cargó'));
                return;
            }
            grecaptcha.ready(function() {
                grecaptcha.execute(siteKey, { action: action }).then(done).catch(fail);
            });
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('submit-btn');
        var original = btn.innerHTML;
        if (alertJs) {
            alertJs.classList.add('d-none');
            alertJs.innerHTML = '';
        }
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Verificando...';

        runCaptcha()
            .then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Enviando...';
                form.submit();
            })
            .catch(function() {
                btn.disabled = false;
                btn.innerHTML = original;
                showFormError('No se pudo verificar reCAPTCHA. Recarga la página e intenta de nuevo.');
            });
    });

    if (alertBox && (alertBox.querySelector('.contacto-alert-success') || alertBox.querySelector('.contacto-alert-danger:not(.d-none)'))) {
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
})();
</script>
<?php endif; ?>

<?= $this->endSection() ?>

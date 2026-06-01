<?php
$empresaNombre = esc($empresa['nombre'] ?? 'NutriNext');
$empresaEmail  = esc($empresa['email'] ?? 'contacto@nutrinext.cl');
$empresaTel    = esc($empresa['telefono'] ?? '');
$empresaDir    = esc($empresa['direccion'] ?? 'Chile');
$fechaActual   = 'Mayo 2026';
?>
<?php $this->extend('layout/web') ?>

<?= $this->section('content') ?>

<?= view('Web/partials/legal_page_styles') ?>

<section class="legal-hero-ns nutrinext-hero">
    <div class="container text-center text-white">
        <p class="section-eyebrow-ns mb-2">Legal</p>
        <h1 class="hero-title-ns mb-2" style="font-weight: 800;">Política de Privacidad</h1>
        <p class="mb-0 opacity-90">Última actualización: <?= $fechaActual ?></p>
    </div>
</section>

<section class="legal-body-ns page-content" style="padding-top: 2rem !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="legal-card-ns">
                    <p>La presente Política de Privacidad describe cómo <strong><?= $empresaNombre ?></strong> («nosotros», «el Responsable») trata los datos personales en el sitio web público y en la plataforma <strong>NutriNext</strong>, software de gestión para nutricionistas y sus pacientes en Chile.</p>

                    <h2>1. Responsable del tratamiento</h2>
                    <p>El responsable del tratamiento de los datos es <strong><?= $empresaNombre ?></strong>, con domicilio en <?= $empresaDir ?>. Para consultas sobre privacidad: <a href="mailto:<?= $empresaEmail ?>"><?= $empresaEmail ?></a><?= $empresaTel ? ' · Teléfono: ' . $empresaTel : '' ?>.</p>

                    <h2>2. Datos que recopilamos</h2>
                    <p>Según el uso del sitio o la plataforma, podemos tratar:</p>
                    <ul>
                        <li><strong>Visitantes del sitio web:</strong> nombre, correo, teléfono, mensaje y plan de interés (formulario de contacto); datos técnicos (IP, navegador, cookies).</li>
                        <li><strong>Pacientes:</strong> identificación (nombre, RUT u otro documento), correo, teléfono, datos de salud y antropometría ingresados por el nutricionista o por el propio paciente al reservar hora.</li>
                        <li><strong>Nutricionistas y usuarios del dashboard:</strong> nombre, correo, credenciales de acceso, datos profesionales, configuración de agenda, planes contratados y facturación.</li>
                        <li><strong>Verificación de seguridad:</strong> tokens de Google reCAPTCHA al enviar formularios públicos.</li>
                        <li><strong>Pagos:</strong> información de transacciones procesada por proveedores de pago (por ejemplo Mercado Pago), sin almacenar datos completos de tarjetas en nuestros servidores.</li>
                    </ul>

                    <h2>3. Finalidades del tratamiento</h2>
                    <ul>
                        <li>Responder consultas comerciales y solicitudes de demo o contratación.</li>
                        <li>Prestar el servicio NutriNext: agenda, consultas, planes alimentarios, historial clínico y comunicaciones con pacientes.</li>
                        <li>Gestionar reservas web, confirmaciones, recordatorios (correo y/o WhatsApp, si están habilitados).</li>
                        <li>Procesar suscripciones y pagos de planes.</li>
                        <li>Mejorar seguridad, prevenir fraude y cumplir obligaciones legales.</li>
                    </ul>

                    <h2>4. Base legal</h2>
                    <p>El tratamiento se fundamenta en el consentimiento del titular, la ejecución de un contrato o medidas precontractuales, el interés legítimo del Responsable en operar y asegurar la plataforma, y el cumplimiento de obligaciones legales aplicables en Chile (incluida la Ley N° 19.628 sobre protección de la vida privada).</p>

                    <h2>5. Datos de salud</h2>
                    <p>La información nutricional y clínica ingresada en NutriNext puede constituir dato sensible. Solo se trata para fines profesionales del nutricionista respecto de su paciente, bajo su responsabilidad profesional, y con medidas de acceso restringido en la plataforma.</p>

                    <h2>6. Cesión y encargados</h2>
                    <p>No vendemos datos personales. Podemos compartirlos con proveedores que nos ayudan a operar el servicio, por ejemplo:</p>
                    <ul>
                        <li>Hosting y correo electrónico</li>
                        <li>Google (reCAPTCHA, Calendar, si el nutricionista lo conecta)</li>
                        <li>Mercado Pago u otros medios de pago</li>
                        <li>Proveedores de mensajería (WhatsApp u otros canales configurados)</li>
                    </ul>
                    <p>Estos proveedores solo tratan datos según nuestras instrucciones y para las finalidades indicadas.</p>

                    <h2>7. Cookies y tecnologías similares</h2>
                    <p>Utilizamos cookies y almacenamiento local necesarios para el funcionamiento del sitio, sesiones de usuario y preferencias. Puede configurar su navegador para limitar cookies; algunas funciones podrían dejar de estar disponibles.</p>

                    <h2>8. Plazo de conservación</h2>
                    <p>Conservamos los datos mientras exista una relación contractual o comercial vigente, mientras el nutricionista mantenga la información en su cuenta, o el tiempo necesario para cumplir obligaciones legales, reclamos o auditorías. Los leads del formulario de contacto se conservan el tiempo necesario para gestionar la solicitud.</p>

                    <h2>9. Seguridad</h2>
                    <p>Aplicamos medidas técnicas y organizativas razonables (acceso autenticado, HTTPS, respaldos, control de permisos). Ningún sistema es 100% invulnerable; en caso de incidente relevante actuaremos conforme a la normativa aplicable.</p>

                    <h2>10. Derechos del titular</h2>
                    <p>Usted puede solicitar acceso, rectificación, actualización, bloqueo o eliminación de sus datos, y revocar su consentimiento cuando corresponda, escribiendo a <a href="mailto:<?= $empresaEmail ?>"><?= $empresaEmail ?></a>. Los pacientes también pueden contactar al nutricionista que trata sus datos. Responderemos en plazos razonables según la ley chilena.</p>

                    <h2>11. Menores de edad</h2>
                    <p>El sitio comercial no está dirigido a menores. El tratamiento de datos de pacientes menores debe realizarse con el consentimiento de quien ejerce la patria potestad o representación legal, conforme indique el profesional responsable.</p>

                    <h2>12. Enlaces a terceros</h2>
                    <p>El sitio puede enlazar a servicios externos (pagos, calendario, redes). No somos responsables de las políticas de privacidad de esos sitios.</p>

                    <h2>13. Cambios</h2>
                    <p>Podemos actualizar esta política. La versión vigente se publicará en esta página con la fecha de actualización.</p>

                    <h2>14. Contacto</h2>
                    <div class="legal-contact-box">
                        <p class="mb-1"><strong><?= $empresaNombre ?></strong></p>
                        <?php if ($empresaDir): ?><p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?= $empresaDir ?></p><?php endif; ?>
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i><a href="mailto:<?= $empresaEmail ?>"><?= $empresaEmail ?></a></p>
                        <?php if ($empresaTel): ?><p class="mb-0"><i class="fas fa-phone me-2"></i><?= $empresaTel ?></p><?php endif; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= base_url('/') ?>" class="btn btn-success"><i class="fas fa-home me-2"></i> Volver al inicio</a>
                        <a href="<?= base_url('terminos-condiciones') ?>" class="btn btn-outline-success ms-2">Términos y condiciones</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

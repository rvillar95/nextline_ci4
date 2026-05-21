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
        <h1 class="hero-title-ns mb-2" style="font-weight: 800;">Términos y Condiciones</h1>
        <p class="mb-0 opacity-90">Última actualización: <?= $fechaActual ?></p>
    </div>
</section>

<section class="legal-body-ns page-content" style="padding-top: 2rem !important;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="legal-card-ns">
                    <p>Los presentes Términos y Condiciones («Términos») regulan el acceso y uso del sitio web y de la plataforma <strong>NutriNext</strong>, operada por <strong><?= $empresaNombre ?></strong> («nosotros», «el Proveedor»). Al utilizar el sitio o contratar un plan, usted acepta estos Términos.</p>

                    <h2>1. Definiciones</h2>
                    <ul>
                        <li><strong>Sitio web:</strong> páginas públicas de NutriNext (información, precios, contacto, reserva de horas, equipo).</li>
                        <li><strong>Plataforma / Dashboard:</strong> sistema en línea para nutricionistas (agenda, pacientes, consultas, planes, pagos configurables).</li>
                        <li><strong>Usuario profesional:</strong> nutricionista o clínica que contrata un plan y accede al dashboard.</li>
                        <li><strong>Paciente / usuario final:</strong> persona que reserva hora o recibe atención a través de un profesional que usa NutriNext.</li>
                    </ul>

                    <h2>2. Objeto del servicio</h2>
                    <p>NutriNext es un software como servicio (SaaS) para la gestión de consultas nutricionales: agenda, ficha de pacientes, antropometría, planes alimentarios, reservas web, notificaciones y herramientas según el plan contratado. No sustituye el criterio profesional del nutricionista ni constituye asesoría médica por parte del Proveedor.</p>

                    <h2>3. Uso del sitio web</h2>
                    <p>El visitante se compromete a:</p>
                    <ul>
                        <li>Proporcionar información veraz en formularios de contacto o reserva.</li>
                        <li>No utilizar el sitio para fines ilícitos, envío de spam, intentos de acceso no autorizado o interferencia con el servicio.</li>
                        <li>No copiar, descompilar o explotar el contenido o código sin autorización.</li>
                    </ul>

                    <h2>4. Contratación de planes</h2>
                    <p>Los precios publicados en <a href="<?= base_url('precios') ?>">/precios</a> son referenciales en pesos chilenos (CLP), salvo cotización personalizada (por ejemplo plan Clínica). La contratación efectiva se confirma por el Proveedor o mediante el flujo comercial acordado. Los planes, límites y módulos (add-ons) son los descritos en el sitio o en la propuesta comercial vigente al momento de la contratación.</p>

                    <h2>5. Cuentas y acceso (profesionales)</h2>
                    <p>El usuario profesional es responsable de la confidencialidad de sus credenciales y de toda actividad en su cuenta. Debe notificar accesos no autorizados. El Proveedor puede suspender cuentas ante incumplimiento, impago o uso abusivo.</p>

                    <h2>6. Reservas y relación con pacientes</h2>
                    <p>Cuando un paciente reserva hora vía web:</p>
                    <ul>
                        <li>La relación profesional es entre el paciente y el nutricionista, no con el Proveedor de software.</li>
                        <li>Confirmaciones, cancelaciones, pagos y asistencia a consultas son responsabilidad del profesional según su política y la ley.</li>
                        <li>El Proveedor facilita herramientas (correo, WhatsApp, calendario) que el profesional puede activar o desactivar.</li>
                    </ul>

                    <h2>7. Pagos</h2>
                    <p>Los pagos de suscripción al Proveedor y los cobros a pacientes (botones de pago, Mercado Pago u otros) se rigen por las condiciones del medio de pago correspondiente. El Proveedor no garantiza la aprobación de transacciones por terceros. Los reembolsos entre profesional y paciente son acordados entre ellos, salvo lo que el Proveedor comunique expresamente por escrito.</p>

                    <h2>8. Disponibilidad y soporte</h2>
                    <p>Procuramos mantener la plataforma disponible, pero pueden existir mantenimientos, actualizaciones o interrupciones por causas técnicas o de fuerza mayor. El soporte se presta por los canales indicados al contratar (correo, horario hábil), según el plan.</p>

                    <h2>9. Propiedad intelectual</h2>
                    <p>El software, marca NutriNext, diseño del sitio, textos y materiales del Proveedor están protegidos por la legislación chilena e internacional. Se concede al cliente una licencia de uso no exclusiva mientras mantenga la suscripción vigente. Los datos ingresados por el profesional (pacientes, planes, etc.) son de su titularidad o de sus pacientes según corresponda; el Proveedor los trata solo para prestar el servicio, conforme a la <a href="<?= base_url('politica-privacidad') ?>">Política de Privacidad</a>.</p>

                    <h2>10. Limitación de responsabilidad</h2>
                    <p>En la medida permitida por la ley, el Proveedor no será responsable por daños indirectos, lucro cesante o decisiones clínicas del profesional. La responsabilidad total del Proveedor por el servicio SaaS se limitará, como máximo, al monto pagado por el cliente en los últimos doce (12) meses por el plan contratado, salvo dolo o culpa grave.</p>

                    <h2>11. Suspensión y terminación</h2>
                    <p>El cliente puede solicitar la baja de su plan según las condiciones comerciales. El Proveedor puede terminar el acceso por impago, violación de estos Términos o requerimiento legal. Tras la terminación, el cliente debe exportar la información que necesite dentro del plazo que se comunique; después podrá aplicarse eliminación conforme a la política de retención.</p>

                    <h2>12. Modificaciones</h2>
                    <p>Podemos modificar estos Términos, precios o funcionalidades. Los cambios relevantes se publicarán en el sitio. El uso continuado tras la publicación implica aceptación, salvo que la ley exija otro procedimiento.</p>

                    <h2>13. Ley aplicable y jurisdicción</h2>
                    <p>Estos Términos se rigen por las leyes de la República de Chile. Cualquier controversia se someterá a los tribunales ordinarios de justicia de Chile, sin perjuicio de mecanismos alternativos que las partes acuerden por escrito.</p>

                    <h2>14. Contacto</h2>
                    <div class="legal-contact-box">
                        <p class="mb-1"><strong><?= $empresaNombre ?></strong></p>
                        <?php if ($empresaDir): ?><p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?= $empresaDir ?></p><?php endif; ?>
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i><a href="mailto:<?= $empresaEmail ?>"><?= $empresaEmail ?></a></p>
                        <?php if ($empresaTel): ?><p class="mb-0"><i class="fas fa-phone me-2"></i><?= $empresaTel ?></p><?php endif; ?>
                    </div>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="<?= base_url('/') ?>" class="btn btn-success"><i class="fas fa-home me-2"></i> Volver al inicio</a>
                        <a href="<?= base_url('politica-privacidad') ?>" class="btn btn-outline-success ms-2">Política de privacidad</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

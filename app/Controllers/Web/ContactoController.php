<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Libraries\RecaptchaEnterpriseService;
use App\Models\LeadModel;
use App\Models\Empresa;
use App\Models\Paquete;
use Config\Services;

final class ContactoController extends BaseController
{
    public function index()
    {
        $empresaModel = new Empresa();
        $empresa = $empresaModel->getEmpresaActiva();
        $empresaData = $empresaModel->getDatosParaPDF();

        $paqueteModel = new Paquete();
        $planes = $paqueteModel->where('activo', 'A')
            ->where('slug !=', 'nutri-partner')
            ->groupStart()
                ->like('slug', 'nutri-', 'after')
                ->orWhereIn('slug', ['presencia', 'gestion'])
            ->groupEnd()
            ->orderBy('orden', 'ASC')
            ->orderBy('precio_mensual', 'ASC')
            ->findAll();

        $planSeleccionado = trim((string) $this->request->getGet('plan'));

        $recaptchaSiteKey = trim((string) env('recaptcha.siteKey', ''));
        $recaptchaEnabled = filter_var(env('recaptcha.enabled', 'true'), FILTER_VALIDATE_BOOLEAN)
            && $recaptchaSiteKey !== '';
        $recaptchaEnterprise = filter_var(env('recaptcha.enterprise', 'false'), FILTER_VALIDATE_BOOLEAN);

        return view('Web/contacto', array_merge(seo_page([
            'title'       => 'Contacto | NutriNext',
            'description' => 'Escríbenos para conocer NutriNext, solicitar una demo o contratar un plan para tu consulta nutricional.',
            'keywords'    => 'contacto nutrinext, demo nutricionista, planes nutrinext chile',
            'canonical'   => seo_canonical_url('contacto'),
        ]), [
            'planes'             => $planes,
            'plan_seleccionado'  => $planSeleccionado,
            'empresa_contacto'   => [
                'telefono'  => $empresaData['telefono'] ?? '+56 9 1234 5678',
                'email'     => $empresaData['email'] ?? 'info@nutrinext.cl',
                'direccion' => $empresaData['direccion'] ?? 'Chile',
            ],
            'recaptcha_enabled'   => $recaptchaEnabled,
            'recaptcha_site_key'  => $recaptchaSiteKey,
            'recaptcha_enterprise'=> $recaptchaEnterprise,
            'recaptcha_action'    => trim((string) env('recaptcha.action', 'contacto')) ?: 'contacto',
        ]));
    }

    public function enviar()
    {
        if (($this->request->getPost('company') ?? '') !== '') {
            return redirect()->to(base_url('/gracias'));
        }

        if (!$this->validarRecaptcha()) {
            return redirect()->to(base_url('contacto'))
                ->withInput()
                ->with('error', 'La verificación de seguridad falló. Por favor, intenta nuevamente.')
                ->with('errors', ['recaptcha' => 'La verificación de seguridad falló. Por favor, intenta nuevamente.']);
        }

        $lead = new LeadModel();
        $db   = db_connect();
        $tieneColumnaPlan = $db->fieldExists('plan_interes', 'lead_contacto');

        if (!$this->validate($lead->getValidationRules(), $lead->getValidationMessages())) {
            return redirect()->to(base_url('contacto'))
                ->withInput()
                ->with('error', 'Revisa los campos marcados e intenta de nuevo.')
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost([
            'nombre', 'correo', 'telefono', 'mensaje', 'plan_interes', 'utm_source', 'utm_medium', 'utm_campaign',
        ]);
        $data['estado_id']   = 1;
        $data['servicio_id'] = null;

        $planSlug = trim((string) ($data['plan_interes'] ?? ''));
        if (!$tieneColumnaPlan) {
            $planLabel = $this->nombrePlanInteres($planSlug);
            $data['mensaje'] = '[Plan de interés: ' . $planLabel . "]\n\n" . ($data['mensaje'] ?? '');
            unset($data['plan_interes']);
        }

        $insertId = $lead->skipValidation(true)->insert($data);
        if ($insertId === false) {
            log_message('error', 'Lead contacto insert falló: ' . json_encode($lead->errors()));
            return redirect()->to(base_url('contacto'))
                ->withInput()
                ->with('error', 'No se pudo guardar tu consulta. Intenta nuevamente.')
                ->with('errors', $lead->errors());
        }

        $dataEmail = $data;
        $dataEmail['plan_interes'] = $planSlug;

        $emailOk = false;
        try {
            $emailOk = $this->enviarNotificacionEmail($dataEmail);
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar email de notificación: ' . $e->getMessage());
        }

        $flash = '¡Gracias! Recibimos tu consulta y te responderemos pronto.';
        if (!$emailOk) {
            $flash .= ' (El aviso por correo no pudo enviarse; tu mensaje sí quedó registrado.)';
            log_message('warning', 'Lead contacto guardado pero email no enviado');
        }

        return redirect()->to(base_url('contacto'))
            ->with('success', $flash);
    }

    /** Destino fijo de notificaciones del formulario web /contacto */
    private const CONTACTO_NOTIFICACION_EMAIL = 'rvillar1995@gmail.com';

    private function enviarNotificacionEmail(array $data): bool
    {
        $planNombre = $this->nombrePlanInteres($data['plan_interes'] ?? null);

        $email = Services::email();
        $email->setFrom(env('email.fromEmail', ''), env('email.fromName', ''));
        $email->setTo(self::CONTACTO_NOTIFICACION_EMAIL);
        $email->setSubject('Nueva consulta web — ' . $planNombre);
        $email->setMessage($this->crearMensajeHTML($data, $planNombre));

        if ($email->send()) {
            log_message('info', 'Email de notificación enviado a: ' . self::CONTACTO_NOTIFICACION_EMAIL);
            return true;
        }

        log_message('error', 'Error al enviar email: ' . $email->printDebugger(['headers']));
        return false;
    }

    private function nombrePlanInteres(?string $slug): string
    {
        if ($slug === null || $slug === '') {
            return 'No especificado';
        }
        if ($slug === 'otro') {
            return 'Otro / Consulta general';
        }

        $paquete = (new Paquete())->where('slug', $slug)->where('activo', 'A')->first();
        return $paquete ? (string) $paquete->nombre : $slug;
    }

    private function crearMensajeHTML(array $data, string $planNombre): string
    {
        $fecha = date('d/m/Y H:i');
        $nombre = esc($data['nombre'] ?? '');
        $correo = esc($data['correo'] ?? '');
        $telefono = esc($data['telefono'] ?? '');
        $mensaje = esc($data['mensaje'] ?? '');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Nueva consulta</title></head>
<body style="margin:0;padding:20px;background:#f8fafc;font-family:Segoe UI,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0"><tr><td align="center">
<table width="600" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.08);">
<tr><td style="background:linear-gradient(135deg,#15803D,#22C55E);padding:28px;text-align:center;">
<h1 style="margin:0;color:#fff;font-size:22px;">Nueva consulta — NutriNext</h1>
<p style="margin:8px 0 0;color:rgba(255,255,255,.9);font-size:14px;">{$fecha}</p>
</td></tr>
<tr><td style="padding:28px;">
<p style="margin:0 0 16px;color:#1f2937;"><strong>Nombre:</strong> {$nombre}</p>
<p style="margin:0 0 16px;color:#1f2937;"><strong>Correo:</strong> <a href="mailto:{$correo}" style="color:#15803D;">{$correo}</a></p>
<p style="margin:0 0 16px;color:#1f2937;"><strong>Teléfono:</strong> {$telefono}</p>
<p style="margin:0 0 16px;color:#1f2937;"><strong>Plan de interés:</strong> {$planNombre}</p>
<p style="margin:0 0 8px;color:#1f2937;font-weight:700;">Mensaje:</p>
<div style="background:#f1f5f9;border-radius:8px;padding:16px;color:#374151;white-space:pre-wrap;">{$mensaje}</div>
</td></tr>
<tr><td style="background:#1f2937;padding:16px;text-align:center;color:rgba(255,255,255,.7);font-size:12px;">
NutriNext — formulario web
</td></tr>
</table>
</td></tr></table>
</body>
</html>
HTML;
    }

    public function gracias()
    {
        return view('Web/gracias', seo_page([
            'title'       => 'Mensaje enviado | NutriNext',
            'description' => 'Gracias por contactarnos. Te responderemos a la brevedad.',
            'robots'      => 'noindex, nofollow',
            'canonical'   => seo_canonical_url('gracias'),
        ]));
    }

    private function validarRecaptcha(): bool
    {
        if (!filter_var(env('recaptcha.enabled', 'true'), FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        $token = trim((string) $this->request->getPost('g-recaptcha-response'));
        if ($token === '') {
            log_message('warning', 'reCAPTCHA: Token no recibido');
            return false;
        }

        $service = new RecaptchaEnterpriseService();
        $userIp  = $this->request->getIPAddress();

        if (filter_var(env('recaptcha.enterprise', 'false'), FILTER_VALIDATE_BOOLEAN)) {
            $enterprise = $service->verify($token, $userIp);
            if ($enterprise !== null) {
                return $enterprise;
            }
        }

        $siteverify = $service->verifySiteverify($token, $userIp);
        if ($siteverify !== null) {
            return $siteverify;
        }

        log_message('info', 'reCAPTCHA: sin credenciales Enterprise ni secretKey — omitiendo validación');

        return true;
    }
}

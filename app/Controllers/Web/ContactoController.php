<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\Empresa;
use Config\Services;

final class ContactoController extends BaseController
{
    public function index()
    {
        // carga de servicios para el select (si ya tienes el modelo Servicio)
        $servicios = db_connect()->table('servicio')->select('id,nombre')->where('estado','A')->get()->getResultArray();
        return view('Web/contacto', ['servicios' => $servicios]);
    }

    public function enviar()
    {
        // Anti-bot simple (honeypot oculto en el form)
        if (($this->request->getPost('company') ?? '') !== '') {
            return redirect()->to(base_url('/gracias'));
        }

        // Validar reCAPTCHA v3
        if (!$this->validarRecaptcha()) {
            return redirect()->back()
                ->withInput()
                ->with('errors', ['recaptcha' => 'La verificación de seguridad falló. Por favor, intenta nuevamente.']);
        }

        $lead = new LeadModel();

        if (! $this->validate($lead->getValidationRules(), $lead->getValidationMessages())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost([
            'nombre','correo','telefono','mensaje','servicio_id','utm_source','utm_medium','utm_campaign'
        ]);
        $data['estado_id'] = 1; // Nuevo

        if (! $lead->insert($data, true)) {
            return redirect()->back()->withInput()->with('errors', $lead->errors());
        }

        // Enviar notificación por email a la empresa
        try {
            $this->enviarNotificacionEmail($data);
        } catch (\Exception $e) {
            log_message('error', 'Error al enviar email de notificación: ' . $e->getMessage());
            // No redirigimos con error, solo logueamos
        }

        return redirect()->to(base_url('/gracias'))->with('success', 'Gracias por contactarnos.');
    }

    /**
     * Enviar notificación por email con diseño moderno
     */
    private function enviarNotificacionEmail(array $data)
    {
        // Obtener correo de la empresa
        $empresaModel = new Empresa();
        $empresa = $empresaModel->getEmpresaActiva();
        
        if (!$empresa || empty($empresa->email)) {
            log_message('warning', 'No se pudo enviar el email: empresa sin correo configurado');
            return false;
        }

        // Obtener nombre del servicio
        $servicio = db_connect()->table('servicio')
            ->select('nombre')
            ->where('id', $data['servicio_id'] ?? 0)
            ->get()
            ->getRow();

        $servicioNombre = $servicio ? $servicio->nombre : 'No especificado';

        // Configurar email manualmente (sin initialize)
        $email = Services::email();
                        
        // Usar la configuración del .env
        $email->setFrom(env('email.fromEmail', ''), env('email.fromName', ''));
        $email->setTo($empresa->email);
        $email->setSubject('🔔 Nueva Consulta desde el Sitio Web');
        
        // Crear el mensaje HTML moderno
        $mensaje = $this->crearMensajeHTML($data, $servicioNombre, $empresa);
        
        $email->setMessage($mensaje);
        
        // Enviar
        if ($email->send()) {
            log_message('info', 'Email de notificación enviado exitosamente a: ' . $empresa->email);
            return true;
        } else {
            log_message('error', 'Error al enviar email: ' . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Crear mensaje HTML moderno para el email
     */
    private function crearMensajeHTML(array $data, string $servicioNombre, object $empresa): string
    {
        $fecha = date('d/m/Y H:i');
        $nombre = esc($data['nombre'] ?? '');
        $correo = esc($data['correo'] ?? '');
        $telefono = esc($data['telefono'] ?? '');
        $mensaje = esc($data['mensaje'] ?? '');
        
        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Consulta</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0; padding: 20px 0;">
        <tr>
            <td align="center">
                <!-- Contenedor Principal -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);">
                    
                    <!-- Header con Gradiente -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1d2844 0%, #4a5f7a 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 800; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                                🔔 Nueva Consulta Recibida
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 16px;">
                                {$fecha}
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Badge de Alerta -->
                    <tr>
                        <td style="padding: 30px 30px 0 30px;">
                            <div style="background: linear-gradient(135deg, #f0841a, #ff6b35); color: white; padding: 15px 25px; border-radius: 12px; text-align: center; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(240, 132, 26, 0.3);">
                                ✨ Se ha recibido una nueva consulta desde el formulario web
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Datos del Cliente -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #1d2844; font-size: 22px; font-weight: 700; border-bottom: 3px solid #f0841a; padding-bottom: 10px;">
                                📋 Datos del Cliente
                            </h2>
                            
                            <!-- Nombre -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 15px; border-left: 4px solid #f0841a;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="padding: 5px 0;">
                                            <div style="display: inline-block; width: 40px; height: 40px; background: linear-gradient(135deg, #f0841a, #ff6b35); border-radius: 50%; text-align: center; line-height: 40px; vertical-align: middle; margin-right: 15px;">
                                                <span style="color: white; font-size: 18px;">👤</span>
                                            </div>
                                            <span style="color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Nombre Completo</span>
                                            <p style="margin: 10px 0 0 55px; color: #1d2844; font-size: 18px; font-weight: 700;">
                                                {$nombre}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Email -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 15px; border-left: 4px solid #1d2844;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="padding: 5px 0;">
                                            <div style="display: inline-block; width: 40px; height: 40px; background: linear-gradient(135deg, #1d2844, #4a5f7a); border-radius: 50%; text-align: center; line-height: 40px; vertical-align: middle; margin-right: 15px;">
                                                <span style="color: white; font-size: 18px;">📧</span>
                                            </div>
                                            <span style="color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Correo Electrónico</span>
                                            <p style="margin: 10px 0 0 55px; color: #1d2844; font-size: 18px; font-weight: 700;">
                                                <a href="mailto:{$correo}" style="color: #f0841a; text-decoration: none;">{$correo}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Teléfono -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 15px; border-left: 4px solid #28a745;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="padding: 5px 0;">
                                            <div style="display: inline-block; width: 40px; height: 40px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; text-align: center; line-height: 40px; vertical-align: middle; margin-right: 15px;">
                                                <span style="color: white; font-size: 18px;">📱</span>
                                            </div>
                                            <span style="color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Teléfono</span>
                                            <p style="margin: 10px 0 0 55px; color: #1d2844; font-size: 18px; font-weight: 700;">
                                                <a href="tel:{$telefono}" style="color: #28a745; text-decoration: none;">{$telefono}</a>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Servicio -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 15px; border-left: 4px solid #6610f2;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="padding: 5px 0;">
                                            <div style="display: inline-block; width: 40px; height: 40px; background: linear-gradient(135deg, #6610f2, #6f42c1); border-radius: 50%; text-align: center; line-height: 40px; vertical-align: middle; margin-right: 15px;">
                                                <span style="color: white; font-size: 18px;">🛠️</span>
                                            </div>
                                            <span style="color: #6c757d; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Servicio de Interés</span>
                                            <p style="margin: 10px 0 0 55px; color: #1d2844; font-size: 18px; font-weight: 700;">
                                                {$servicioNombre}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Mensaje -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #1d2844; font-size: 22px; font-weight: 700; border-bottom: 3px solid #f0841a; padding-bottom: 10px;">
                                💬 Mensaje del Cliente
                            </h2>
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; border-left: 4px solid #f0841a;">
                                <p style="margin: 0; color: #555; font-size: 16px; line-height: 1.8; white-space: pre-wrap;">
                                    {$mensaje}
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Call to Action -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px; text-align: center;">
                            <a href="mailto:{$correo}?subject=Re: Su consulta en MANSANCHEZ" style="display: inline-block; background: linear-gradient(135deg, #f0841a, #ff6b35); color: white; text-decoration: none; padding: 18px 40px; border-radius: 50px; font-weight: 700; font-size: 16px; box-shadow: 0 8px 25px rgba(240, 132, 26, 0.3); text-transform: uppercase; letter-spacing: 0.5px;">
                                📧 Responder al Cliente
                            </a>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #1d2844; padding: 30px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: rgba(255, 255, 255, 0.8); font-size: 14px;">
                                Este correo fue generado automáticamente desde el sitio web
                            </p>
                            <p style="margin: 0; color: rgba(255, 255, 255, 0.6); font-size: 12px;">
                                © 2025 {$empresa->nombre} - Todos los derechos reservados
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    public function gracias()
    {
        return view('Web/gracias');
    }

    /**
     * Validar Google reCAPTCHA v3
     */
    private function validarRecaptcha(): bool
    {
        // Verificar si reCAPTCHA está habilitado desde .env
        if (!filter_var(env('recaptcha.enabled', 'true'), FILTER_VALIDATE_BOOLEAN)) {
            return true; // Si está deshabilitado, permitir el envío
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');

        if (empty($recaptchaResponse)) {
            log_message('warning', 'reCAPTCHA: Token no recibido');
            return false;
        }

        $secretKey = env('recaptcha.secretKey');
        
        if (empty($secretKey)) {
            log_message('error', 'reCAPTCHA: Secret key no configurada en .env');
            return true; // Permitir si no está configurado (para desarrollo)
        }

        // Preparar datos para enviar a Google
        $data = [
            'secret'   => $secretKey,
            'response' => $recaptchaResponse,
            'remoteip' => $this->request->getIPAddress()
        ];

        // Enviar petición a Google
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($verify);
        curl_close($verify);

        if (!$response) {
            log_message('error', 'reCAPTCHA: Error al conectar con Google');
            return true; // Permitir si hay error de conexión
        }

        $responseData = json_decode($response, true);

        // Verificar respuesta
        if (!isset($responseData['success']) || !$responseData['success']) {
            log_message('warning', 'reCAPTCHA: Validación fallida - ' . json_encode($responseData));
            return false;
        }

        // Verificar score (puntuación)
        $minScore = (float) env('recaptcha.minScore', 0.5);
        $score = $responseData['score'] ?? 0;

        log_message('info', "reCAPTCHA: Score obtenido = {$score}, mínimo requerido = {$minScore}");

        if ($score < $minScore) {
            log_message('warning', "reCAPTCHA: Score demasiado bajo ({$score} < {$minScore})");
            return false;
        }

        return true;
    }
}

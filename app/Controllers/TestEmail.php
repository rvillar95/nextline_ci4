<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Services;

class TestEmail extends BaseController
{
    /**
     * Página de prueba de envío de emails
     * Acceder desde: http://localhost/codeigniter4/nextline_ci4/test-email
     */
    public function index()
    {
        // Verificar configuración
        $config = [
            'fromEmail' => env('email.fromEmail', 'NO CONFIGURADO'),
            'fromName' => env('email.fromName', 'NO CONFIGURADO'),
            'SMTPHost' => env('email.SMTPHost', 'NO CONFIGURADO'),
            'SMTPUser' => env('email.SMTPUser', 'NO CONFIGURADO'),
            'SMTPPort' => env('email.SMTPPort', 465),
            'SMTPCrypto' => env('email.SMTPCrypto', 'ssl'),
        ];

        $data = [
            'config' => $config,
            'message' => '',
            'error' => ''
        ];

        // Si hay un email destino en GET, intentar enviar
        $emailDestino = $this->request->getGet('to');
        
        if ($emailDestino) {
            try {
                $email = Services::email();
                
                $email->setFrom($config['fromEmail'], $config['fromName']);
                $email->setTo($emailDestino);
                $email->setSubject('✅ Prueba de Email desde Local - ' . date('Y-m-d H:i:s'));
                
                $mensaje = $this->crearMensajePrueba();
                $email->setMessage($mensaje);
                
                if ($email->send()) {
                    $data['message'] = "✅ Email enviado exitosamente a: {$emailDestino}";
                } else {
                    $data['error'] = "❌ Error al enviar: " . $email->printDebugger(['headers', 'subject', 'body']);
                }
            } catch (\Exception $e) {
                $data['error'] = "❌ Excepción: " . $e->getMessage();
            }
        }

        return view('test_email', $data);
    }

    /**
     * Crear mensaje HTML de prueba
     */
    private function crearMensajePrueba()
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #4A90E2; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                .success { color: #6BCB77; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎉 ¡Email Funcionando!</h1>
                </div>
                <div class="content">
                    <p class="success">✅ Tu configuración de email está funcionando correctamente.</p>
                    <p>Este es un email de prueba enviado desde tu entorno local.</p>
                    <p><strong>Fecha y hora:</strong> ' . date('d-m-Y H:i:s') . '</p>
                    <p><strong>Servidor SMTP:</strong> ' . env('email.SMTPHost', 'N/A') . '</p>
                    <hr>
                    <p style="color: #666; font-size: 12px;">
                        Si recibes este email, significa que tu configuración de SMTP está correcta.
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}

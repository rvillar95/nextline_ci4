<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\LeadModel;
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

        // (Opcional) notificar por email
        // $email = Services::email();
        // $email->setTo('ventas@tuempresa.com')->setSubject('Nuevo lead')->setMessage('...')->send();

        return redirect()->to(base_url('/gracias'))->with('success', 'Gracias por contactarnos.');
    }

    public function gracias()
    {
        return view('Web/gracias');
    }
}

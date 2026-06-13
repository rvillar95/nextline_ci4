<?php

declare(strict_types=1);

namespace App\Controllers\Web\Abopech;

use App\Controllers\BaseController;
use App\Libraries\RecaptchaEnterpriseService;
use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\ContactoModel;
use App\Models\Abopech\UsuarioModel;

final class ContactoController extends BaseController
{
    public function form(int $abogadoId)
    {
        $abogado = (new AbogadoModel())->findPublico($abogadoId);
        if (!$abogado) {
            return redirect()->to(base_url('abopech/buscar'))->with('error', 'Abogado no encontrado.');
        }

        return view('abopech/contactar', $this->recaptchaData() + [
            'abogado' => $abogado,
        ]);
    }

    public function enviar(int $abogadoId)
    {
        if (($this->request->getPost('company') ?? '') !== '') {
            return redirect()->to(base_url('abopech'));
        }

        if (!$this->validarRecaptcha()) {
            return redirect()->back()->withInput()->with('error', 'Verificación de seguridad fallida.');
        }

        $abogado = (new AbogadoModel())->findPublico($abogadoId);
        if (!$abogado) {
            return redirect()->to(base_url('abopech/buscar'))->with('error', 'Abogado no encontrado.');
        }

        $rules = [
            'nombre'   => 'required|min_length[2]|max_length[120]',
            'apellido' => 'required|min_length[2]|max_length[120]',
            'correo'   => 'required|valid_email|max_length[150]',
            'telefono' => 'required|min_length[8]|max_length[30]',
            'consentimiento' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        session()->set('abopech_contacto_pendiente', [
            'abogado_id' => $abogadoId,
            'nombre'     => trim((string) $this->request->getPost('nombre')),
            'apellido'   => trim((string) $this->request->getPost('apellido')),
            'correo'     => trim((string) $this->request->getPost('correo')),
            'telefono'   => trim((string) $this->request->getPost('telefono')),
        ]);

        return view('abopech/contactar_confirmar', [
            'abogado' => $abogado,
            'datos'   => session()->get('abopech_contacto_pendiente'),
        ]);
    }

    public function confirmarPost(int $abogadoId)
    {
        $datos = session()->get('abopech_contacto_pendiente');
        if (!is_array($datos) || (int) ($datos['abogado_id'] ?? 0) !== $abogadoId) {
            return redirect()->to(base_url('abopech/abogado/' . $abogadoId . '/contactar'));
        }

        $abogado = (new AbogadoModel())->findPublico($abogadoId);
        if (!$abogado) {
            return redirect()->to(base_url('abopech/buscar'));
        }

        $usuario = (new UsuarioModel())->find((int) $abogado['usuario_id']);
        if (!$usuario) {
            return redirect()->back()->with('error', 'No se pudo obtener contacto del abogado.');
        }

        $canal = (string) env('ABOPECH_CONTACT_DEFAULT', 'whatsapp');
        $destino = abopech_telefono_e164((string) $usuario['telefono']);

        (new ContactoModel())->insert([
            'abogado_id'           => $abogadoId,
            'nombre'               => $datos['nombre'],
            'apellido'             => $datos['apellido'],
            'correo'               => $datos['correo'],
            'telefono'             => $datos['telefono'],
            'consentimiento_datos' => 1,
            'canal_redireccion'    => $canal,
            'destino_e164'         => $destino,
            'estado_seguimiento'   => 'nuevo',
            'ip_origen'            => $this->request->getIPAddress(),
            'user_agent'           => substr((string) $this->request->getUserAgent(), 0, 400),
        ]);

        session()->remove('abopech_contacto_pendiente');

        $url = $canal === 'telefono'
            ? 'tel:' . $destino
            : abopech_whatsapp_url($destino);

        return redirect()->to($url);
    }

    private function recaptchaData(): array
    {
        $siteKey = trim((string) env('recaptcha.siteKey', ''));
        $enabled = filter_var(env('recaptcha.enabled', 'true'), FILTER_VALIDATE_BOOLEAN) && $siteKey !== '';

        return [
            'recaptcha_enabled'    => $enabled,
            'recaptcha_site_key'   => $siteKey,
            'recaptcha_enterprise' => filter_var(env('recaptcha.enterprise', 'false'), FILTER_VALIDATE_BOOLEAN),
            'recaptcha_action'     => 'abopech_contacto',
        ];
    }

    private function validarRecaptcha(): bool
    {
        if (!filter_var(env('recaptcha.enabled', 'true'), FILTER_VALIDATE_BOOLEAN)) {
            return true;
        }

        $token = trim((string) $this->request->getPost('g-recaptcha-response'));
        if ($token === '') {
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

        return true;
    }
}

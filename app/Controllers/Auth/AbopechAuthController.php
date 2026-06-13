<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\UsuarioModel;
use App\Services\GoogleAuthService;

final class AbopechAuthController extends BaseController
{
    public function login()
    {
        if (abopech_usuario()) {
            return redirect()->to(base_url('abopech/mi-perfil'));
        }

        return view('abopech/auth/login');
    }

    public function loginPost()
    {
        $correo = trim((string) $this->request->getPost('correo'));
        $clave  = (string) $this->request->getPost('clave');

        $model = new UsuarioModel();
        $user  = $model->findByCorreo($correo);

        if (!$user || empty($user['clave']) || !password_verify($clave, (string) $user['clave'])) {
            return redirect()->back()->withInput()->with('error', 'Correo o contraseña incorrectos.');
        }

        return $this->iniciarSesion($user);
    }

    public function registro()
    {
        return view('abopech/auth/registro');
    }

    public function registroPost()
    {
        $rules = [
            'nombre'   => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'correo'   => 'required|valid_email|max_length[150]',
            'telefono' => 'required|min_length[8]|max_length[30]',
            'clave'    => 'required|min_length[8]|max_length[100]',
            'consentimiento' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $correo = trim((string) $this->request->getPost('correo'));
        $model  = new UsuarioModel();
        if ($model->where('correo', $correo)->first()) {
            return redirect()->back()->withInput()->with('error', 'El correo ya está registrado.');
        }

        $userId = $model->insert([
            'correo'                  => $correo,
            'clave'                   => password_hash((string) $this->request->getPost('clave'), PASSWORD_DEFAULT),
            'nombre'                  => trim((string) $this->request->getPost('nombre')),
            'apellido'                => trim((string) $this->request->getPost('apellido')),
            'telefono'                => trim((string) $this->request->getPost('telefono')),
            'tipo_cuenta'             => 'profesional',
            'estado'                  => 'A',
            'consentimiento_contacto' => 1,
            'terminos_aceptados_en'   => date('Y-m-d H:i:s'),
        ], true);

        $user = $model->find((int) $userId);
        $this->ensureAbogadoRow((int) $userId);

        return $this->iniciarSesion($user);
    }

    public function googleRedirect()
    {
        $state = bin2hex(random_bytes(16));
        session()->set('abopech_oauth_state', $state);
        $svc = new GoogleAuthService();

        return redirect()->to($svc->getAuthUrl($state));
    }

    public function googleCallback()
    {
        $code     = (string) $this->request->getGet('code');
        $state    = (string) $this->request->getGet('state');
        $expected = (string) session()->get('abopech_oauth_state');
        session()->remove('abopech_oauth_state');

        if ($code === '' || $state === '' || $expected === '' || !hash_equals($expected, $state)) {
            return redirect()->to(base_url('abopech/auth/login'))->with('error', 'OAuth inválido.');
        }

        try {
            $svc   = new GoogleAuthService();
            $token = $svc->exchangeCodeForToken($code);
            $info  = $svc->getUserInfo($token['access_token']);
        } catch (\Throwable $e) {
            return redirect()->to(base_url('abopech/auth/login'))->with('error', 'No se pudo conectar con Google.');
        }

        $sub   = (string) $info['sub'];
        $email = (string) ($info['email'] ?? '');
        $model = new UsuarioModel();
        $user  = $model->findByGoogleSub($sub);

        if (!$user && $email !== '') {
            $user = $model->findByCorreo($email);
            if ($user) {
                $model->update((int) $user['id'], [
                    'oauth_google_sub'   => $sub,
                    'oauth_google_email' => $email,
                    'oauth_vinculado_en' => date('Y-m-d H:i:s'),
                ]);
                $user = $model->find((int) $user['id']);
            }
        }

        if (!$user) {
            $userId = $model->insert([
                'correo'                  => $email !== '' ? $email : $sub . '@google.oauth',
                'clave'                   => null,
                'nombre'                  => (string) ($info['given_name'] ?? 'Abogado'),
                'apellido'                => (string) ($info['family_name'] ?? ''),
                'telefono'                => '+56900000000',
                'tipo_cuenta'             => 'profesional',
                'estado'                  => 'A',
                'consentimiento_contacto' => 1,
                'terminos_aceptados_en'   => date('Y-m-d H:i:s'),
                'oauth_google_sub'        => $sub,
                'oauth_google_email'      => $email,
                'oauth_vinculado_en'      => date('Y-m-d H:i:s'),
            ], true);
            $user = $model->find((int) $userId);
        }

        $this->ensureAbogadoRow((int) $user['id']);

        return $this->iniciarSesion($user);
    }

    public function logout()
    {
        session()->remove('abopech_usuario');

        return redirect()->to(base_url('abopech'));
    }

    private function iniciarSesion(array $user)
    {
        session()->set('abopech_usuario', [
            'id'           => (int) $user['id'],
            'correo'       => $user['correo'],
            'nombre'       => $user['nombre'],
            'apellido'     => $user['apellido'],
            'telefono'     => $user['telefono'],
            'tipo_cuenta'  => $user['tipo_cuenta'],
        ]);

        if (($user['tipo_cuenta'] ?? '') === 'administrador') {
            return redirect()->to(base_url('abopech/admin'));
        }

        return redirect()->to(base_url('abopech/mi-perfil'));
    }

    private function ensureAbogadoRow(int $usuarioId): void
    {
        $abogadoModel = new AbogadoModel();
        if ($abogadoModel->findByUsuarioId($usuarioId)) {
            return;
        }

        $abogadoModel->insert([
            'usuario_id'     => $usuarioId,
            'rut'            => sprintf('%08d-%s', $usuarioId, 'K'),
            'nombres'        => '',
            'apellidos'      => '',
            'habilidades'    => ' ',
            'experiencia'    => ' ',
            'estado_perfil'  => 'borrador',
        ]);
    }
}

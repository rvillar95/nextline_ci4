<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Services\GoogleAuthService;

class GoogleAuthController extends BaseController
{
    public function redirect()
    {
        $empresaId = (int) ($this->request->getGet('empresa_id') ?? 0);
        $state = bin2hex(random_bytes(16)) . ':' . $empresaId;
        session()->set('google_oauth_state', $state);

        $svc = new GoogleAuthService();
        return redirect()->to($svc->getAuthUrl($state));
    }

    public function callback()
    {
        $error = (string) ($this->request->getGet('error') ?? '');
        if ($error !== '') {
            return redirect()->to(base_url('login'))->with('errors', 'Google OAuth error: ' . $error);
        }

        $code = (string) ($this->request->getGet('code') ?? '');
        $state = (string) ($this->request->getGet('state') ?? '');
        $expected = (string) (session()->get('google_oauth_state') ?? '');
        session()->remove('google_oauth_state');

        if ($code === '' || $state === '' || $expected === '' || !hash_equals($expected, $state)) {
            return redirect()->to(base_url('login'))->with('errors', 'Google OAuth: state inválido');
        }

        $parts = explode(':', $state, 2);
        $empresaId = isset($parts[1]) ? (int) $parts[1] : 0;

        try {
            $svc = new GoogleAuthService();
            $token = $svc->exchangeCodeForToken($code);
            $info = $svc->getUserInfo($token['access_token']);

            $sub = (string) $info['sub'];
            $email = (string) ($info['email'] ?? '');

            // Buscar usuario por sub
            $db = \Config\Database::connect();
            $user = $db->table('usuario u')
                ->select('u.*, f.nombre as perfil_nombre, f.poder')
                ->join('perfil f', 'u.perfil_id = f.id', 'left')
                ->where('u.oauth_google_sub', $sub)
                ->where('u.estado', 'A')
                ->get()
                ->getRowArray();

            // Si no existe, intentar vincular por email si coincide y está activo
            if (!$user && $email !== '') {
                $candidate = $db->table('usuario u')
                    ->select('u.*, f.nombre as perfil_nombre, f.poder')
                    ->join('perfil f', 'u.perfil_id = f.id', 'left')
                    ->where('u.correo', $email)
                    ->where('u.estado', 'A')
                    ->get()
                    ->getRowArray();
                if ($candidate) {
                    $db->table('usuario')->where('id', (int) $candidate['id'])->update([
                        'oauth_google_sub' => $sub,
                        'oauth_google_email' => $email,
                        'oauth_vinculado_en' => date('Y-m-d H:i:s'),
                        'factualizacion' => date('Y-m-d H:i:s'),
                    ]);
                    $user = $candidate;
                    $user['oauth_google_sub'] = $sub;
                }
            }

            // Si aún no existe, crear usuario Alumno (si se entrega empresa_id)
            if (!$user) {
                if ($empresaId <= 0) {
                    return redirect()->to(base_url('login'))->with('errors', 'No se pudo crear usuario (falta empresa_id)');
                }

                $perfilAlumno = $db->table('perfil')->select('id')->where('nombre', 'Alumno')->where('poder', 1)->where('estado', 'A')->get()->getRow();
                $perfilAlumnoId = (int) ($perfilAlumno->id ?? 0);
                if ($perfilAlumnoId <= 0) {
                    return redirect()->to(base_url('login'))->with('errors', 'Perfil Alumno no existe');
                }

                $nombre = (string) ($info['given_name'] ?? ($info['name'] ?? 'Alumno'));
                $apellido = (string) ($info['family_name'] ?? '');
                if ($email === '') {
                    $email = 'google_' . $sub . '@example.invalid';
                }

                // evitar colisión de correo
                $emailExists = $db->table('usuario')->where('correo', $email)->countAllResults() > 0;
                if ($emailExists) {
                    $email = 'google_' . $sub . '+' . time() . '@example.invalid';
                }

                $db->table('usuario')->insert([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'correo' => $email,
                    'telefono' => null,
                    'clave' => null,
                    'perfil_id' => $perfilAlumnoId,
                    'empresa_id' => $empresaId,
                    'estado' => 'A',
                    'oauth_google_sub' => $sub,
                    'oauth_google_email' => $email,
                    'oauth_vinculado_en' => date('Y-m-d H:i:s'),
                    'fcreacion' => date('Y-m-d H:i:s'),
                    'factualizacion' => date('Y-m-d H:i:s'),
                ]);

                $newId = (int) $db->insertID();
                $usuarioModel = new Usuario();
                $user = $usuarioModel->select('usuario.* , perfil.nombre as perfil_nombre, perfil.poder')
                    ->join('perfil', 'usuario.perfil_id = perfil.id', 'left')
                    ->where('usuario.id', $newId)
                    ->first();
            }

            if (!$user) {
                return redirect()->to(base_url('login'))->with('errors', 'No se pudo iniciar sesión con Google');
            }

            // Guardar sesión
            session()->set('usuario', $user);

            // Redirigir por poder
            $poder = (int) ($user['poder'] ?? 0);
            if ($poder <= 1) {
                return redirect()->to(base_url('alumno/inicio'));
            }
            return redirect()->to(base_url('dashboard/menu'))->with('mensaje', 'Bienvenid@');
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth error: ' . $e->getMessage());
            return redirect()->to(base_url('login'))->with('errors', 'Google OAuth: ' . $e->getMessage());
        }
    }
}


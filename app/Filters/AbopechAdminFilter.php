<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AbopechAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('abopech_usuario');
        if (!is_array($usuario) || empty($usuario['id'])) {
            return redirect()->to(base_url('abopech/auth/login'))
                ->with('error', 'Debes iniciar sesión.');
        }

        if (($usuario['tipo_cuenta'] ?? '') !== 'administrador') {
            return redirect()->to(base_url('abopech'))->with('error', 'Se requiere perfil administrador.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

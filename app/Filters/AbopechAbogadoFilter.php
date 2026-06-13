<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class AbopechAbogadoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('abopech_usuario');
        if (!is_array($usuario) || empty($usuario['id'])) {
            return redirect()->to(base_url('abopech/auth/login'))
                ->with('error', 'Debes iniciar sesión como abogado.');
        }

        $tipo = (string) ($usuario['tipo_cuenta'] ?? '');
        if (!in_array($tipo, ['profesional', 'administrador'], true)) {
            return redirect()->to(base_url('abopech'))->with('error', 'Acceso no permitido.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}

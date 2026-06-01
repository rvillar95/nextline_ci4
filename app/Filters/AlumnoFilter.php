<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AlumnoFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $usuario = session()->get('usuario');
        if (!$usuario) {
            return redirect()->to(base_url('login'));
        }

        $poder = (int) ($usuario['poder'] ?? 0);
        if ($poder > 1) {
            return redirect()->to(base_url('dashboard/menu'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}


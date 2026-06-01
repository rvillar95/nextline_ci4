<?php

namespace App\Controllers\Dashboard\Gym;

use App\Controllers\BaseController;
use App\Models\ModuloDetalle;

abstract class BaseGymController extends BaseController
{
    protected function buildMenuData(): array
    {
        $menuTotal = [];
        $modulo = new ModuloDetalle();
        $menu = $modulo->getMenu(session()->get('usuario')['perfil_id']);

        foreach ($menu as $entity) {
            $submenu = $modulo->getSubMenu($entity['id']);
            $menuTotal[] = ["menu" => $entity, "submenu" => $submenu];
        }

        return [
            'menu' => $menu,
            'data' => $menuTotal,
        ];
    }

    protected function requireEmpresaId(): int
    {
        $empresaId = (int) (session()->get('usuario')['empresa_id'] ?? 0);
        if ($empresaId <= 0) {
            throw new \RuntimeException('empresa_id no disponible en sesión');
        }
        return $empresaId;
    }

    protected function requireUsuarioId(): int
    {
        $usuarioId = (int) (session()->get('usuario')['id'] ?? 0);
        if ($usuarioId <= 0) {
            throw new \RuntimeException('usuario.id no disponible en sesión');
        }
        return $usuarioId;
    }
}


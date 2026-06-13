<?php

declare(strict_types=1);

namespace App\Controllers\Web\Abopech;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\UsuarioModel;
use App\Services\Abopech\PerfilAbogadoService;

final class PerfilController extends BaseController
{
    public function ver(int $id)
    {
        $abogado = (new AbogadoModel())->findPublico($id);
        if (!$abogado) {
            return redirect()->to(base_url('abopech/buscar'))->with('error', 'Perfil no disponible.');
        }

        $perfil = (new PerfilAbogadoService())->getPerfilCompleto($id);
        unset($perfil['abogado']['rut']);

        return view('abopech/perfil', [
            'perfil' => $perfil,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Controllers\Abopech\Admin;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoModel;
use App\Services\Abopech\PerfilAbogadoService;

final class AbogadoController extends BaseController
{
    public function lista()
    {
        $estado = trim((string) ($this->request->getGet('estado') ?? 'pendiente'));
        $model = new AbogadoModel();
        $builder = $model->orderBy('factualizacion', 'DESC');
        if ($estado !== '') {
            $builder->where('estado_perfil', $estado);
        }

        return view('abopech/admin/abogados_lista', [
            'abogados' => $builder->findAll(100),
            'estado'   => $estado,
        ]);
    }

    public function ver(int $id)
    {
        $perfil = (new PerfilAbogadoService())->getPerfilCompleto($id);
        if (!$perfil) {
            return redirect()->to(base_url('abopech/admin/abogados'));
        }

        return view('abopech/admin/abogado_ver', ['perfil' => $perfil]);
    }

    public function aprobar(int $id)
    {
        (new AbogadoModel())->update($id, ['estado_perfil' => 'aprobado']);

        return redirect()->back()->with('success', 'Perfil aprobado.');
    }

    public function rechazar(int $id)
    {
        (new AbogadoModel())->update($id, [
            'estado_perfil' => 'rechazado',
        ]);

        return redirect()->back()->with('success', 'Perfil rechazado.');
    }
}

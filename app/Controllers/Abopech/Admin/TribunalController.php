<?php

declare(strict_types=1);

namespace App\Controllers\Abopech\Admin;

use App\Controllers\BaseController;
use App\Models\Abopech\ComunaModel;
use App\Models\Abopech\RegionModel;
use App\Models\Abopech\TribunalModel;

final class TribunalController extends BaseController
{
    public function lista()
    {
        $tipo = trim((string) ($this->request->getGet('tipo') ?? ''));
        $model = new TribunalModel();
        $items = $tipo !== '' ? $model->listActivos($tipo) : $model->orderBy('nombre')->findAll(500);

        return view('abopech/admin/tribunales_lista', [
            'tribunales' => $items,
            'tipo'       => $tipo,
        ]);
    }

    public function registro()
    {
        return view('abopech/admin/tribunal_form', [
            'regiones' => (new RegionModel())->listActivas(),
            'comunas'  => [],
        ]);
    }

    public function guardar()
    {
        $rules = [
            'nombre'      => 'required|min_length[3]|max_length[300]',
            'region_id'   => 'required|integer',
            'tipo_codigo' => 'required|in_list[garantia,juicio_oral_penal,otro]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $comunaId = (int) ($this->request->getPost('comuna_id') ?? 0);

        (new TribunalModel())->insert([
            'nombre'       => trim((string) $this->request->getPost('nombre')),
            'descripcion'  => trim((string) $this->request->getPost('descripcion')),
            'region_id'    => (int) $this->request->getPost('region_id'),
            'comuna_id'    => $comunaId > 0 ? $comunaId : null,
            'tipo_codigo'  => (string) $this->request->getPost('tipo_codigo'),
            'estado'       => 'activo',
        ]);

        return redirect()->to(base_url('abopech/admin/tribunales'))->with('success', 'Tribunal registrado.');
    }
}

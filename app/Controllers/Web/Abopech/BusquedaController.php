<?php

declare(strict_types=1);

namespace App\Controllers\Web\Abopech;

use App\Controllers\BaseController;
use App\Models\Abopech\ComunaModel;
use App\Models\Abopech\RegionModel;
use App\Models\Abopech\TribunalModel;
use App\Services\Abopech\AbogadoBusquedaService;

final class BusquedaController extends BaseController
{
    public function index()
    {
        $regionId  = (int) ($this->request->getGet('region_id') ?? 0);
        $comunaId  = (int) ($this->request->getGet('comuna_id') ?? 0);
        $tribunalId = (int) ($this->request->getGet('tribunal_id') ?? 0);
        $tipoTribunal = trim((string) ($this->request->getGet('tipo_tribunal') ?? ''));

        $regionModel = new RegionModel();
        $comunaModel = new ComunaModel();
        $tribunalModel = new TribunalModel();

        $comunas = $regionId > 0 ? $comunaModel->listByRegion($regionId) : [];
        $tribunales = $tribunalModel->listActivos($tipoTribunal !== '' ? $tipoTribunal : null);

        $busqueda = new AbogadoBusquedaService();
        $abogados = $busqueda->buscar(
            $regionId > 0 ? $regionId : null,
            $comunaId > 0 ? $comunaId : null,
            $tribunalId > 0 ? $tribunalId : null
        );

        return view('abopech/buscar', [
            'regiones'      => $regionModel->listActivas(),
            'comunas'       => $comunas,
            'tribunales'    => $tribunales,
            'abogados'      => $abogados,
            'region_id'     => $regionId,
            'comuna_id'     => $comunaId,
            'tribunal_id'   => $tribunalId,
            'tipo_tribunal' => $tipoTribunal,
        ]);
    }

    public function comunasJson(int $regionId)
    {
        $comunas = (new ComunaModel())->listByRegion($regionId);

        return $this->response->setJSON($comunas);
    }
}

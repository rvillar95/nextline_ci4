<?php

declare(strict_types=1);

namespace App\Controllers\Abogado;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\RegionModel;
use App\Models\Abopech\TribunalModel;
use App\Models\Abopech\TipoEstudioModel;
use App\Services\Abopech\PerfilAbogadoService;

final class PerfilController extends BaseController
{
    public function index()
    {
        $usuario = abopech_usuario();
        $abogadoModel = new AbogadoModel();
        $abogado = $abogadoModel->findByUsuarioId((int) $usuario['id']);

        if (!$abogado) {
            return redirect()->to(base_url('abopech/auth/logout'));
        }

        $perfil = (new PerfilAbogadoService())->getPerfilCompleto((int) $abogado['id']);

        return view('abogado/perfil', [
            'abogado'    => $abogado,
            'perfil'     => $perfil,
            'regiones'   => (new RegionModel())->listActivas(),
            'tribunales' => (new TribunalModel())->listActivos(),
            'tipos_estudio' => (new TipoEstudioModel())->listActivos(),
        ]);
    }

    public function guardar()
    {
        $usuario = abopech_usuario();
        $abogadoModel = new AbogadoModel();
        $abogado = $abogadoModel->findByUsuarioId((int) $usuario['id']);
        if (!$abogado) {
            return redirect()->to(base_url('abopech'));
        }

        $habilidades = trim((string) $this->request->getPost('habilidades'));
        $experiencia = trim((string) $this->request->getPost('experiencia'));

        if (!abopech_word_count_ok($habilidades) || !abopech_word_count_ok($experiencia)) {
            return redirect()->back()->withInput()->with('error', 'Habilidades y experiencia: máximo 500 palabras cada una.');
        }

        $abogadoModel->update((int) $abogado['id'], [
            'rut'         => trim((string) $this->request->getPost('rut')),
            'nombres'     => trim((string) $this->request->getPost('nombres')),
            'apellidos'   => trim((string) $this->request->getPost('apellidos')),
            'habilidades' => $habilidades,
            'experiencia' => $experiencia,
        ]);

        (new PerfilAbogadoService())->syncRelaciones(
            (int) $abogado['id'],
            (array) ($this->request->getPost('region_ids') ?? []),
            (array) ($this->request->getPost('comuna_ids') ?? []),
            (array) ($this->request->getPost('tribunal_ids') ?? [])
        );

        return redirect()->to(base_url('abopech/mi-perfil'))->with('success', 'Perfil actualizado.');
    }

    public function enviarRevision()
    {
        $usuario = abopech_usuario();
        $abogadoModel = new AbogadoModel();
        $abogado = $abogadoModel->findByUsuarioId((int) $usuario['id']);
        if (!$abogado) {
            return redirect()->to(base_url('abopech'));
        }

        $estudios = db_connect('abopech')->table('rj_abogado_estudio')
            ->where('abogado_id', (int) $abogado['id'])->countAllResults();

        if ($estudios < 1) {
            return redirect()->back()->with('error', 'Debes registrar al menos un estudio con documento.');
        }

        $abogadoModel->update((int) $abogado['id'], ['estado_perfil' => 'pendiente']);

        return redirect()->back()->with('success', 'Perfil enviado a revisión.');
    }

    public function comunasJson(int $regionId)
    {
        $comunas = (new \App\Models\Abopech\ComunaModel())->listByRegion($regionId);

        return $this->response->setJSON($comunas);
    }
}

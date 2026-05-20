<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Usuario;
use App\Models\UsuarioCredencial;

class EquipoController extends BaseController
{
    protected const PLACEHOLDER_FOTO = 'lib/src/assets/img/profile-30.png';

    /**
     * GET /equipo — listado de nutricionistas activos.
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $nutricionistas = $db->table('usuario')
            ->select('id, nombre, apellido, foto, titulo_profesional, especialidad, carrera, presentacion, telefono, correo')
            ->where('perfil_id', Usuario::PERFIL_NUTRICIONISTA)
            ->where('estado', 'A')
            ->orderBy('nombre', 'ASC')
            ->orderBy('apellido', 'ASC')
            ->get()
            ->getResult();

        return view('Web/equipo', array_merge(seo_page([
            'title'       => 'Nuestro equipo | NutriNext',
            'description' => 'Conoce a los nutricionistas de NutriNext: formación, especialidades y cómo reservar tu consulta.',
            'keywords'    => 'equipo nutricionistas, nutricionista Chile, consulta nutricional, nutrinext',
            'canonical'   => seo_canonical_url('equipo'),
        ]), [
            'nutricionistas'   => $nutricionistas,
            'placeholder_foto' => base_url(self::PLACEHOLDER_FOTO),
        ]));
    }

    /**
     * GET /equipo/{id} — ficha pública del nutricionista.
     */
    public function detalle($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to(base_url('equipo'));
        }

        $db = \Config\Database::connect();
        $nutricionista = $db->table('usuario')
            ->select('id, nombre, apellido, foto, titulo_profesional, especialidad, carrera, presentacion, descripcion_profesional, telefono, correo')
            ->where('id', $id)
            ->where('perfil_id', Usuario::PERFIL_NUTRICIONISTA)
            ->where('estado', 'A')
            ->get()
            ->getRow();

        if (!$nutricionista) {
            return redirect()->to(base_url('equipo'))->with('error', 'Profesional no encontrado.');
        }

        $credencialModel = new UsuarioCredencial();
        $credenciales = $credencialModel->getPorUsuarioWeb($id);
        $credencialesPorTipo = $credencialModel->agruparPorTipo($credenciales);

        $nombreCompleto = trim(($nutricionista->nombre ?? '') . ' ' . ($nutricionista->apellido ?? ''));

        return view('Web/equipo_detalle', array_merge(seo_page([
            'title'       => $nombreCompleto . ' | Equipo NutriNext',
            'description' => $this->extracto($nutricionista->presentacion ?? '', 160) ?: 'Perfil profesional de ' . $nombreCompleto,
            'keywords'    => 'nutricionista, ' . $nombreCompleto . ', nutrinext',
            'canonical'   => seo_canonical_url('equipo/' . $id),
            'robots'      => 'index, follow',
        ]), [
            'nutricionista'        => $nutricionista,
            'credenciales_por_tipo' => $credencialesPorTipo,
            'tipos_label'          => UsuarioCredencial::TIPOS_LABEL,
            'placeholder_foto'   => base_url(self::PLACEHOLDER_FOTO),
            'nombre_completo'      => $nombreCompleto,
        ]));
    }

    private function extracto(?string $texto, int $max = 200): string
    {
        $texto = trim(strip_tags((string) $texto));
        if ($texto === '') {
            return '';
        }
        if (mb_strlen($texto) <= $max) {
            return $texto;
        }
        return mb_substr($texto, 0, $max) . '…';
    }
}

<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ServicioNutrinext;
use CodeIgniter\Exceptions\PageNotFoundException;

class ServicioNutrinextController extends BaseController
{
    public function index()
    {
        $model      = new ServicioNutrinext();
        $categoria  = $this->request->getGet('categoria');
        $categorias = ServicioNutrinext::categorias();
        $servicios  = $model->getParaWeb($categoria ?: null);
        $agrupados  = $model->getParaWebAgrupados($categoria ?: null);

        return view('Web/funcionalidades', array_merge(seo_page([
            'title'       => 'Funcionalidades | NutriNext - Software para nutricionistas',
            'description' => 'Conoce todas las funcionalidades de NutriNext: agenda, pacientes, historial clinico, planes alimentarios, pagos y WhatsApp.',
            'keywords'    => 'funcionalidades nutrinext, software nutricionista, agenda, historial clinico, plan alimentario',
            'canonical'   => seo_canonical_url('funcionalidades' . ($categoria ? '?categoria=' . urlencode($categoria) : '')),
        ]), [
            'servicios'          => $servicios,
            'por_categoria'      => $agrupados,
            'categorias'         => $categorias,
            'categoria_actual'   => $categoria,
        ]));
    }

    public function detalle(string $codigo)
    {
        $model   = new ServicioNutrinext();
        $servicio = $model->getPorCodigoWeb($codigo);

        if (! $servicio) {
            throw PageNotFoundException::forPageNotFound();
        }

        $relacionados = array_values(array_filter(
            $model->getParaWeb($servicio->categoria),
            static fn ($s) => (int) $s->id !== (int) $servicio->id
        ));
        $relacionados = array_slice($relacionados, 0, 3);

        $descSeo = mb_substr(strip_tags($servicio->descripcion_corta), 0, 160);

        return view('Web/funcionalidad_detalle', array_merge(seo_page([
            'title'       => $servicio->nombre . ' | NutriNext',
            'description' => $descSeo,
            'keywords'    => $servicio->etiquetas ?? 'nutrinext, ' . $servicio->nombre,
            'canonical'   => seo_canonical_url('funcionalidades/' . $servicio->codigo),
        ]), [
            'servicio'     => $servicio,
            'categorias'   => ServicioNutrinext::categorias(),
            'beneficios'   => ServicioNutrinext::parseLineas($servicio->beneficios),
            'incluye'      => ServicioNutrinext::parseLineas($servicio->incluye),
            'relacionados' => $relacionados,
        ]));
    }
}

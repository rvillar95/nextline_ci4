<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Paquete;

class PreciosController extends BaseController
{
    public function index()
    {
        $paqueteModel = new Paquete();

        $planes = $paqueteModel->where('activo', 'A')
            ->where('slug !=', 'nutri-partner')
            ->like('slug', 'nutri-', 'after')
            ->orderBy('orden', 'ASC')
            ->orderBy('precio_mensual', 'ASC')
            ->findAll();

        $presencia = $paqueteModel->where('slug', 'presencia')->where('activo', 'A')->first();
        $gestion   = $paqueteModel->where('slug', 'gestion')->where('activo', 'A')->first();

        $profesional = null;
        $avanzado    = null;
        foreach ($planes as $p) {
            if ($p->slug === 'nutri-profesional') {
                $profesional = $p;
            }
            if ($p->slug === 'nutri-avanzado') {
                $avanzado = $p;
            }
        }

        $bundles = [];
        if ($presencia && $profesional) {
            $suma = (float) $presencia->precio_mensual + (float) $profesional->precio_mensual;
            $bundles[] = [
                'nombre'  => 'Web + Consulta',
                'detalle' => 'Presencia + Nutri Profesional',
                'precio'  => 36990,
                'ahorro'  => max(0, $suma - 36990),
            ];
        }
        if ($gestion && $avanzado) {
            $suma = (float) $gestion->precio_mensual + (float) $avanzado->precio_mensual;
            $bundles[] = [
                'nombre'  => 'Gestión + Avanzado',
                'detalle' => 'Gestión + Nutri Avanzado',
                'precio'  => 74990,
                'ahorro'  => max(0, $suma - 74990),
            ];
        }

        $addons = [
            ['nombre' => '4 componentes', 'precio' => 7990],
            ['nombre' => '5 componentes', 'precio' => 12990],
            ['nombre' => 'Somatotipo', 'precio' => 6990],
            ['nombre' => 'Pack antropometría avanzada', 'precio' => 19990, 'nota' => '4 + 5 + somatotipo'],
            ['nombre' => 'Recordatorios WhatsApp', 'precio' => 4990],
        ];

        return view('Web/precios', array_merge(seo_page([
            'title'       => 'Planes y precios | NutriNext Chile',
            'description' => 'Planes Nutri Esencial, Profesional y Avanzado para nutricionistas en Chile. Composición corporal, agenda, plan alimentario y reserva web.',
            'keywords'    => 'precios nutrinext, software nutricionista chile, planes nutrición',
            'canonical'   => seo_canonical_url('precios'),
        ]), [
            'planes'  => $planes,
            'bundles' => $bundles,
            'addons'  => $addons,
        ]));
    }
}

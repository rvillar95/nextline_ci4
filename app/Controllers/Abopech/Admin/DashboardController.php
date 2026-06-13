<?php

declare(strict_types=1);

namespace App\Controllers\Abopech\Admin;

use App\Controllers\BaseController;
use App\Models\Abopech\AbogadoModel;
use App\Models\Abopech\ContactoModel;

final class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect('abopech');
        $pendientes = $db->table('rj_abogado')->where('estado_perfil', 'pendiente')->countAllResults();
        $contactosNuevos = $db->table('contacto')->where('estado_seguimiento', 'nuevo')->countAllResults();

        return view('abopech/admin/dashboard', [
            'pendientes'       => $pendientes,
            'contactos_nuevos' => $contactosNuevos,
        ]);
    }
}

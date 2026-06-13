<?php

declare(strict_types=1);

namespace App\Controllers\Abopech\Admin;

use App\Controllers\BaseController;
use App\Models\Abopech\ContactoModel;

final class ContactoController extends BaseController
{
    public function lista()
    {
        $model = new ContactoModel();
        $contactos = $model->orderBy('fcreacion', 'DESC')->findAll(200);

        return view('abopech/admin/contactos_lista', ['contactos' => $contactos]);
    }

    public function cambiarEstado(int $id)
    {
        $estado = trim((string) $this->request->getPost('estado_seguimiento'));
        $notas  = trim((string) $this->request->getPost('notas_internas'));
        $valid  = ['nuevo', 'visto', 'en_gestion', 'cerrado'];

        if (!in_array($estado, $valid, true)) {
            return redirect()->back()->with('error', 'Estado inválido.');
        }

        (new ContactoModel())->update($id, [
            'estado_seguimiento' => $estado,
            'notas_internas'     => $notas !== '' ? $notas : null,
        ]);

        return redirect()->back()->with('success', 'Contacto actualizado.');
    }
}

<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Notificacion;

class NotificacionController extends BaseController
{
    public function listar()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $usuarioId = (int) session()->get('usuario')['id'];
        $limit = min(50, max(5, (int) ($this->request->getGet('limit') ?: 20)));

        $model = new Notificacion();
        $rows = $model->where('usuario_id', $usuarioId)
            ->orderBy('fcreacion', 'DESC')
            ->limit($limit)
            ->findAll();

        $items = [];
        foreach ($rows as $row) {
            $enlace = $row['enlace'];
            if (in_array($row['tipo'], ['reserva_web', 'confirmacion_email'], true) && !empty($row['referencia_id'])) {
                $enlace = base_url('dashboard/agenda/lista?destacar=' . (int) $row['referencia_id']);
            } elseif ($row['tipo'] === 'cancelacion_email') {
                $enlace = base_url('dashboard/agenda/calendario');
            }
            $items[] = [
                'id'        => (int) $row['id'],
                'tipo'      => $row['tipo'],
                'titulo'    => $row['titulo'],
                'mensaje'   => $row['mensaje'],
                'enlace'    => $enlace,
                'leida'     => (int) $row['leida'],
                'fcreacion' => $row['fcreacion'],
                'hace'      => $this->tiempoRelativo($row['fcreacion']),
            ];
        }

        return $this->response->setJSON([
            'notificaciones' => $items,
            'no_leidas'      => $model->contarNoLeidas($usuarioId),
        ]);
    }

    public function marcarLeida()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $usuarioId = (int) session()->get('usuario')['id'];
        $id = (int) $this->request->getPost('id');

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'error' => 'ID inválido'])->setStatusCode(400);
        }

        $model = new Notificacion();
        $model->where('id', $id)->where('usuario_id', $usuarioId)->set(['leida' => 1])->update();

        return $this->response->setJSON([
            'success'   => true,
            'no_leidas' => $model->contarNoLeidas($usuarioId),
        ]);
    }

    public function marcarTodasLeidas()
    {
        $this->response->setContentType('application/json');

        if (!session()->get('usuario')) {
            return $this->response->setJSON(['error' => 'No autorizado'])->setStatusCode(401);
        }

        $usuarioId = (int) session()->get('usuario')['id'];
        $model = new Notificacion();
        $model->where('usuario_id', $usuarioId)->where('leida', 0)->set(['leida' => 1])->update();

        return $this->response->setJSON(['success' => true, 'no_leidas' => 0]);
    }

    private function tiempoRelativo(?string $fecha): string
    {
        if (!$fecha) {
            return '';
        }
        try {
            $dt = new \DateTime($fecha);
        } catch (\Exception $e) {
            return $fecha;
        }
        $diff = time() - $dt->getTimestamp();
        if ($diff < 60) {
            return 'Hace un momento';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return 'Hace ' . $m . ' min';
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return 'Hace ' . $h . ' h';
        }
        return $dt->format('d/m/Y H:i');
    }
}

<?php

namespace App\Controllers\Alumno;

use App\Controllers\BaseController;
use App\Services\Gym\EntrenamientoService;

class EntrenamientoController extends BaseController
{
    protected EntrenamientoService $entrenamiento;

    public function __construct()
    {
        $this->entrenamiento = new EntrenamientoService();
    }

    public function ejecutar($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $id = (int) $id;

        $data = $this->entrenamiento->obtenerSesionCompleta($id, $usuarioId);
        if (!$data) {
            return redirect()->to(base_url('alumno/inicio'))->with('errors', 'Sesión no encontrada');
        }

        if ($data['entrenamiento']->estado !== 'en_curso') {
            return redirect()->to(base_url('alumno/historial/' . $id));
        }

        $ultimosPesos = [];
        foreach ($data['ejercicios'] as $bloque) {
            $ejId = (int) $bloque['ejercicio']->ejercicio_id;
            $ultimosPesos[$ejId] = $this->entrenamiento->ultimoPesoEjercicio($usuarioId, $ejId, $id);
        }

        return view('alumno/entrenar', [
            'usuario'            => $usuario,
            'sesion'             => $data,
            'ultimosPesos'       => $ultimosPesos,
            'notasCoachPrograma' => $this->entrenamiento->notasCoachPrograma(
                (int) ($data['entrenamiento']->programa_usuario_id ?? 0)
            ),
            'navActive'          => 'entrenar',
            'pageTitle'          => 'Entrenando',
            'hideBottomNav'      => true,
        ]);
    }

    public function guardarSerie($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);

        $payload = $this->requestPayload();

        $serieId = (int) ($payload['serie_id'] ?? 0);
        if ($serieId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'          => false,
                'error'       => 'serie_id requerido',
                'csrf_token'  => csrf_hash(),
            ]);
        }

        $res = $this->entrenamiento->guardarSerie($serieId, $usuarioId, [
            'peso_kg'       => $payload['peso_kg'] ?? null,
            'repeticiones'  => $payload['repeticiones'] ?? null,
            'completada'    => $payload['completada'] ?? null,
            'notas'         => $payload['notas'] ?? null,
        ]);

        $res['csrf_token'] = csrf_hash();

        return $this->response->setJSON($res)->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    public function guardarSensacionEjercicio($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $payload = $this->requestPayload();

        $eeId = (int) ($payload['entrenamiento_ejercicio_id'] ?? 0);
        if ($eeId <= 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'         => false,
                'error'      => 'entrenamiento_ejercicio_id requerido',
                'csrf_token' => csrf_hash(),
            ]);
        }

        $res = $this->entrenamiento->guardarSensacionEjercicio(
            $eeId,
            $usuarioId,
            isset($payload['sensacion']) ? (string) $payload['sensacion'] : null
        );
        $res['csrf_token'] = csrf_hash();

        return $this->response->setJSON($res)->setHeader('X-CSRF-TOKEN', csrf_hash());
    }

    /**
     * POST form-urlencoded o JSON; nunca llama getJSON si el body no es JSON.
     *
     * @return array<string, mixed>
     */
    private function requestPayload(): array
    {
        $post = $this->request->getPost();
        if (is_array($post) && $post !== []) {
            return $post;
        }

        $ct = $this->request->getHeaderLine('Content-Type');
        if (stripos($ct, 'application/json') === false) {
            return is_array($post) ? $post : [];
        }

        try {
            $json = $this->request->getJSON(true);
            return is_array($json) ? $json : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function finalizar($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $id = (int) $id;
        $notas = $this->request->getPost('notas');

        if (!$this->entrenamiento->finalizar($id, $usuarioId, $notas)) {
            return redirect()->to(base_url('alumno/entrenamiento/' . $id))->with('errors', 'No se pudo finalizar la sesión');
        }

        return redirect()->to(base_url('alumno/historial/' . $id))->with('success', '¡Entrenamiento completado!');
    }

    public function abandonar($id)
    {
        $usuario = session()->get('usuario');
        $usuarioId = (int) ($usuario['id'] ?? 0);
        $id = (int) $id;

        $this->entrenamiento->abandonar($id, $usuarioId);

        return redirect()->to(base_url('alumno/inicio'))->with('success', 'Sesión abandonada');
    }
}

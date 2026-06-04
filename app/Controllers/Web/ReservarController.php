<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Services\ReservaPublicaService;

/**
 * Vista pública para que pacientes reserven hora (sin login).
 * Flujo: ver nutricionistas y horarios disponibles → elegir slot → datos (RUT, nombre, correo, teléfono) → reserva creada (estado reservada).
 */
class ReservarController extends BaseController
{
    protected ReservaPublicaService $reservaService;

    public function __construct()
    {
        $this->reservaService = new ReservaPublicaService();
    }

    /**
     * Página pública: listar nutricionistas y disponibilidad.
     * GET /reservar?e=empresa_id (e opcional; si no se envía se usa la primera empresa)
     */
    public function index()
    {
        $empresaParam = (int) $this->request->getGet('e');
        $empresaId = $empresaParam > 0 ? $empresaParam : $this->reservaService->resolverEmpresaId(null);
        $nutricionistaPre = (int) $this->request->getGet('nutricionista_id');
        $nutricionistas = $this->reservaService->listarNutricionistasConCupos($empresaParam > 0 ? $empresaParam : null);
        if ($nutricionistaPre > 0 && ! $this->nutricionistaEnLista($nutricionistas, $nutricionistaPre)) {
            $nutricionistas = array_merge(
                $nutricionistas,
                $this->reservaService->listarNutricionistasPorIds([$nutricionistaPre])
            );
        }
        $data = [
            'empresa_id'          => $empresaId,
            'nutricionista_pre'   => $nutricionistaPre,
            'nutricionistas'      => $nutricionistas,
            'csrf_token'     => csrf_hash(),
        ];
        return view('Web/reservar', array_merge(seo_page([
            'title'       => 'Reservar hora | NutriNext - Consulta nutricional online',
            'description' => 'Reserva tu hora con un nutricionista de forma online. Elige profesional, fecha y horario disponible sin necesidad de crear cuenta.',
            'keywords'    => 'reservar cita nutricionista, agendar consulta nutricional, hora nutrición online, nutrinext',
            'canonical'   => seo_canonical_url('reservar'),
        ]), $data));
    }

    /**
     * Disponibilidad pública (slots sin paciente).
     * GET /reservar/disponibilidad?nutricionista_id=2&fecha=2026-02-24
     */
    public function disponibilidad()
    {
        $this->response->setContentType('application/json');
        $nutricionistaId = $this->request->getGet('nutricionista_id');
        $fecha = $this->request->getGet('fecha');
        if (!$fecha || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            $fecha = date('Y-m-d');
        }
        $nutId = ($nutricionistaId !== null && $nutricionistaId !== '') ? (int) $nutricionistaId : null;
        $empresaParam = (int) $this->request->getGet('e');
        $empresaFiltro = $empresaParam > 0 ? $empresaParam : null;
        $slots = $this->reservaService->disponibilidad($nutId, $fecha, $empresaFiltro);
        return $this->response->setJSON(['success' => true, 'slots' => $slots]);
    }

    /**
     * @param list<object> $lista
     */
    private function nutricionistaEnLista(array $lista, int $id): bool
    {
        foreach ($lista as $n) {
            if ((int) ($n->id ?? 0) === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Buscar paciente por RUT para autocompletar.
     * GET /reservar/paciente-por-rut?rut=12345678-9
     */
    public function pacientePorRut()
    {
        $this->response->setContentType('application/json');
        $rut = $this->request->getGet('rut');
        $paciente = $this->reservaService->pacientePorRut((string) ($rut ?? ''));
        if (!$paciente) {
            return $this->response->setJSON(['found' => false]);
        }
        return $this->response->setJSON([
            'found'    => true,
            'nombre'   => $paciente['nombre'],
            'apellido' => $paciente['apellido'],
            'telefono' => $paciente['telefono'],
            'email'    => $paciente['email'],
        ]);
    }

    /**
     * Crear reserva (paciente reserva desde link público).
     * POST detalle_agenda_id, rut_dni, nombre, apellido, email, telefono
     */
    public function reservar()
    {
        $this->response->setContentType('application/json');
        $resultado = $this->reservaService->crearReserva(
            (int) $this->request->getPost('detalle_agenda_id'),
            (string) $this->request->getPost('rut_dni'),
            (string) $this->request->getPost('nombre'),
            (string) $this->request->getPost('apellido'),
            (string) $this->request->getPost('email'),
            trim((string) $this->request->getPost('telefono')) ?: null,
            'paciente'
        );
        if (!$resultado['success']) {
            return $this->response->setJSON($resultado)->setStatusCode(400);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => $resultado['message'],
        ]);
    }
}

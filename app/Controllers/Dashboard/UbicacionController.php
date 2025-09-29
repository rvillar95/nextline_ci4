<?php

namespace App\Controllers\Dashboard;

use App\Controllers\BaseController;
use App\Models\Region;
use App\Models\Comuna;

class UbicacionController extends BaseController
{
    /**
     * Obtener todas las regiones activas
     */
    public function getRegiones()
    {
        $regionModel = new Region();
        $regiones = $regionModel->getRegionesActivas();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $regiones
        ]);
    }

    /**
     * Obtener comunas de una región específica
     */
    public function getComunasPorRegion($regionId = null)
    {
        if (!$regionId) {
            $regionId = $this->request->getGet('region_id');
        }
        
        if (!$regionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de región requerido'
            ]);
        }
        
        $comunaModel = new Comuna();
        $comunas = $comunaModel->getComunasPorRegion($regionId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $comunas
        ]);
    }

    /**
     * Buscar comunas por término de búsqueda
     */
    public function buscarComunas()
    {
        $termino = $this->request->getGet('q');
        
        if (!$termino || strlen($termino) < 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Término de búsqueda debe tener al menos 2 caracteres'
            ]);
        }
        
        $comunaModel = new Comuna();
        $comunas = $comunaModel->buscarComunas($termino);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $comunas
        ]);
    }

    /**
     * Obtener información completa de una comuna
     */
    public function getComunaInfo($comunaId = null)
    {
        if (!$comunaId) {
            $comunaId = $this->request->getGet('comuna_id');
        }
        
        if (!$comunaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de comuna requerido'
            ]);
        }
        
        $comunaModel = new Comuna();
        $comuna = $comunaModel->getComunaConRegion($comunaId);
        
        if (!$comuna) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Comuna no encontrada'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $comuna
        ]);
    }

    /**
     * Validar región y comuna
     */
    public function validarUbicacion()
    {
        $regionId = $this->request->getPost('region_id');
        $comunaId = $this->request->getPost('comuna_id');
        
        if (!$regionId || !$comunaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Región y comuna son requeridas'
            ]);
        }
        
        $comunaModel = new Comuna();
        $esValida = $comunaModel->validarComunaRegion($comunaId, $regionId);
        
        return $this->response->setJSON([
            'success' => true,
            'valid' => $esValida,
            'message' => $esValida ? 'Ubicación válida' : 'La comuna no pertenece a la región seleccionada'
        ]);
    }

    /**
     * Obtener estadísticas de ubicaciones
     */
    public function getEstadisticas()
    {
        $regionModel = new Region();
        $comunaModel = new Comuna();
        
        $totalRegiones = $regionModel->where('activo', 1)->countAllResults();
        $totalComunas = $comunaModel->where('activo', 1)->countAllResults();
        $estadisticasRegiones = $regionModel->getEstadisticasRegiones();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_regiones' => $totalRegiones,
                'total_comunas' => $totalComunas,
                'estadisticas_regiones' => $estadisticasRegiones
            ]
        ]);
    }
}

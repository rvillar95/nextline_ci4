<?php

namespace App\Services;

use App\Models\ModuloDetalle;

/**
 * Servicio para verificar acceso a módulos y métodos según paquete y add-ons
 */
class AccesoService
{
    /**
     * Verificar si empresa tiene acceso a un módulo
     * Considera: Paquete base + Add-ons activos
     */
    public function tieneAccesoModulo($empresaId, $moduloId)
    {
        $db = \Config\Database::connect();
        
        // 1. Verificar si está en el paquete base
        $empresa = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRow();
        
        if ($empresa && $empresa->paquete_id) {
            $enPaquete = $db->table('paquete_modulo')
                ->where('paquete_id', $empresa->paquete_id)
                ->where('modulo_id', $moduloId)
                ->where('incluido', 'S')
                ->countAllResults() > 0;
            
            if ($enPaquete) {
                return true; // Está en el plan base
            }
        }
        
        // 2. Verificar si es add-on activo
        $addonActivo = $db->table('empresa_addon')
            ->where('empresa_id', $empresaId)
            ->where('tipo', 'modulo')
            ->where('referencia_id', $moduloId)
            ->where('estado', 'activo')
            ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
            ->countAllResults() > 0;
        
        return $addonActivo;
    }
    
    /**
     * Verificar si empresa tiene acceso a un método de cálculo
     * Considera: Paquete base (usando paquete_modulo_detalle) + Add-ons activos
     */
    public function tieneAccesoMetodo($empresaId, $metodoSlug)
    {
        $db = \Config\Database::connect();
        
        // Obtener ID de la ruta del método
        $rutaMetodo = $db->table('modulo_detalle')
            ->select('id')
            ->where('ruta', '/calcular-' . $metodoSlug)
            ->get()
            ->getRow();
        
        if (!$rutaMetodo) {
            return false;
        }
        
        // 1. Verificar si está en el paquete base (usando paquete_modulo_detalle)
        $empresa = $db->table('empresa')
            ->select('paquete_id')
            ->where('id', $empresaId)
            ->get()
            ->getRow();
        
        if ($empresa && $empresa->paquete_id) {
            // Verificar en paquete_modulo_detalle (rutas específicas)
            $enPaquete = $db->table('paquete_modulo_detalle')
                ->where('paquete_id', $empresa->paquete_id)
                ->where('modulo_detalle_id', $rutaMetodo->id)
                ->where('incluido', 'S')
                ->countAllResults() > 0;
            
            if ($enPaquete) {
                return true; // Está en el plan base
            }
        }
        
        // 2. Verificar si es add-on activo (usando el ID del método de cálculo o modulo_detalle)
        $metodo = $db->table('metodos_calculo')
            ->select('id')
            ->where('slug', $metodoSlug)
            ->get()
            ->getRow();
        
        if ($metodo) {
            // Verificar add-on por método de cálculo
            $addonActivo = $db->table('empresa_addon')
                ->where('empresa_id', $empresaId)
                ->where('tipo', 'metodo_calculo')
                ->where('referencia_id', $metodo->id)
                ->where('estado', 'activo')
                ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
                ->countAllResults() > 0;
            
            if ($addonActivo) {
                return true;
            }
            
            // También verificar add-on por modulo_detalle (ruta específica)
            $addonRuta = $db->table('empresa_addon')
                ->where('empresa_id', $empresaId)
                ->where('tipo', 'modulo')
                ->where('referencia_id', $rutaMetodo->id)
                ->where('estado', 'activo')
                ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
                ->countAllResults() > 0;
            
            if ($addonRuta) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Obtener todos los add-ons activos de una empresa
     */
    public function getAddonsActivos($empresaId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('empresa_addon')
            ->where('empresa_id', $empresaId)
            ->where('estado', 'activo')
            ->where('(fecha_fin IS NULL OR fecha_fin >= CURDATE())', null, false)
            ->get()
            ->getResult();
    }
    
    /**
     * Verificar si empresa tiene acceso a un módulo por su ruta
     * Útil para verificar acceso desde controladores sin conocer el ID del módulo
     */
    public function tieneAccesoModuloPorRuta($empresaId, $rutaModulo)
    {
        $db = \Config\Database::connect();
        
        // Obtener ID del módulo por ruta
        $modulo = $db->table('modulo')
            ->select('id')
            ->where('ruta', $rutaModulo)
            ->where('estado', 'A')
            ->get()
            ->getRow();
        
        if (!$modulo) {
            return false;
        }
        
        return $this->tieneAccesoModulo($empresaId, $modulo->id);
    }
    
    /**
     * Calcular precio total mensual de una empresa
     */
    public function calcularPrecioTotalMensual($empresaId)
    {
        $db = \Config\Database::connect();
        
        // Precio del paquete base
        $empresa = $db->table('empresa e')
            ->select('p.precio_mensual as precio_paquete')
            ->join('paquetes p', 'p.id = e.paquete_id', 'left')
            ->where('e.id', $empresaId)
            ->get()
            ->getRow();
        
        $precioBase = $empresa->precio_paquete ?? 0;
        
        // Sumar precio de add-ons activos
        $addons = $this->getAddonsActivos($empresaId);
        $precioAddons = 0;
        foreach ($addons as $addon) {
            $precioAddons += floatval($addon->precio_mensual);
        }
        
        return [
            'precio_base' => floatval($precioBase),
            'precio_addons' => $precioAddons,
            'total' => floatval($precioBase) + $precioAddons
        ];
    }
}

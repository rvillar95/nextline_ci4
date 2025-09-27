<?php

namespace App\Traits;

trait MaintainsFilters
{
    /**
     * Redirige manteniendo los parámetros de filtro de la URL actual
     */
    protected function redirectWithFilters($url, $message = null, $type = 'success')
    {
        // Obtener parámetros de filtro de la URL actual
        $filters = $this->request->getGet();
        
        // Construir URL con filtros
        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }
        
        // Redirigir con mensaje
        if ($message) {
            return redirect()->to($url)->with($type, $message);
        }
        
        return redirect()->to($url);
    }
    
    /**
     * Redirige manteniendo filtros específicos
     */
    protected function redirectWithSpecificFilters($url, $filters, $message = null, $type = 'success')
    {
        // Construir URL con filtros específicos
        if (!empty($filters)) {
            $url .= '?' . http_build_query($filters);
        }
        
        // Redirigir con mensaje
        if ($message) {
            return redirect()->to($url)->with($type, $message);
        }
        
        return redirect()->to($url);
    }
    
    /**
     * Redirige manteniendo filtros desde POST (para formularios de eliminación)
     */
    protected function redirectWithPostFilters($url, $message = null, $type = 'success')
    {
        // Obtener filtros del POST (campos que terminan en _filter)
        $postData = $this->request->getPost();
        $filters = [];
        
        foreach ($postData as $key => $value) {
            if (str_ends_with($key, '_filter') && !empty($value)) {
                $filterKey = str_replace('_filter', '', $key);
                $filters[$filterKey] = $value;
            }
        }
        
        return $this->redirectWithSpecificFilters($url, $filters, $message, $type);
    }
}

<?php

namespace App\Validation;

use CodeIgniter\Validation\Rules;

class ChileanRutRules extends Rules
{
    /**
     * Valida que el RUT chileno sea válido
     * 
     * @param string $str
     * @param string $error
     * @param array $data
     * @return bool
     */
    public function chilean_rut(string $str, string $error = null, array $data = []): bool
    {
        // Si el campo está vacío y es permit_empty, retornar true
        if (empty($str)) {
            return true;
        }
        
        // Limpiar el RUT: remover espacios, puntos y convertir a mayúscula
        $rut = strtoupper(trim($str));
        $rut = str_replace([' ', '.'], '', $rut);
        
        // Verificar formato básico: números seguidos de guión y dígito verificador
        if (!preg_match('/^(\d{1,8})-([0-9K])$/', $rut, $matches)) {
            return false;
        }
        
        $numero = $matches[1];
        $dv = $matches[2];
        
        // Verificar que el número tenga entre 1 y 8 dígitos
        if (strlen($numero) < 1 || strlen($numero) > 8) {
            return false;
        }
        
        // Calcular dígito verificador
        $dvCalculado = $this->calcularDigitoVerificador($numero);
        
        // Comparar dígito verificador
        return $dv === $dvCalculado;
    }
    
    /**
     * Calcula el dígito verificador de un RUT chileno
     * 
     * @param string $numero
     * @return string
     */
    private function calcularDigitoVerificador(string $numero): string
    {
        $suma = 0;
        $multiplicador = 2;
        
        // Recorrer el número de derecha a izquierda
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += (int) $numero[$i] * $multiplicador;
            $multiplicador++;
            if ($multiplicador > 7) {
                $multiplicador = 2;
            }
        }
        
        $resto = $suma % 11;
        $dv = 11 - $resto;
        
        if ($dv == 11) {
            return '0';
        } elseif ($dv == 10) {
            return 'K';
        } else {
            return (string) $dv;
        }
    }
    
    /**
     * Formatea un RUT con puntos y guión
     * 
     * @param string $rut
     * @return string
     */
    public function formatearRut(string $rut): string
    {
        // Limpiar el RUT
        $rut = str_replace([' ', '.', '-'], '', strtoupper(trim($rut)));
        
        // Separar número y dígito verificador
        if (preg_match('/^(\d{1,8})([0-9K])$/', $rut, $matches)) {
            $numero = $matches[1];
            $dv = $matches[2];
            
            // Agregar puntos cada 3 dígitos desde la derecha
            $numeroFormateado = number_format($numero, 0, ',', '.');
            
            return $numeroFormateado . '-' . $dv;
        }
        
        return $rut;
    }
    
    /**
     * Limpia un RUT removiendo puntos y guiones
     * 
     * @param string $rut
     * @return string
     */
    public function limpiarRut(string $rut): string
    {
        return str_replace([' ', '.', '-'], '', strtoupper(trim($rut)));
    }
}

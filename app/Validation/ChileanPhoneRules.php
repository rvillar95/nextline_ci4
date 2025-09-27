<?php

namespace App\Validation;

use CodeIgniter\Validation\Rules;

class ChileanPhoneRules extends Rules
{
    /**
     * Valida que el número de teléfono sea chileno válido
     * 
     * @param string $str
     * @param string $error
     * @param array $data
     * @return bool
     */
    public function chilean_phone(string $str, string $error = null, array $data = []): bool
    {
        // Si el campo está vacío y es permit_empty, retornar true
        if (empty($str)) {
            return true;
        }
        
        // Remover espacios, guiones y paréntesis
        $phone = preg_replace('/[\s\-\(\)]/', '', $str);
        
        // Verificar que solo contenga números y el prefijo +56
        if (!preg_match('/^(\+56|56)?[2-9]\d{8}$/', $phone)) {
            return false;
        }
        
        // Si tiene prefijo +56 o 56, removerlo
        if (strpos($phone, '+56') === 0) {
            $phone = substr($phone, 3);
        } elseif (strpos($phone, '56') === 0 && strlen($phone) === 11) {
            $phone = substr($phone, 2);
        }
        
        // Verificar que tenga exactamente 9 dígitos
        if (strlen($phone) !== 9) {
            return false;
        }
        
        // Verificar que el primer dígito sea válido para Chile (2-9)
        $firstDigit = (int) $phone[0];
        if ($firstDigit < 2 || $firstDigit > 9) {
            return false;
        }
        
        return true;
    }
}

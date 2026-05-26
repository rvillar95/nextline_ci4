<?php

namespace App\Libraries;

/**
 * Validación y formato de RUT/DNI chileno (módulo 11).
 */
class RutChile
{
    public static function limpiar(string $rut): string
    {
        return preg_replace('/[^0-9kK]/', '', trim($rut)) ?? '';
    }

    public static function validar(string $rut): bool
    {
        $rut = self::limpiar($rut);
        if (strlen($rut) < 2) {
            return false;
        }

        $numero = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));

        if ($numero === '' || !ctype_digit($numero)) {
            return false;
        }

        $suma = 0;
        $multiplicador = 2;
        for ($i = strlen($numero) - 1; $i >= 0; $i--) {
            $suma += (int) $numero[$i] * $multiplicador;
            $multiplicador++;
            if ($multiplicador > 7) {
                $multiplicador = 2;
            }
        }

        $resto = $suma % 11;
        $dvCalculado = 11 - $resto;

        if ($dvCalculado === 11) {
            $dvCalculado = '0';
        } elseif ($dvCalculado === 10) {
            $dvCalculado = 'K';
        } else {
            $dvCalculado = (string) $dvCalculado;
        }

        return $dv === $dvCalculado;
    }

    public static function formatear(string $rut): string
    {
        $rut = self::limpiar($rut);
        if ($rut === '') {
            return '';
        }

        $numero = substr($rut, 0, -1);
        $dv = strtoupper(substr($rut, -1));
        if ($numero === '') {
            return $rut;
        }

        $invertido = strrev($numero);
        $conPuntos = chunk_split($invertido, 3, '.');
        $numeroFormateado = strrev(rtrim($conPuntos, '.'));

        return $numeroFormateado . '-' . $dv;
    }
}

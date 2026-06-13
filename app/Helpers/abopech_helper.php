<?php

declare(strict_types=1);

namespace App\Helpers;

if (!function_exists('abopech_usuario')) {
    function abopech_usuario(): ?array
    {
        $u = session()->get('abopech_usuario');

        return is_array($u) ? $u : null;
    }
}

if (!function_exists('abopech_telefono_e164')) {
    function abopech_telefono_e164(string $telefono): string
    {
        $digits = preg_replace('/\D+/', '', $telefono) ?? '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '56')) {
            return '+' . $digits;
        }
        if (str_starts_with($digits, '9') && strlen($digits) === 9) {
            return '+56' . $digits;
        }

        return '+' . $digits;
    }
}

if (!function_exists('abopech_whatsapp_url')) {
    function abopech_whatsapp_url(string $e164): string
    {
        $n = ltrim($e164, '+');

        return 'https://wa.me/' . $n;
    }
}

if (!function_exists('abopech_word_count_ok')) {
    function abopech_word_count_ok(string $text, int $maxWords = 500): bool
    {
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return count($words) <= $maxWords;
    }
}

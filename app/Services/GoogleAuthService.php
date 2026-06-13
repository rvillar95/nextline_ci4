<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

final class GoogleAuthService
{
    public function getAuthUrl(string $state): string
    {
        $clientId    = (string) env('GOOGLE_AUTH_CLIENT_ID');
        $redirectUri = (string) env('GOOGLE_AUTH_REDIRECT_URI', base_url('abopech/auth/google/callback'));

        if ($clientId === '' || $redirectUri === '') {
            throw new Exception('Google OAuth no configurado (GOOGLE_AUTH_CLIENT_ID / GOOGLE_AUTH_REDIRECT_URI)');
        }

        $params = [
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'prompt'        => 'select_account',
            'state'         => $state,
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * @return array{access_token:string}
     */
    public function exchangeCodeForToken(string $code): array
    {
        $clientId     = (string) env('GOOGLE_AUTH_CLIENT_ID');
        $clientSecret = (string) env('GOOGLE_AUTH_CLIENT_SECRET');
        $redirectUri  = (string) env('GOOGLE_AUTH_REDIRECT_URI', base_url('abopech/auth/google/callback'));

        if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
            throw new Exception('Google OAuth no configurado');
        }

        $payload = [
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ];

        $resp = $this->postForm('https://oauth2.googleapis.com/token', $payload);
        if (empty($resp['access_token'])) {
            throw new Exception('No se pudo obtener access_token de Google');
        }

        return $resp;
    }

    /**
     * @return array{sub:string,email?:string,name?:string,given_name?:string,family_name?:string}
     */
    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        ]);
        $raw      = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode((string) $raw, true);
        if ($httpCode < 200 || $httpCode >= 300 || !is_array($data) || empty($data['sub'])) {
            throw new Exception('Error al obtener perfil Google');
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function postForm(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        return json_decode((string) $raw, true) ?: [];
    }
}

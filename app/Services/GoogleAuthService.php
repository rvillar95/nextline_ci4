<?php

namespace App\Services;

use Exception;

class GoogleAuthService
{
    public function getAuthUrl(string $state): string
    {
        $clientId = (string) env('GOOGLE_AUTH_CLIENT_ID');
        $redirectUri = (string) env('GOOGLE_AUTH_REDIRECT_URI', base_url('auth/google/callback'));

        if ($clientId === '' || $redirectUri === '') {
            throw new Exception('Google OAuth no configurado (GOOGLE_AUTH_CLIENT_ID / GOOGLE_AUTH_REDIRECT_URI)');
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
            'prompt' => 'select_account',
            'state' => $state,
        ];

        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }

    /**
     * @return array{access_token:string,id_token?:string,refresh_token?:string,expires_in?:int,token_type?:string}
     */
    public function exchangeCodeForToken(string $code): array
    {
        $clientId = (string) env('GOOGLE_AUTH_CLIENT_ID');
        $clientSecret = (string) env('GOOGLE_AUTH_CLIENT_SECRET');
        $redirectUri = (string) env('GOOGLE_AUTH_REDIRECT_URI', base_url('auth/google/callback'));

        if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
            throw new Exception('Google OAuth no configurado (CLIENT_ID/SECRET/REDIRECT_URI)');
        }

        $payload = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $resp = $this->postForm('https://oauth2.googleapis.com/token', $payload);
        if (empty($resp['access_token'])) {
            throw new Exception('No se pudo obtener access_token de Google');
        }
        return $resp;
    }

    /**
     * @return array{sub:string,email?:string,email_verified?:bool,name?:string,given_name?:string,family_name?:string,picture?:string}
     */
    public function getUserInfo(string $accessToken): array
    {
        $ch = curl_init('https://openidconnect.googleapis.com/v1/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
        ]);
        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new Exception('Error al consultar userinfo: ' . $err);
        }

        $data = json_decode($raw, true);
        if ($httpCode < 200 || $httpCode >= 300) {
            $msg = is_array($data) ? json_encode($data) : $raw;
            throw new Exception('Google userinfo error HTTP ' . $httpCode . ': ' . $msg);
        }

        if (empty($data['sub'])) {
            throw new Exception('userinfo sin sub');
        }

        return $data;
    }

    private function postForm(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        $raw = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new Exception('Error HTTP POST: ' . $err);
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new Exception('Respuesta inválida: ' . $raw);
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new Exception('HTTP ' . $httpCode . ': ' . json_encode($data));
        }

        return $data;
    }
}


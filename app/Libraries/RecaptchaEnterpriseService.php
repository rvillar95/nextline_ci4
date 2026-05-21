<?php

declare(strict_types=1);

namespace App\Libraries;

use Google\ApiCore\ApiException;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

/**
 * Valida tokens reCAPTCHA Enterprise (Create Assessment) o siteverify (legacy secret).
 */
class RecaptchaEnterpriseService
{
    private static ?RecaptchaEnterpriseServiceClient $client = null;

    /**
     * Verificación clásica siteverify (secret key de Enterprise).
     *
     * @return bool|null true/false si evaluó; null si no hay secretKey
     */
    public function verifySiteverify(string $token, ?string $userIp = null): ?bool
    {
        $secretKey = trim((string) env('recaptcha.secretKey', ''));
        if ($secretKey === '') {
            return null;
        }

        $fields = [
            'secret'   => $secretKey,
            'response' => $token,
        ];
        if ($userIp !== null && $userIp !== '') {
            $fields['remoteip'] = $userIp;
        }

        $result = $this->httpPost(
            'https://www.google.com/recaptcha/api/siteverify',
            http_build_query($fields),
            ['Content-Type: application/x-www-form-urlencoded']
        );

        if ($result === null) {
            if (ENVIRONMENT === 'development') {
                log_message('warning', 'reCAPTCHA siteverify: sin respuesta HTTP — permitido en development');

                return true;
            }

            return false;
        }

        $data = json_decode($result['body'], true);
        if (!is_array($data)) {
            log_message('error', 'reCAPTCHA siteverify: JSON inválido');

            return false;
        }

        if (!($data['success'] ?? false)) {
            log_message('warning', 'reCAPTCHA siteverify falló: ' . json_encode($data));

            return false;
        }

        $minScore = (float) env('recaptcha.minScore', 0.5);
        $score    = (float) ($data['score'] ?? 1.0);
        log_message('info', "reCAPTCHA siteverify: score={$score}");

        if ($score < $minScore) {
            log_message('warning', "reCAPTCHA siteverify: score bajo ({$score} < {$minScore})");

            return false;
        }

        return true;
    }

    /**
     * @return bool|null true/false si pudo evaluar; null si Enterprise no está configurado
     */
    public function verify(string $token, ?string $userIp = null): ?bool
    {
        $projectId = trim((string) env('recaptcha.projectId', ''));
        $siteKey   = trim((string) env('recaptcha.siteKey', ''));
        $action    = trim((string) env('recaptcha.action', 'contacto')) ?: 'contacto';
        $minScore  = (float) env('recaptcha.minScore', 0.5);

        if ($projectId === '' || $siteKey === '') {
            return null;
        }

        if ($this->hasServiceAccountCredentials()) {
            return $this->verifyWithSdk($token, $projectId, $siteKey, $action, $minScore, $userIp);
        }

        $apiKey = trim((string) env('recaptcha.apiKey', ''));
        if ($apiKey !== '') {
            return $this->verifyWithRestApi($token, $projectId, $siteKey, $action, $minScore, $apiKey);
        }

        return null;
    }

    private function hasServiceAccountCredentials(): bool
    {
        $path = $this->resolveCredentialsPath();
        if ($path !== null && is_readable($path)) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $path);
            $_ENV['GOOGLE_APPLICATION_CREDENTIALS']    = $path;
            $_SERVER['GOOGLE_APPLICATION_CREDENTIALS'] = $path;

            return true;
        }

        $envPath = getenv('GOOGLE_APPLICATION_CREDENTIALS') ?: '';

        return $envPath !== '' && is_readable($envPath);
    }

    private function resolveCredentialsPath(): ?string
    {
        $configured = trim((string) env('recaptcha.credentialsPath', ''));
        if ($configured === '') {
            return null;
        }

        if (str_starts_with($configured, DIRECTORY_SEPARATOR) || preg_match('/^[A-Za-z]:\\\\/', $configured)) {
            return $configured;
        }

        return rtrim(ROOTPATH, '\\/') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $configured);
    }

    private function verifyWithSdk(
        string $token,
        string $projectId,
        string $siteKey,
        string $action,
        float $minScore,
        ?string $userIp
    ): bool {
        try {
            $client      = self::getClient();
            $projectName = $client->projectName($projectId);

            $event = (new Event())
                ->setSiteKey($siteKey)
                ->setToken($token)
                ->setExpectedAction($action);

            if ($userIp !== null && $userIp !== '') {
                $event->setUserIpAddress($userIp);
            }

            $assessment = (new Assessment())->setEvent($event);
            $request    = (new CreateAssessmentRequest())
                ->setParent($projectName)
                ->setAssessment($assessment);

            $response = $client->createAssessment($request);

            if (!$response->getTokenProperties()->getValid()) {
                $reason = InvalidReason::name($response->getTokenProperties()->getInvalidReason());
                log_message('warning', 'reCAPTCHA Enterprise SDK: token inválido - ' . $reason);

                return false;
            }

            $actionReturned = (string) $response->getTokenProperties()->getAction();
            if ($actionReturned !== '' && $actionReturned !== $action) {
                log_message(
                    'warning',
                    "reCAPTCHA Enterprise SDK: action esperada {$action}, recibida {$actionReturned}"
                );

                return false;
            }

            $score = (float) $response->getRiskAnalysis()->getScore();
            log_message('info', "reCAPTCHA Enterprise SDK: score={$score}");

            if ($score < $minScore) {
                log_message('warning', "reCAPTCHA Enterprise SDK: score bajo ({$score} < {$minScore})");

                return false;
            }

            return true;
        } catch (ApiException $e) {
            log_message('error', 'reCAPTCHA Enterprise SDK: ' . $e->getMessage());

            return false;
        } catch (\Throwable $e) {
            log_message('error', 'reCAPTCHA Enterprise SDK: ' . $e->getMessage());

            return false;
        }
    }

    private static function getClient(): RecaptchaEnterpriseServiceClient
    {
        if (self::$client === null) {
            self::$client = new RecaptchaEnterpriseServiceClient();
        }

        return self::$client;
    }

    private function verifyWithRestApi(
        string $token,
        string $projectId,
        string $siteKey,
        string $action,
        float $minScore,
        string $apiKey
    ): bool {
        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            rawurlencode($projectId),
            rawurlencode($apiKey)
        );

        $payload = json_encode([
            'event' => [
                'token'          => $token,
                'siteKey'        => $siteKey,
                'expectedAction' => $action,
            ],
        ]);

        $result = $this->httpPost($url, $payload, ['Content-Type: application/json']);
        if ($result === null || $result['code'] >= 400) {
            log_message('error', 'reCAPTCHA Enterprise REST: error HTTP ' . ($result['code'] ?? 0));

            return false;
        }

        $data = json_decode($result['body'], true);
        if (!is_array($data)) {
            log_message('error', 'reCAPTCHA Enterprise REST: respuesta inválida');

            return false;
        }

        if (!($data['tokenProperties']['valid'] ?? false)) {
            $reason = $data['tokenProperties']['invalidReason'] ?? 'unknown';
            log_message('warning', 'reCAPTCHA Enterprise REST: token inválido - ' . $reason);

            return false;
        }

        $actionReturned = (string) ($data['tokenProperties']['action'] ?? '');
        if ($actionReturned !== '' && $actionReturned !== $action) {
            log_message(
                'warning',
                "reCAPTCHA Enterprise REST: action esperada {$action}, recibida {$actionReturned}"
            );

            return false;
        }

        $score = (float) ($data['riskAnalysis']['score'] ?? 0);
        log_message('info', "reCAPTCHA Enterprise REST: score={$score}");

        if ($score < $minScore) {
            log_message('warning', "reCAPTCHA Enterprise REST: score bajo ({$score} < {$minScore})");

            return false;
        }

        return true;
    }

    private function sslVerifyPeer(): bool
    {
        return filter_var(env('recaptcha.sslVerify', 'true'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array{body: string, code: int}|null
     */
    private function httpPost(string $url, string $body, array $headers = []): ?array
    {
        if (!function_exists('curl_init')) {
            log_message('error', 'reCAPTCHA HTTP: extensión curl no disponible');

            return null;
        }

        $verifySsl = $this->sslVerifyPeer();
        $ch        = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            log_message('error', 'reCAPTCHA HTTP: ' . ($curlErr !== '' ? $curlErr : 'sin respuesta'));

            return null;
        }

        return ['body' => (string) $response, 'code' => $httpCode];
    }
}

<?php

namespace App\Services\Storage;

use Config\Storage as StorageConfig;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Cloud\Storage\StorageClient;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Cliente Google Cloud Storage (bucket privado).
 */
class GcsStorageClient
{
    private StorageClient $client;

    private \Google\Cloud\Storage\Bucket $bucket;

    private string $prefix;

    public function __construct(?StorageConfig $config = null)
    {
        $config ??= config('Storage');
        $keyFile = $this->resolveKeyFilePath($config);

        $clientOptions = [
            'projectId'   => $config->gcsProjectId,
            'keyFilePath' => $keyFile,
        ];
        // Solo en local/WAMP si cURL falla por SSL (certificado). En hosting debe ser false.
        if (filter_var(env('storage.gcsInsecureSsl', false), FILTER_VALIDATE_BOOLEAN)) {
            try {
                $clientOptions['httpHandler'] = HttpHandlerFactory::build(
                    new GuzzleClient(['verify' => false])
                );
            } catch (\Throwable $e) {
                log_message('warning', 'GCS httpHandler: ' . $e->getMessage() . ' — use storage.gcsInsecureSsl=false en producción.');
            }
        }
        $this->client = new StorageClient($clientOptions);
        $this->bucket = $this->client->bucket($config->gcsBucket);
        $this->prefix = trim($config->gcsPrefix, '/');
    }

    public function upload(string $storageKey, string $sourcePath, string $contentType): void
    {
        $object = $this->bucket->upload(
            fopen($sourcePath, 'rb'),
            [
                'name'        => $this->objectName($storageKey),
                'contentType' => $contentType,
                'metadata'    => ['cacheControl' => 'private, max-age=0'],
            ]
        );

        if (!$object->exists()) {
            throw new \RuntimeException('No se pudo subir el archivo a Cloud Storage');
        }
    }

    public function delete(string $storageKey): void
    {
        $object = $this->bucket->object($this->objectName($storageKey));
        if ($object->exists()) {
            $object->delete();
        }
    }

    public function exists(string $storageKey): bool
    {
        return $this->bucket->object($this->objectName($storageKey))->exists();
    }

    /**
     * Descarga a archivo temporal para enviar al navegador.
     */
    public function downloadToTemp(string $storageKey): ?string
    {
        $object = $this->bucket->object($this->objectName($storageKey));
        if (!$object->exists()) {
            return null;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'gcs_');
        if ($tmp === false) {
            return null;
        }

        $object->downloadToFile($tmp);

        return $tmp;
    }

    /**
     * Enlace firmado para compartir con pacientes (bucket sigue privado).
     */
    public function signedUrl(string $storageKey, int $ttlSeconds): string
    {
        $object = $this->bucket->object($this->objectName($storageKey));

        return $object->signedUrl(
            new \DateTime('+' . $ttlSeconds . ' seconds'),
            ['version' => 'v4']
        );
    }

    private function objectName(string $storageKey): string
    {
        $storageKey = ltrim(str_replace('\\', '/', $storageKey), '/');
        if ($this->prefix === '') {
            return $storageKey;
        }

        return $this->prefix . '/' . $storageKey;
    }

    private function resolveKeyFilePath(StorageConfig $config): string
    {
        $candidates = array_filter([
            env('GOOGLE_APPLICATION_CREDENTIALS'),
            $config->gcsKeyFile,
            WRITEPATH . 'credentials/gcp-storage.json',
        ]);

        foreach ($candidates as $path) {
            $path = (string) $path;
            if ($path === '') {
                continue;
            }
            if (!is_file($path) && is_file(ROOTPATH . $path)) {
                $path = ROOTPATH . $path;
            }
            if (is_file($path)) {
                return $path;
            }
        }

        throw new \RuntimeException('No se encontró el JSON de la cuenta de servicio de GCP (GOOGLE_APPLICATION_CREDENTIALS).');
    }
}

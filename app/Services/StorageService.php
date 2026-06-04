<?php

namespace App\Services;

use App\Services\Storage\GcsStorageClient;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Storage as StorageConfig;

/**
 * Almacenamiento local o Google Cloud Storage.
 * Rutas en BD: "uploads/perfil/x.jpg" (público) o "private/credenciales/1/doc.pdf".
 */
class StorageService
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_PRIVATE = 'private';

    protected StorageConfig $config;

    protected ?GcsStorageClient $gcs = null;

    public function __construct(?StorageConfig $config = null)
    {
        $this->config = $config ?? config('Storage');
    }

    public function usesGcs(): bool
    {
        return strtolower($this->config->driver) === 'gcs';
    }

    /**
     * @return array{key: string, nombre: string}|array{error: string}
     */
    public function putUploadedFile(
        UploadedFile $file,
        string $visibility,
        string $folder,
        array $allowedMimes,
        int $maxBytes
    ): array {
        if (!$file->isValid() || $file->hasMoved()) {
            return ['error' => 'Archivo no válido'];
        }

        $mime = (string) $file->getMimeType();
        if (!in_array($mime, $allowedMimes, true)) {
            return ['error' => 'Formato no permitido'];
        }

        if ($file->getSize() > $maxBytes) {
            $mb = (int) round($maxBytes / 1024 / 1024);

            return ['error' => "El archivo no debe superar {$mb} MB"];
        }

        $folder = trim($folder, '/');
        $safeClient = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientName()) ?: 'archivo';
        $ext = $file->getClientExtension() ?: pathinfo($safeClient, PATHINFO_EXTENSION) ?: 'bin';
        $newName = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $key = $this->buildKey($visibility, $folder, $newName);

        if ($this->usesGcs() && $visibility === self::VISIBILITY_PRIVATE) {
            try {
                $this->gcsClient()->upload($key, $file->getTempName(), $mime);
            } catch (\Throwable $e) {
                log_message('error', 'GCS upload: ' . $e->getMessage());

                return ['error' => 'Error al guardar en almacenamiento en la nube'];
            }

            return [
                'key'    => $key,
                'nombre' => $file->getClientName(),
            ];
        }

        $absoluteDir = $this->absoluteDir($visibility, $folder);
        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0750, true) && !is_dir($absoluteDir)) {
            return ['error' => 'No se pudo crear la carpeta de almacenamiento'];
        }

        if (!$file->move($absoluteDir, $newName)) {
            return ['error' => 'Error al guardar el archivo'];
        }

        return [
            'key'    => $key,
            'nombre' => $file->getClientName(),
        ];
    }

    public function delete(?string $storageKey): void
    {
        if ($storageKey === null || $storageKey === '') {
            return;
        }

        if ($this->usesGcs() && $this->isPrivateKey($storageKey)) {
            try {
                $this->gcsClient()->delete($storageKey);
            } catch (\Throwable $e) {
                log_message('error', 'GCS delete: ' . $e->getMessage());
            }

            return;
        }

        $path = $this->absolutePath($storageKey);
        if ($path !== null && is_file($path)) {
            @unlink($path);
        }
    }

    public function exists(?string $storageKey): bool
    {
        if ($storageKey === null || $storageKey === '') {
            return false;
        }

        if ($this->usesGcs() && $this->isPrivateKey($storageKey)) {
            try {
                return $this->gcsClient()->exists($storageKey);
            } catch (\Throwable $e) {
                log_message('error', 'GCS exists: ' . $e->getMessage());

                return false;
            }
        }

        $path = $this->absolutePath($storageKey);

        return $path !== null && is_file($path);
    }

    /**
     * Enlace firmado (solo GCS + clave private/).
     */
    public function signedUrl(?string $storageKey, ?int $ttlSeconds = null): ?string
    {
        if (!$this->usesGcs() || !$this->isPrivateKey($storageKey)) {
            return null;
        }

        $ttl = $ttlSeconds ?? $this->config->gcsSignedUrlSeconds;

        try {
            return $this->gcsClient()->signedUrl($storageKey, $ttl);
        } catch (\Throwable $e) {
            log_message('error', 'GCS signedUrl: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Copia el archivo a un temporal (para adjuntos de correo, etc.).
     */
    public function downloadToTemp(?string $storageKey): ?string
    {
        if ($storageKey === null || $storageKey === '') {
            return null;
        }

        if ($this->usesGcs() && $this->isPrivateKey($storageKey)) {
            try {
                return $this->gcsClient()->downloadToTemp($storageKey);
            } catch (\Throwable $e) {
                log_message('error', 'GCS downloadToTemp: ' . $e->getMessage());

                return null;
            }
        }

        $path = $this->absolutePath($storageKey);
        if ($path === null || !is_file($path)) {
            return null;
        }

        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'doc_' . bin2hex(random_bytes(6)) . '_' . basename($path);
        if (!@copy($path, $tmp)) {
            return null;
        }

        return $tmp;
    }

    /**
     * Respuesta de descarga (local o GCS).
     */
    public function downloadResponse(string $storageKey, string $downloadName): ?ResponseInterface
    {
        if ($this->usesGcs() && $this->isPrivateKey($storageKey)) {
            try {
                $tmp = $this->gcsClient()->downloadToTemp($storageKey);
                if ($tmp === null || !is_file($tmp)) {
                    return null;
                }
                $mime = mime_content_type($tmp) ?: 'application/octet-stream';
                $response = service('response')->download($tmp, null)->setFileName($downloadName)->setContentType($mime);
                register_shutdown_function(static function () use ($tmp): void {
                    if (is_file($tmp)) {
                        @unlink($tmp);
                    }
                });

                return $response;
            } catch (\Throwable $e) {
                log_message('error', 'GCS download: ' . $e->getMessage());

                return null;
            }
        }

        $path = $this->absolutePath($storageKey);
        if ($path === null || !is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';

        return service('response')->download($path, null)->setFileName($downloadName)->setContentType($mime);
    }

    /**
     * Ruta absoluta en disco local (null si el archivo está solo en GCS).
     */
    public function absolutePath(?string $storageKey): ?string
    {
        if ($storageKey === null || $storageKey === '') {
            return null;
        }

        if ($this->usesGcs() && $this->isPrivateKey($storageKey)) {
            return null;
        }

        $storageKey = str_replace('\\', '/', $storageKey);

        if (str_starts_with($storageKey, 'private/')) {
            $relative = substr($storageKey, strlen('private/'));

            return $this->privateBasePath() . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        }

        if (!str_contains($storageKey, '..')) {
            return FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $storageKey);
        }

        return null;
    }

    public function publicUrl(?string $storageKey): ?string
    {
        if ($storageKey === null || $storageKey === '') {
            return null;
        }

        if ($this->isPrivateKey($storageKey)) {
            return null;
        }

        return base_url($storageKey);
    }

    public function isPrivateKey(?string $storageKey): bool
    {
        return $storageKey !== null && str_starts_with($storageKey, 'private/');
    }

    protected function gcsClient(): GcsStorageClient
    {
        if ($this->gcs === null) {
            $this->gcs = new GcsStorageClient($this->config);
        }

        return $this->gcs;
    }

    protected function buildKey(string $visibility, string $folder, string $filename): string
    {
        $folder = trim($folder, '/');

        if ($visibility === self::VISIBILITY_PRIVATE) {
            return 'private/' . ($folder !== '' ? $folder . '/' : '') . $filename;
        }

        $publicRoot = trim($this->config->publicRoot, '/');

        return $publicRoot . '/' . ($folder !== '' ? $folder . '/' : '') . $filename;
    }

    protected function absoluteDir(string $visibility, string $folder): string
    {
        $folder = trim($folder, '/');
        if ($visibility === self::VISIBILITY_PRIVATE) {
            $base = $this->privateBasePath();

            return $folder !== '' ? $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder) : $base;
        }

        $base = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . trim($this->config->publicRoot, '/');

        return $folder !== '' ? $base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folder) : $base;
    }

    protected function privateBasePath(): string
    {
        return rtrim(WRITEPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim($this->config->privateRoot, '/'));
    }
}

<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Almacenamiento: disco local (hosting) o Google Cloud Storage.
 */
class Storage extends BaseConfig
{
    /** local | gcs */
    public string $driver = 'local';

    public string $gcsBucket = 'nutrinext-document';

    public string $gcsProjectId = 'nutrinext-dev';

    /** Ruta al JSON de la cuenta de servicio (fuera de Git). */
    public string $gcsKeyFile = 'writable/credentials/gcp-storage.json';

    /** Prefijo opcional dentro del bucket, ej. prod o dev */
    public string $gcsPrefix = '';

    /** Vigencia enlaces firmados para pacientes (segundos). 7 días */
    public int $gcsSignedUrlSeconds = 604_800;

    /**
     * Carpeta pública bajo la raíz del sitio (FCPATH).
     * Fotos de perfil, imágenes web, etc.
     */
    public string $publicRoot = 'uploads';

    /**
     * Carpeta privada fuera del acceso web directo (WRITEPATH).
     * PDFs, credenciales, documentos clínicos.
     */
    public string $privateRoot = 'storage/private';

    /** Tamaño máximo documentos privados (bytes). 5 MB */
    public int $maxDocumentBytes = 5_242_880;

    /** Tamaño máximo imágenes públicas (bytes). 2 MB */
    public int $maxImageBytes = 2_097_152;

    /** @var list<string> */
    public array $documentMimeTypes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /** @var list<string> */
    public array $imageMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->driver = (string) env('storage.driver', $this->driver);
        $this->publicRoot = trim((string) env('storage.publicRoot', $this->publicRoot), '/');
        $this->privateRoot = trim((string) env('storage.privateRoot', $this->privateRoot), '/');
        $this->maxDocumentBytes = (int) env('storage.maxDocumentMb', 5) * 1024 * 1024;
        $this->maxImageBytes = (int) env('storage.maxImageMb', 2) * 1024 * 1024;
        $this->gcsBucket = (string) env('GCS_BUCKET', $this->gcsBucket);
        $this->gcsProjectId = (string) env('GCS_PROJECT_ID', $this->gcsProjectId);
        $this->gcsKeyFile = (string) env('storage.gcsKeyFile', $this->gcsKeyFile);
        $this->gcsPrefix = trim((string) env('GCS_PREFIX', $this->gcsPrefix), '/');
        $this->gcsSignedUrlSeconds = (int) env('GCS_SIGNED_URL_SECONDS', $this->gcsSignedUrlSeconds);
    }
}

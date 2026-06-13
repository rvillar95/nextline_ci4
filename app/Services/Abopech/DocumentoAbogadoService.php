<?php

declare(strict_types=1);

namespace App\Services\Abopech;

use App\Models\Abopech\DocumentoGcsModel;
use App\Services\StorageService;
use CodeIgniter\HTTP\Files\UploadedFile;
use Config\Storage as StorageConfig;

final class DocumentoAbogadoService
{
    public function storeEstudioDocument(
        UploadedFile $file,
        int $abogadoId,
        int $usuarioId,
        string $nombreLegible,
        ?string $descripcion = null
    ): array {
        $storage = new StorageService();
        $config  = config('Storage');
        $maxBytes = (int) ($config->maxDocumentBytes ?? 5_242_880);
        $allowed  = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
        ];

        $folder = 'abopech/abogados/' . $abogadoId . '/estudios';
        $result = $storage->putUploadedFile(
            $file,
            StorageService::VISIBILITY_PRIVATE,
            $folder,
            $allowed,
            $maxBytes
        );

        if (isset($result['error'])) {
            return $result;
        }

        $bucket = $this->resolveBucket($config);
        $docModel = new DocumentoGcsModel();
        $docId = $docModel->insert([
            'abogado_id'            => $abogadoId,
            'nombre'                => $nombreLegible,
            'descripcion'           => $descripcion,
            'bucket'                => $bucket,
            'object_key'            => $result['key'],
            'content_type'          => (string) $file->getMimeType(),
            'tamano_bytes'          => (int) $file->getSize(),
            'subido_por_usuario_id' => $usuarioId,
        ], true);

        return ['documento_id' => (int) $docId, 'key' => $result['key']];
    }

    private function resolveBucket(StorageConfig $config): string
    {
        if (strtolower($config->driver) === 'gcs') {
            return (string) ($config->gcsBucket ?? env('GCS_BUCKET', 'local'));
        }

        return 'local';
    }
}

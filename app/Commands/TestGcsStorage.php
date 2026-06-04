<?php

namespace App\Commands;

use App\Services\Storage\GcsStorageClient;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Prueba conexión al bucket GCS (php spark storage:test-gcs).
 */
class TestGcsStorage extends BaseCommand
{
    protected $group       = 'Storage';
    protected $name        = 'storage:test-gcs';
    protected $description = 'Verifica credenciales y bucket de Google Cloud Storage';
    protected $usage       = 'storage:test-gcs';

    public function run(array $params)
    {
        try {
            $gcs = new GcsStorageClient();
            $config = config('Storage');
            $testKey = 'private/_healthcheck/' . date('Y-m-d') . '_test.txt';
            $tmp = WRITEPATH . 'gcs_test_' . uniqid('', true) . '.txt';
            file_put_contents($tmp, 'NutriNext GCS OK ' . date('c'));

            $gcs->upload($testKey, $tmp, 'text/plain');
            @unlink($tmp);

            if (!$gcs->exists($testKey)) {
                CLI::error('Subida falló: el objeto no existe en el bucket.');

                return;
            }

            $url = $gcs->signedUrl($testKey, 300);
            $gcs->delete($testKey);

            CLI::write('Conexión OK', 'green');
            CLI::write('Bucket: ' . $config->gcsBucket);
            CLI::write('Proyecto: ' . $config->gcsProjectId);
            CLI::write('URL firmada de prueba (5 min): ' . $url);
        } catch (\Throwable $e) {
            CLI::error($e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\DocumentoEnvio;
use App\Models\Paciente;
use Config\Email as EmailConfig;
use Config\Services;

/**
 * Envía documentos al correo del paciente (adjuntos y/o enlaces firmados).
 */
class DocumentoEnvioService
{
    protected const MAX_ADJUNTO_BYTES = 5_242_880; // 5 MB por archivo
    protected const MAX_TOTAL_ADJUNTOS = 12_582_912; // 12 MB total

    public function enviarAlPaciente(
        array $documentoIds,
        int $nutricionistaId,
        string $nombreNutricionista,
        ?string $mensajePersonal = null
    ): array {
        $documentoIds = array_values(array_unique(array_filter(array_map('intval', $documentoIds))));
        if ($documentoIds === []) {
            return ['ok' => false, 'error' => 'Seleccione al menos un documento.'];
        }

        $documentoModel = new Documento();
        $docs = [];
        $pacienteId = null;

        foreach ($documentoIds as $id) {
            $doc = $documentoModel->find($id);
            if (!$doc || ($doc->estado ?? '') !== 'A') {
                return ['ok' => false, 'error' => 'Uno o más documentos no están disponibles.'];
            }
            if (!$this->puedeEnviarDocumento($doc, $nutricionistaId)) {
                return ['ok' => false, 'error' => 'No tiene permiso para enviar uno o más documentos seleccionados.'];
            }
            if ($pacienteId === null) {
                $pacienteId = (int) $doc->paciente_id;
            } elseif ((int) $doc->paciente_id !== $pacienteId) {
                return ['ok' => false, 'error' => 'Todos los documentos deben ser del mismo paciente.'];
            }
            $docs[] = $doc;
        }

        $pacienteModel = new Paciente();
        $paciente = $pacienteModel->find($pacienteId);
        if (!$paciente || !$this->puedeEnviarPaciente($paciente, $nutricionistaId)) {
            return ['ok' => false, 'error' => 'Paciente no encontrado.'];
        }

        $emailPaciente = trim((string) ($paciente->email ?? ''));
        if ($emailPaciente === '' || !filter_var($emailPaciente, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok'    => false,
                'error' => 'El paciente no tiene un correo válido. Actualice su ficha antes de enviar.',
            ];
        }

        $nombrePaciente = trim(($paciente->nombre ?? '') . ' ' . ($paciente->apellido ?? ''));
        $mensajePersonal = trim((string) ($mensajePersonal ?? ''));
        if (strlen($mensajePersonal) > 2000) {
            $mensajePersonal = substr($mensajePersonal, 0, 2000);
        }

        $storage = new StorageService();
        $itemsEmail = [];
        $tempFiles = [];
        $totalAdjuntos = 0;

        foreach ($docs as $doc) {
            $item = [
                'titulo'      => $doc->titulo,
                'tipo'        => $this->etiquetaTipo($doc->tipo_documento),
                'fecha'       => date('d/m/Y', strtotime($doc->fecha_documento)),
                'descripcion' => $doc->descripcion ?? '',
                'contenido'   => $doc->contenido ?? '',
                'enlace'      => null,
                'adjunto'     => false,
            ];

            if (!empty($doc->archivo_ruta)) {
                $enlace = $storage->signedUrl($doc->archivo_ruta);
                $item['enlace'] = $enlace ?? base_url('dashboard/documento/' . $doc->id . '/descargar');

                $tmp = $storage->downloadToTemp($doc->archivo_ruta);
                if ($tmp !== null && is_file($tmp)) {
                    $size = (int) filesize($tmp);
                    if ($size > 0 && $size <= self::MAX_ADJUNTO_BYTES && ($totalAdjuntos + $size) <= self::MAX_TOTAL_ADJUNTOS) {
                        $tempFiles[] = $tmp;
                        $item['adjunto'] = true;
                        $item['_tmp'] = $tmp;
                        $item['_nombre'] = $doc->archivo_nombre ?? ($doc->titulo . '.pdf');
                        $totalAdjuntos += $size;
                    } else {
                        @unlink($tmp);
                    }
                }
            }

            $itemsEmail[] = $item;
        }

        $mensaje = view('emails/documentos_paciente', [
            'nombrePaciente'      => $nombrePaciente,
            'nombreNutricionista' => $nombreNutricionista,
            'mensajePersonal'     => $mensajePersonal,
            'documentos'          => $itemsEmail,
        ]);

        $emailConfig = config(EmailConfig::class);
        $email = Services::email();
        $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName);
        $email->setTo($emailPaciente);
        $email->setSubject('Documentos de su consulta nutricional — ' . $nombreNutricionista);

        foreach ($itemsEmail as $item) {
            if (!empty($item['_tmp']) && is_file($item['_tmp'])) {
                $email->attach($item['_tmp'], 'attachment', $item['_nombre'] ?? 'documento.pdf');
            }
        }

        $email->setMessage($mensaje);
        $enviado = $email->send();

        foreach ($tempFiles as $tmp) {
            if (is_file($tmp)) {
                @unlink($tmp);
            }
        }

        $envioModel = new DocumentoEnvio();
        $idsParaLog = array_map(static fn ($d) => (int) $d->id, $docs);

        if (!$enviado) {
            $errorMail = 'No se pudo enviar el correo. Revise la configuración SMTP.';
            log_message('error', 'DocumentoEnvioService: ' . $email->printDebugger(['headers']));
            $envioModel->registrar(
                $pacienteId,
                $nutricionistaId > 0 ? $nutricionistaId : null,
                $emailPaciente,
                $mensajePersonal,
                $idsParaLog,
                'fallido',
                $errorMail
            );

            return ['ok' => false, 'error' => $errorMail];
        }

        foreach ($docs as $doc) {
            $documentoModel->marcarComoEnviado((int) $doc->id, 'email');
        }

        $envioId = $envioModel->registrar(
            $pacienteId,
            $nutricionistaId > 0 ? $nutricionistaId : null,
            $emailPaciente,
            $mensajePersonal,
            $idsParaLog,
            'enviado'
        );

        log_message('info', 'Documentos enviados por email a ' . $emailPaciente . ' ids=' . implode(',', $documentoIds));

        return [
            'ok'      => true,
            'mensaje' => count($docs) === 1
                ? 'Documento enviado por correo a ' . $emailPaciente
                : count($docs) . ' documentos enviados por correo a ' . $emailPaciente,
            'email'   => $emailPaciente,
            'envio_id' => $envioId,
        ];
    }

    protected function etiquetaTipo(?string $tipo): string
    {
        return match ($tipo) {
            'pauta_nutricional' => 'Pauta nutricional',
            'receta'            => 'Receta',
            'informe'           => 'Informe',
            'consentimiento'    => 'Consentimiento',
            'otro'              => 'Otro',
            default             => 'Documento',
        };
    }

    private function esSuperAdmin(): bool
    {
        $usuario = session()->get('usuario');

        return (int) ($usuario['poder'] ?? 0) === 3;
    }

    private function puedeEnviarPaciente($paciente, int $nutricionistaId): bool
    {
        if ($this->esSuperAdmin()) {
            return (bool) $paciente;
        }

        return $paciente && (int) ($paciente->nutricionista_id ?? 0) === $nutricionistaId;
    }

    private function puedeEnviarDocumento($documento, int $nutricionistaId): bool
    {
        if ($this->esSuperAdmin()) {
            return (bool) $documento;
        }

        if (!$documento) {
            return false;
        }

        $docNutri = (int) ($documento->nutricionista_id ?? 0);
        if ($docNutri > 0) {
            return $docNutri === $nutricionistaId;
        }

        $paciente = (new Paciente())->find((int) ($documento->paciente_id ?? 0));

        return $this->puedeEnviarPaciente($paciente, $nutricionistaId);
    }
}

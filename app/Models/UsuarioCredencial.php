<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioCredencial extends Model
{
    protected $table            = 'usuario_credencial';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'usuario_id', 'tipo', 'nombre', 'institucion', 'anio', 'descripcion',
        'archivo_ruta', 'archivo_nombre', 'orden', 'visible_web',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    public const TIPOS = ['titulo', 'diploma', 'curso', 'certificado', 'otro'];

    public const TIPOS_LABEL = [
        'titulo'      => 'Títulos',
        'diploma'     => 'Diplomas',
        'curso'       => 'Cursos',
        'certificado' => 'Certificados',
        'otro'        => 'Otros',
    ];

    /**
     * Credenciales de un usuario para la web pública.
     */
    public function getPorUsuarioWeb(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
            ->where('visible_web', 'S')
            ->orderBy('orden', 'ASC')
            ->orderBy('anio', 'DESC')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    /**
     * Todas las credenciales (dashboard).
     */
    public function getPorUsuario(int $usuarioId): array
    {
        return $this->where('usuario_id', $usuarioId)
            ->orderBy('orden', 'ASC')
            ->orderBy('anio', 'DESC')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    /**
     * Agrupa credenciales por tipo para la vista pública.
     *
     * @return array<string, list<array>>
     */
    public function agruparPorTipo(array $credenciales): array
    {
        $grupos = [];
        foreach (self::TIPOS as $tipo) {
            $grupos[$tipo] = [];
        }
        foreach ($credenciales as $c) {
            $tipo = $c['tipo'] ?? 'otro';
            if (!isset($grupos[$tipo])) {
                $grupos[$tipo] = [];
            }
            $grupos[$tipo][] = $c;
        }
        return array_filter($grupos, static fn ($items) => count($items) > 0);
    }
}

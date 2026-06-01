<?php

namespace App\Models;

use CodeIgniter\Model;

class ServicioNutrinext extends Model
{
    protected $table            = 'servicio_nutrinext';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'codigo', 'nombre', 'categoria', 'icono', 'color',
        'descripcion_corta', 'descripcion_larga', 'beneficios', 'incluye', 'etiquetas',
        'modulo_id', 'ruta_dashboard', 'requiere_configuracion', 'nota_configuracion',
        'visible_web', 'visible_catalogo', 'destacado', 'orden', 'estado',
        'documentacion_url', 'video_url',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'fcreacion';
    protected $updatedField  = 'factualizacion';

    /** Validacion en ServicioNutrinextController + Config\Validation::formServicioNutrinext */
    protected $validationRules = [];

    public static function categorias(): array
    {
        return [
            'clinica'        => 'Clinica / consulta',
            'comunicacion'   => 'Comunicacion',
            'facturacion'    => 'Facturacion',
            'admin'          => 'Administracion',
            'integraciones'  => 'Integraciones',
        ];
    }

    public function getCatalogo(string $estado = 'A'): array
    {
        return $this->where('estado', $estado)
            ->where('visible_catalogo', 'S')
            ->orderBy('categoria', 'ASC')
            ->orderBy('orden', 'ASC')
            ->orderBy('nombre', 'ASC')
            ->findAll();
    }

    public function getParaWeb(?string $categoria = null): array
    {
        $builder = $this->where('estado', 'A')->where('visible_web', 'S');
        if ($categoria !== null && $categoria !== '') {
            $builder->where('categoria', $categoria);
        }

        return $builder->orderBy('orden', 'ASC')->orderBy('nombre', 'ASC')->findAll();
    }

    /**
     * @return array<string, list<object>>
     */
    public function getParaWebAgrupados(?string $categoria = null): array
    {
        $agrupados = [];
        foreach ($this->getParaWeb($categoria) as $item) {
            $agrupados[$item->categoria][] = $item;
        }

        return $agrupados;
    }

    public function getPorCodigoWeb(string $codigo): ?object
    {
        return $this->where('estado', 'A')
            ->where('visible_web', 'S')
            ->where('codigo', $codigo)
            ->first();
    }

    public function getDestacadosWeb(int $limit = 6): array
    {
        $destacados = $this->where('estado', 'A')
            ->where('visible_web', 'S')
            ->where('destacado', 'S')
            ->orderBy('orden', 'ASC')
            ->orderBy('nombre', 'ASC')
            ->findAll($limit);

        if (count($destacados) >= $limit) {
            return $destacados;
        }

        $ids = array_map(static fn ($r) => (int) $r->id, $destacados);
        $faltan = $limit - count($destacados);
        $extra  = $this->where('estado', 'A')
            ->where('visible_web', 'S')
            ->orderBy('orden', 'ASC')
            ->orderBy('nombre', 'ASC')
            ->findAll($limit + count($ids));

        foreach ($extra as $row) {
            if (in_array((int) $row->id, $ids, true)) {
                continue;
            }
            $destacados[] = $row;
            if (count($destacados) >= $limit) {
                break;
            }
        }

        return $destacados;
    }

    /**
     * @return list<string>
     */
    public static function parseLineas(?string $texto): array
    {
        if ($texto === null || trim($texto) === '') {
            return [];
        }
        $texto = str_replace('|', "\n", $texto);
        $lineas = preg_split('/\r\n|\r|\n/', $texto) ?: [];

        return array_values(array_filter(array_map('trim', $lineas)));
    }

    public function generarCodigo(string $nombre, ?int $excluirId = null): string
    {
        $codigo = strtolower(trim($nombre));
        $codigo = strtr($codigo, [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ñ' => 'n',
        ]);
        $codigo = preg_replace('/[^a-z0-9]+/', '-', $codigo);
        $codigo = trim($codigo, '-');
        if ($codigo === '') {
            $codigo = 'servicio';
        }

        $original = $codigo;
        $n        = 1;
        while ($this->codigoExiste($codigo, $excluirId)) {
            $codigo = $original . '-' . $n;
            $n++;
        }

        return $codigo;
    }

    protected function codigoExiste(string $codigo, ?int $excluirId): bool
    {
        $builder = $this->where('codigo', $codigo);
        if ($excluirId) {
            $builder->where('id !=', $excluirId);
        }

        return $builder->first() !== null;
    }

    public function syncPaquetes(int $servicioId, array $paqueteIds): void
    {
        $db = \Config\Database::connect();
        $db->table('servicio_nutrinext_paquete')->where('servicio_nutrinext_id', $servicioId)->delete();

        foreach ($paqueteIds as $paqueteId) {
            $paqueteId = (int) $paqueteId;
            if ($paqueteId <= 0) {
                continue;
            }
            $db->table('servicio_nutrinext_paquete')->insert([
                'servicio_nutrinext_id' => $servicioId,
                'paquete_id'            => $paqueteId,
                'incluido'              => 'S',
                'fcreacion'             => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function getPaqueteIds(int $servicioId): array
    {
        $rows = \Config\Database::connect()
            ->table('servicio_nutrinext_paquete')
            ->select('paquete_id')
            ->where('servicio_nutrinext_id', $servicioId)
            ->where('incluido', 'S')
            ->get()
            ->getResultArray();

        return array_map('intval', array_column($rows, 'paquete_id'));
    }
}

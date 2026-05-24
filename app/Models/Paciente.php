<?php

namespace App\Models;

use CodeIgniter\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nutricionista_id', 'tipo_paciente', 'nombre', 'apellido', 'rut_dni', 'fecha_nacimiento',
        'genero', 'telefono', 'email', 'direccion', 'region_id', 'comuna_id', 'comuna', 'region',
        'peso_inicial', 'altura', 'imc_inicial', 'objetivo', 'alergias', 'medicamentos',
        'condiciones_medicas', 'observaciones', 'estado'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'tipo_paciente' => 'required|in_list[particular,convenio,seguro,fonasa,isapre,otro]',
        'nombre' => 'required|string|max_length[100]',
        'apellido' => 'required|string|max_length[100]',
        'rut_dni' => 'permit_empty|string|max_length[20]',
        'fecha_nacimiento' => 'permit_empty|valid_date',
        'genero' => 'permit_empty|in_list[M,F,O]',
        'telefono' => 'permit_empty|string|max_length[50]',
        'email' => 'permit_empty|valid_email|max_length[150]',
        'peso_inicial' => 'permit_empty|decimal',
        'altura' => 'permit_empty|decimal'
    ];

    protected $validationMessages = [
        'tipo_paciente' => [
            'required' => 'El tipo de paciente es obligatorio.',
            'in_list' => 'El tipo de paciente debe ser: particular, convenio, seguro, fonasa, isapre u otro.'
        ],
        'nombre' => [
            'required' => 'El nombre del paciente es obligatorio.',
            'string' => 'El nombre debe ser una cadena de texto.',
            'max_length' => 'El nombre no puede exceder de 100 caracteres.'
        ],
        'apellido' => [
            'required' => 'El apellido del paciente es obligatorio.',
            'string' => 'El apellido debe ser una cadena de texto.',
            'max_length' => 'El apellido no puede exceder de 100 caracteres.'
        ],
        'rut_dni' => [
            'max_length' => 'El RUT/DNI no puede exceder de 20 caracteres.'
        ],
        'fecha_nacimiento' => [
            'valid_date' => 'La fecha de nacimiento debe ser una fecha válida.'
        ],
        'genero' => [
            'in_list' => 'El género debe ser: M (Masculino), F (Femenino) u O (Otro).'
        ],
        'telefono' => [
            'max_length' => 'El teléfono no puede exceder de 50 caracteres.'
        ],
        'email' => [
            'valid_email' => 'El email debe tener un formato válido.',
            'max_length' => 'El email no puede exceder de 150 caracteres.'
        ],
        'peso_inicial' => [
            'decimal' => 'El peso debe ser un número decimal válido.'
        ],
        'altura' => [
            'decimal' => 'La altura debe ser un número decimal válido.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obtener paciente completo con relaciones
     */
    public function getPacienteCompleto($id)
    {
        $paciente = $this->find($id);
        if (!$paciente) {
            return null;
        }

        // Cargar región y comuna si existen
        if ($paciente->region_id) {
            $regionModel = new Region();
            $paciente->region_data = $regionModel->find($paciente->region_id);
        }
        if ($paciente->comuna_id) {
            $comunaModel = new Comuna();
            $paciente->comuna_data = $comunaModel->find($paciente->comuna_id);
        }

        // Cargar nutricionista si existe
        if ($paciente->nutricionista_id) {
            $usuarioModel = new Usuario();
            $paciente->nutricionista = $usuarioModel->find($paciente->nutricionista_id);
        }

        return $paciente;
    }

    /**
     * Calcular IMC
     */
    public function calcularIMC($peso, $altura)
    {
        if ($peso > 0 && $altura > 0) {
            $alturaMetros = $altura / 100; // Convertir cm a metros
            return round($peso / ($alturaMetros * $alturaMetros), 2);
        }
        return null;
    }

    /**
     * Obtener pacientes por nutricionista
     */
    public function getPacientesPorNutricionista($nutricionistaId, $estado = 'A')
    {
        return $this->where('nutricionista_id', $nutricionistaId)
            ->where('estado', $estado)
            ->orderBy('nombre', 'ASC')
            ->orderBy('apellido', 'ASC')
            ->findAll();
    }

    /**
     * Obtener pacientes para select (DataTable)
     */
    public function getPacientesSelect($nutricionistaId = null)
    {
        $builder = $this->select('id, CONCAT(nombre, " ", apellido) as nombre_completo, rut_dni, email, telefono')
            ->where('estado', 'A');

        if ($nutricionistaId) {
            $builder->where('nutricionista_id', $nutricionistaId);
        }

        return $builder->orderBy('nombre', 'ASC')
            ->orderBy('apellido', 'ASC')
            ->findAll();
    }

    /**
     * Normaliza RUT/DNI para comparación (solo alfanumérico, DV en mayúscula).
     */
    public static function normalizarRutDni(?string $rut): string
    {
        if ($rut === null || $rut === '') {
            return '';
        }

        return strtoupper(preg_replace('/[^0-9kK]/', '', trim($rut)));
    }

    /**
     * Valida RUT chileno (módulo 11). Para DNI extranjero corto devuelve true si tiene al menos 3 caracteres alfanuméricos.
     */
    public static function esRutDniValido(?string $rut): bool
    {
        $norm = self::normalizarRutDni($rut);
        if ($norm === '') {
            return false;
        }

        if (strlen($norm) < 3) {
            return false;
        }

        // Formato chileno: cuerpo numérico + dígito verificador
        if (preg_match('/^(\d{7,8})([\dK])$/', $norm, $m)) {
            $dv = $m[2];
            $body = $m[1];
            $sum = 0;
            $serie = [2, 3, 4, 5, 6, 7];
            $len = strlen($body);
            for ($i = 0; $i < $len; $i++) {
                $sum += (int) $body[$len - 1 - $i] * $serie[$i % 6];
            }
            $rest = $sum % 11;
            $expected = 11 - $rest;
            if ($expected === 11) {
                $expected = '0';
            } elseif ($expected === 10) {
                $expected = 'K';
            } else {
                $expected = (string) $expected;
            }

            return $expected === $dv;
        }

        // DNI / documento extranjero (sin algoritmo chileno)
        return strlen($norm) >= 3;
    }

    /**
     * ¿Ya existe un paciente activo con el mismo RUT/DNI para este nutricionista?
     * Otro nutricionista puede tener el mismo RUT (misma persona, otra ficha).
     */
    public function existeRutParaNutricionista(string $rutNormalizado, int $nutricionistaId, ?int $excluirPacienteId = null): bool
    {
        if ($rutNormalizado === '' || strlen($rutNormalizado) < 3) {
            return false;
        }

        $builder = $this->where('estado', 'A')
            ->where('nutricionista_id', $nutricionistaId)
            ->where(
                "REPLACE(REPLACE(REPLACE(IFNULL(rut_dni,''),'.',''),'-',''),' ','') = ",
                $rutNormalizado
            );

        if ($excluirPacienteId) {
            $builder->where('id !=', $excluirPacienteId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Buscar pacientes por término
     */
    public function buscarPacientes($termino, $nutricionistaId = null)
    {
        $builder = $this->groupStart()
            ->like('nombre', $termino)
            ->orLike('apellido', $termino)
            ->orLike('rut_dni', $termino)
            ->orLike('email', $termino)
            ->groupEnd()
            ->where('estado', 'A');

        if ($nutricionistaId) {
            $builder->where('nutricionista_id', $nutricionistaId);
        }

        return $builder->orderBy('nombre', 'ASC')
            ->orderBy('apellido', 'ASC')
            ->findAll();
    }
}

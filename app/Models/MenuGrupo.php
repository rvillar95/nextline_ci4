<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuGrupo extends Model
{
    protected $table = 'menu_grupo';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['slug', 'etiqueta', 'orden', 'estado'];
    protected $useTimestamps = true;
    protected $createdField = 'fcreacion';
    protected $updatedField = 'factualizacion';

    protected $validationRules = [
        'slug' => 'required|alpha_dash|max_length[50]',
        'etiqueta' => 'required|max_length[100]',
        'orden' => 'required|integer',
        'estado' => 'required|in_list[A,I]',
    ];

    public function getActivosOrdenados(): array
    {
        return $this->where('estado', 'A')->orderBy('orden', 'ASC')->orderBy('id', 'ASC')->findAll();
    }

    public function getTodosOrdenados(): array
    {
        return $this->orderBy('orden', 'ASC')->orderBy('id', 'ASC')->findAll();
    }
}

<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------

    public array $formUserLogin = [
        'correo' => [
            'label' => 'Correo',
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'valid_email' => 'Debes ingresar una dirección de correo electrónico válida.'
            ],
        ],
        'clave' => [
            'label' => 'Clave',
            'rules' => 'required|max_length[12]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 12 caracteres de longitud.'
            ],
        ]
    ];


    public array $formUserRegister = [
        'nombre' => [
            'label' => 'nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'apellido' => [
            'label' => 'apellido',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'correo' => [
            'label' => 'Correo',
            'rules' => 'required|valid_email|is_unique[usuario.correo]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'valid_email' => 'Debes ingresar una dirección de correo electrónico válida.',
                'is_unique' => 'El campo {field} ya existe.'
            ],
        ],
        'clave' => [
            'label' => 'Clave',
            'rules' => 'required|max_length[12]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 12 caracteres de longitud.'
            ],
        ],
        'reclave' => [
            'label' => 'Re Clave',
            'rules' => 'matches[clave]',
            'errors' => [
                'matches' => 'Las claves tienen que ser iguales',
                'max_length' => 'El campo {field} no puede exceder de 12 caracteres de longitud.'
            ],
        ],
        'perfil' => [
            'label' => 'perfil',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
    ];

}

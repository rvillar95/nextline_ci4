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
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'apellido' => [
            'label' => 'Apellido',
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
                'is_unique' => 'El campo {field} ya esta registrado.'
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

    public array $formUserEdit = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'apellido' => [
            'label' => 'Apellido',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'correo' => [
            'label' => 'Correo',
            'rules' => 'required|valid_email',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'valid_email' => 'Debes ingresar una dirección de correo electrónico válida.',
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
        'estado' => [
            'label' => 'estado',
            'rules' => 'max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
    ];

    public array $formUserEditPassword = [
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
    ];

    public array $formPerfilRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[perfil.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'estado' => [
            'label' => 'estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ]
    ];

    public array $formPerfilEdit = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'estado' => [
            'label' => 'estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ]
    ];

    public array $formModuloRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[modulo.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'descripcion' => [
            'label' => 'Descripcion',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'ruta' => [
            'label' => 'Ruta',
            'rules' => 'required|max_length[100]|is_unique[modulo.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'sa' => [
            'label' => 'Es Modulo Super Administrador?',
            'rules' => 'required|in_list[S,N]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre S ó N.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ],
        'mostrar' => [
            'label' => 'Mostrar',
            'rules' => 'required|in_list[S,N]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre S ó N.'
            ],
        ]
    ];

    public array $formModuloEdit = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'descripcion' => [
            'label' => 'Descripcion',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'ruta' => [
            'label' => 'Ruta',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El campo {field} ya esta registrado.',
            ],
        ],
        'sa' => [
            'label' => 'Es Modulo Super Administrador?',
            'rules' => 'required|in_list[S,N]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre S ó N.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ],
        'mostrar' => [
            'label' => 'Mostrar',
            'rules' => 'required|in_list[S,N]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre S ó N.'
            ],
        ]
    ];

    public array $formPerfilDetalleRegister = [
        'perfil' => [
            'label' => 'Perfil',
            'rules' => 'required',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
            ],
        ],
        'modulo' => [
            'label' => 'Modulo',
            'rules' => 'required',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
            ],
        ],
        'ver' => [
            'label' => 'Ver',
            'rules' => 'required|in_list[1,0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'En el campo {field} tiene que elegir entre Si ó No.'
            ],
        ],
        'registrar' => [
            'label' => 'Registrar',
            'rules' => 'required|in_list[1,0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'En el campo {field} tiene que elegir entre Si ó No.'
            ],
        ],
        'editar' => [
            'label' => 'Editar',
            'rules' => 'required|in_list[1,0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'En el campo {field} tiene que elegir entre Si ó No.'
            ],
        ],
        'eliminar' => [
            'label' => 'Eliminar',
            'rules' => 'required|in_list[1,0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'En el campo {field} tiene que elegir entre Si ó No.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'required|greater_than[0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'greater_than' => 'El campo {field} debe ser un número mayor a 0.'
            ],
        ]
    ];

    public array $formModuloDetalleRegister = [

        'modulo' => [
            'label' => 'Modulo',
            'rules' => 'required',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
            ],
        ],
        'descripcion' => [
            'label' => 'Descripcion',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.',
            ],
        ],
        'ruta' => [
            'label' => 'Ruta',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
            ],
        ],
        'accion' => [
            'label' => 'Acción',
            'rules' => 'required|max_length[50]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 50 caracteres de longitud.',
            ],
        ],
        'estado' => [
            'label' => 'estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ],
        'mostrar' => [
            'label' => 'estado',
            'rules' => 'required|in_list[S,N]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre S ó N.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ]
    ];

    public array $formServiceRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'descripcionCorta' => [
            'label' => 'Descripción Corta',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'descripcionLarga' => [
            'label' => 'Descripción Larga',
            'rules' => 'required|max_length[2000]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 2000 caracteres de longitud.'
            ],
        ],
        'valor' => [
            'label' => 'Valor',
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than' => 'El campo {field} debe ser un número mayor a 0.'
            ],
        ],
        'img' => [
            'label' => 'Image File',
            'rules' => [
                'uploaded[img]',
                'is_image[img]',
                'mime_in[img,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[img,4096]',
            ],
            'errors' => [
                'uploaded' => 'Por favor sube una imagen.',
                'is_image' => 'El archivo debe ser una imagen.',
                'max_size' => 'El archivo no puede exceder 500 KB.',
                'mime_in' => 'Solo se permiten archivos JPG, JPEG, PNG.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ]
    ];

    public array $formServiceEdit = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'descripcionCorta' => [
            'label' => 'Descripción Corta',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'descripcionLarga' => [
            'label' => 'Descripción Larga',
            'rules' => 'required|max_length[2000]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 2000 caracteres de longitud.'
            ],
        ],
        'valor' => [
            'label' => 'Valor',
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than' => 'El campo {field} debe ser un número mayor a 0.'
            ],
        ],
        'img' => [
            'label' => 'Image File',
            'rules' => [
                'is_image[img]',
                'mime_in[img,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[img,4096]',
            ],
            'errors' => [
                'is_image' => 'El archivo debe ser una imagen.',
                'max_size' => 'El archivo no puede exceder 500 KB.',
                'mime_in' => 'Solo se permiten archivos JPG, JPEG, PNG.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} tiene que ser un carácter entre A ó I.'
            ],
        ]
    ];
}

<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;
use App\Validation\ChileanPhoneRules;

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
        ChileanPhoneRules::class,
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
            'rules' => 'required|max_length[100]|is_unique[servicio.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre del servicio ya existe. Por favor, elige otro nombre.'
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
        'categoria_id' => [
            'label' => 'Categoría',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'caracteristicas' => [
            'label' => 'Características',
            'rules' => 'permit_empty',
            'errors' => []
        ],
        'beneficios' => [
            'label' => 'Beneficios',
            'rules' => 'permit_empty',
            'errors' => []
        ],
        'tiempo_estimado' => [
            'label' => 'Tiempo Estimado',
            'rules' => 'permit_empty|max_length[50]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 50 caracteres de longitud.'
            ],
        ],
        'garantia' => [
            'label' => 'Garantía',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'precio_desde' => [
            'label' => 'Precio Desde',
            'rules' => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.'
            ],
        ],
        'precio_hasta' => [
            'label' => 'Precio Hasta',
            'rules' => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.'
            ],
        ],
        'mostrar_precio' => [
            'label' => 'Mostrar Precio',
            'rules' => 'permit_empty|in_list[S,N]',
            'errors' => [
                'in_list' => 'El campo {field} debe ser Sí o No.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'destacado' => [
            'label' => 'Destacado',
            'rules' => 'permit_empty|in_list[S,N]',
            'errors' => [
                'in_list' => 'El campo {field} debe ser Sí o No.'
            ],
        ],
        'img' => [
            'label' => 'Foto',
            'rules' => [
                'permit_empty',
                'uploaded[img]',
                'is_image[img]',
                'mime_in[img,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[img,2048]',
            ],
            'errors' => [
                'uploaded' => 'Por favor sube una imagen.',
                'is_image' => 'El archivo debe ser una imagen.',
                'max_size' => 'El archivo no puede exceder 2MB.',
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
        'id' => [
            'label' => 'ID',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[servicio.nombre,id,{id}]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre del servicio ya existe. Por favor, elige otro nombre.'
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
        'categoria_id' => [
            'label' => 'Categoría',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'caracteristicas' => [
            'label' => 'Características',
            'rules' => 'permit_empty',
            'errors' => [],
        ],
        'beneficios' => [
            'label' => 'Beneficios',
            'rules' => 'permit_empty',
            'errors' => [],
        ],
        'tiempo_estimado' => [
            'label' => 'Tiempo Estimado',
            'rules' => 'permit_empty|max_length[50]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 50 caracteres de longitud.'
            ],
        ],
        'garantia' => [
            'label' => 'Garantía',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'precio_desde' => [
            'label' => 'Precio Desde',
            'rules' => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.'
            ],
        ],
        'precio_hasta' => [
            'label' => 'Precio Hasta',
            'rules' => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.'
            ],
        ],
        'mostrar_precio' => [
            'label' => 'Mostrar Precio',
            'rules' => 'permit_empty|in_list[S,N]',
            'errors' => [
                'in_list' => 'El campo {field} debe ser S o N.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser un número mayor o igual a 0.'
            ],
        ],
        'destacado' => [
            'label' => 'Destacado',
            'rules' => 'permit_empty|in_list[S,N]',
            'errors' => [
                'in_list' => 'El campo {field} debe ser S o N.'
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

    public array $formGaleriaRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'categoria_id' => [
            'label' => 'Categoría',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero válido.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'portada' => [
            'label' => 'Portada',
            'rules' => [
                'uploaded[portada]',
                'is_image[portada]',
                'mime_in[portada,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[portada,4096]'
            ],
            'errors' => [
                'uploaded' => 'Por favor sube una imagen.',
                'is_image' => 'El archivo debe ser una imagen.',
                'mime_in' => 'Solo se permiten archivos JPG, JPEG, GIF o PNG.',
                'max_size' => 'El archivo no puede exceder de 4 MB.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser un carácter entre A ó I.'
            ],
        ],
    ];

    public array $formGaleriaEdit = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'categoria_id' => [
            'label' => 'Categoría',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero válido.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'required|max_length[500]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'portada' => [
            'label' => 'Portada',
            'rules' => [
                'is_image[portada]',
                'mime_in[portada,image/jpg,image/jpeg,image/gif,image/png]',
                'max_size[portada,4096]'
            ],
            'errors' => [
                'is_image' => 'El archivo debe ser una imagen.',
                'mime_in' => 'Solo se permiten archivos JPG, JPEG, GIF o PNG.',
                'max_size' => 'El archivo no puede exceder de 4 MB.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser un carácter entre A ó I.'
            ],
        ],
    ];
    
    public array $formGaleriaCategoriaRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[galeria_categoria.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre de la categoría ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'icono' => [
            'label' => 'Icono',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'color' => [
            'label' => 'Color',
            'rules' => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 20 caracteres de longitud.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser un número mayor o igual a 0.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser A o I.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];

    public array $formGaleriaCategoriaEdit = [
        'id' => [
            'label' => 'ID',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[galeria_categoria.nombre,id,{id}]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre de la categoría ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'icono' => [
            'label' => 'Icono',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'color' => [
            'label' => 'Color',
            'rules' => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 20 caracteres de longitud.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser un número mayor o igual a 0.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser A o I.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];
    
    // Reglas de validación para categorías de servicios
    public array $formServicioCategoriaRegister = [
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[servicio_categoria.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre de la categoría ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'icono' => [
            'label' => 'Icono',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'color' => [
            'label' => 'Color',
            'rules' => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 20 caracteres de longitud.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser un carácter entre A ó I.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];

    public array $formServicioCategoriaEdit = [
        'id' => [
            'label' => 'ID',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'nombre' => [
            'label' => 'Nombre',
            'rules' => 'required|max_length[100]|is_unique[servicio_categoria.nombre,id,{id}]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.',
                'is_unique' => 'El nombre de la categoría ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'descripcion' => [
            'label' => 'Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'icono' => [
            'label' => 'Icono',
            'rules' => 'permit_empty|max_length[100]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 100 caracteres de longitud.'
            ],
        ],
        'color' => [
            'label' => 'Color',
            'rules' => 'permit_empty|max_length[20]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 20 caracteres de longitud.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[A,I]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser un carácter entre A ó I.'
            ],
        ],
        'orden' => [
            'label' => 'Orden',
            'rules' => 'permit_empty|integer|greater_than_equal_to[0]',
            'errors' => [
                'integer' => 'El campo {field} debe ser un número entero.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];
    
    // Validaciones para Proyectos
    public array $formProyectoRegister = [
        'nombre' => [
            'label' => 'Nombre del Proyecto',
            'rules' => 'required|max_length[255]|is_unique[proyectos.nombre]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.',
                'is_unique' => 'El nombre del proyecto ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'cliente' => [
            'label' => 'Cliente',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'tipo_proyecto' => [
            'label' => 'Tipo de Proyecto',
            'rules' => 'required|in_list[residencial,comercial,industrial,institucional,otro]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser uno de los valores permitidos.'
            ],
        ],
        'ubicacion' => [
            'label' => 'Ubicación',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'direccion' => [
            'label' => 'Dirección',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'fecha_inicio' => [
            'label' => 'Fecha de Inicio',
            'rules' => 'permit_empty|valid_date',
            'errors' => [
                'valid_date' => 'El campo {field} debe ser una fecha válida.'
            ],
        ],
        'fecha_finalizacion' => [
            'label' => 'Fecha de Finalización',
            'rules' => 'permit_empty|valid_date',
            'errors' => [
                'valid_date' => 'El campo {field} debe ser una fecha válida.'
            ],
        ],
        'presupuesto' => [
            'label' => 'Presupuesto',
            'rules' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[en_progreso,completado,en_pausa,cancelado]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser uno de los valores permitidos.'
            ],
        ],
        'descripcion_corta' => [
            'label' => 'Descripción Corta',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'descripcion_detallada' => [
            'label' => 'Descripción Detallada',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'caracteristicas_tecnicas' => [
            'label' => 'Características Técnicas',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'area_construida' => [
            'label' => 'Área Construida',
            'rules' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'materiales_principales' => [
            'label' => 'Materiales Principales',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'testimonio_cliente' => [
            'label' => 'Testimonio del Cliente',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'nombre_cliente' => [
            'label' => 'Nombre del Cliente',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];

    public array $formProyectoEdit = [
        'id' => [
            'label' => 'ID',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'integer' => 'El campo {field} debe ser un número entero.'
            ],
        ],
        'nombre' => [
            'label' => 'Nombre del Proyecto',
            'rules' => 'required|max_length[255]|is_unique[proyectos.nombre,id,{id}]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.',
                'is_unique' => 'El nombre del proyecto ya existe. Por favor, elige otro nombre.'
            ],
        ],
        'cliente' => [
            'label' => 'Cliente',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'tipo_proyecto' => [
            'label' => 'Tipo de Proyecto',
            'rules' => 'required|in_list[residencial,comercial,industrial,institucional,otro]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser uno de los valores permitidos.'
            ],
        ],
        'ubicacion' => [
            'label' => 'Ubicación',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'direccion' => [
            'label' => 'Dirección',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'fecha_inicio' => [
            'label' => 'Fecha de Inicio',
            'rules' => 'permit_empty|valid_date',
            'errors' => [
                'valid_date' => 'El campo {field} debe ser una fecha válida.'
            ],
        ],
        'fecha_finalizacion' => [
            'label' => 'Fecha de Finalización',
            'rules' => 'permit_empty|valid_date',
            'errors' => [
                'valid_date' => 'El campo {field} debe ser una fecha válida.'
            ],
        ],
        'presupuesto' => [
            'label' => 'Presupuesto',
            'rules' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'estado' => [
            'label' => 'Estado',
            'rules' => 'required|in_list[en_progreso,completado,en_pausa,cancelado]',
            'errors' => [
                'required' => 'El campo {field} es obligatorio.',
                'in_list' => 'El campo {field} debe ser uno de los valores permitidos.'
            ],
        ],
        'descripcion_corta' => [
            'label' => 'Descripción Corta',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'descripcion_detallada' => [
            'label' => 'Descripción Detallada',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'caracteristicas_tecnicas' => [
            'label' => 'Características Técnicas',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'area_construida' => [
            'label' => 'Área Construida',
            'rules' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'errors' => [
                'decimal' => 'El campo {field} debe ser un número decimal válido.',
                'greater_than_equal_to' => 'El campo {field} debe ser mayor o igual a 0.'
            ],
        ],
        'materiales_principales' => [
            'label' => 'Materiales Principales',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'testimonio_cliente' => [
            'label' => 'Testimonio del Cliente',
            'rules' => 'permit_empty|string',
            'errors' => [
                'string' => 'El campo {field} debe ser texto válido.'
            ],
        ],
        'nombre_cliente' => [
            'label' => 'Nombre del Cliente',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_titulo' => [
            'label' => 'Meta Título',
            'rules' => 'permit_empty|max_length[255]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 255 caracteres de longitud.'
            ],
        ],
        'meta_descripcion' => [
            'label' => 'Meta Descripción',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
        'meta_keywords' => [
            'label' => 'Meta Keywords',
            'rules' => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'El campo {field} no puede exceder de 500 caracteres de longitud.'
            ],
        ],
    ];

    public $formTestimonioRegister = [
        'nombre' => 'required|string|max_length[100]',
        'cargo' => 'permit_empty|string|max_length[100]',
        'empresa' => 'permit_empty|string|max_length[100]',
        'testimonio' => 'required|string|max_length[2000]',
        'calificacion' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'proyecto_id' => 'permit_empty|integer',
        'servicio_id' => 'permit_empty|integer',
        'estado' => 'required|in_list[A,I]',
        'destacado' => 'required|in_list[S,N]',
        'fecha_proyecto' => 'permit_empty|valid_date'
    ];

    public $formTestimonioEdit = [
        'id' => 'required|integer',
        'nombre' => 'required|string|max_length[100]',
        'cargo' => 'permit_empty|string|max_length[100]',
        'empresa' => 'permit_empty|string|max_length[100]',
        'testimonio' => 'required|string|max_length[2000]',
        'calificacion' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'proyecto_id' => 'permit_empty|integer',
        'servicio_id' => 'permit_empty|integer',
        'estado' => 'required|in_list[A,I]',
        'destacado' => 'required|in_list[S,N]',
        'fecha_proyecto' => 'permit_empty|valid_date'
    ];
}

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
     * @var string[]
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

    /**
     * Conjunto de reglas para el alta de un nuevo libro
     */
    public array $newBook=([
        'nombre_libro' => [
            'rules' => 'required|is_unique[articulo.titulo]',
            'errors' => [
                'required' => 'El nombre es obligatorio.',
                'is_unique' => 'Ya existe un libro con ese titullo.'
            ]
        ],
        'precio_libro' => [
            'rules' => 'required|greater_than[0]',
            'errors' => [
                'required' => 'El precio es obligatorio.',
                'greater_than' => 'El precio debe ser mayor a cero.'
            ]
        ],
        'autor_libro' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Debe seleccionar un autor.'
            ]
        ],
        'editorial_libro' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Debe seleccionar una editorial.'
            ]
        ],
        'sinopsis_libro' => [
            'rules' => 'required',
            'errors' => [
                'required' => 'Debe agregar una sinopsis.',
        
            ]
        ],
        'paginas_libro' => [
            'rules' => 'required|greater_than[0]|less_than_equal_to[1000]',
            'errors' => [
                'required' => 'Debe agregar la cantidad de páginas.',
                'greater_than' => 'Las páginas no pueden ser 0 o menor.',
                'less_than_equal_to' => 'La cantidad de páginas debe ser menor que 1001.'
            ]
        ],
        'fecha_libro' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Por favor, seleccione una fecha para la publicación del libro.'
                ]
            ]
    ]);
}

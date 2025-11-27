<?php

namespace App\Models;

use CodeIgniter\Model;

class GeneroModel extends Model
{
    protected $table = 'genero';

    /**
     * Permite obtener los generos de los libros guardados en la base de datos
     * @return ObjectArray retorna un arreglo de objetos con los datos de todos los géneros almacenados en la base de datos
     */
    public function getGeneros()
    {
        $db = \Config\Database::connect();
        $query = $db->query("CALL digibook2.getGeneros()");
        return $query->getResult(); // devuelve array de objetos
    }
}

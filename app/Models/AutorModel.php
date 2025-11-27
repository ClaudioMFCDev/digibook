<?php

namespace App\Models;

use CodeIgniter\Model;

class AutorModel extends Model
{
    protected $table = 'autor';

    /**
     * Permite obtener los datos de los autores almacenados en la base de datos
     * @return ObjectArray retorna un arreglo de objetos con los datos de todos los autores almacenados en la base de datos
     */
    public function getAutores()
    {
        $db = \Config\Database::connect();
        $query = $db->query("CALL digibook2.getAutores()");
        return $query->getResult(); // devuelve array de objetos
    }
}
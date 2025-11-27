<?php

namespace App\Models;

use CodeIgniter\Model;

class EditorialModel extends Model
{
    protected $table = 'editorial';

     /**
     * Permite obtener la información de las editoriales almacenadas en la base de datos
     * @return ObjectArray retorna un arreglo de objetos con los datos de todas las editoriales almacenados en la base de datos
     */
    public function getEditoriales()
    {
        $db = \Config\Database::connect();
        $query = $db->query("CALL digibook2.getEditoriales()");
        return $query->getResult(); // devuelve array de objetos
    }
}

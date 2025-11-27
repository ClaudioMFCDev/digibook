<?php

namespace App\Models;

use CodeIgniter\Model;

class CompraModel extends Model
{
    protected $table = 'compra';
    protected $db;

    public function __construct()
    {
        //parent::__construct(); 
        $this->db = \Config\Database::connect();
    }

    /**
     * Permite obtener los datos de los autores almacenados en la base de datos
     * @return ObjectArray retorna un arreglo de objetos con los datos de todos los autores almacenados en la base de datos
     */
    public function realizarCompra($total, $fecha, $dni,$jsonDetalles)
    {
         
            $query = $this->db->query("CALL registrar_compra_con_detalles(?, ?, ?, ?, @mensaje, @codigo)", [
            $total,
            $fecha,
            $dni,
            $jsonDetalles
        ]);
        return $this->db->query("SELECT @mensaje AS mensaje, @codigo AS codigo")->getRow();
        //return $query->getResult(); // devuelve array de objetos
    }
}
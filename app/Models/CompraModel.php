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
    public function realizarCompra($total, $fecha, $dni, $jsonDetalles)
    {
        // Ejecutamos el procedimiento
        // Si algo falla dentro de MySQL (ROLLBACK), lanzará una Excepción aquí mismo.
        $this->db->query("CALL registrar_compra_con_detalles(?, ?, ?, ?, @mensaje, @codigo)", [
            $total,
            $fecha,
            $dni,
            $jsonDetalles
        ]);
        
        // no intentamos leer @mensaje ni @codigo para evitar el error de conexión.
        // si llegamos hasta aquí, es que funcionó.
        return true; 
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Node\Stmt\TryCatch;

class ArticuloModel extends Model
{
    /**
     * Permite crear un nuevo libro en la base de datos mediante un procedimiento almacenado en ésta.
     * @param Array $data un arreglo con el título, precio, editorial, sinopsis, páginas, autor y género del libro
     * @return Array retorna un arreglo que contiene el resultado de la operación que será 1 en caso de éxito y 0 en caso de error, y un mensaje de error en caso de existir
     */
    public function insertaArticulo($data): Array
{
    $db = \Config\Database::connect();
     // Asignar fecha o null
     $fecha = !empty($data['fecha_publicacion']) 
     ? date('Y-m-d', strtotime($data['fecha_publicacion'])) 
     : null;

    //creo el conjunto de datos que voy a almacenar
    $data_to_insert=[
        $data['titulo'],
        $data['precio'],
        $data['editorial_id'],
        $data['sinopsis'],
        $data['paginas'],
        $data['autor_id'],
        $data['genero_id'],
        $data['img'],
        $fecha
    ];
    //la sentencia sql que llama al procedimiento en mysql
    $sql = "CALL crearNuevoLibro(?, ?, ?, ?, ?, ?, ?, ?, ?, @resultado, @msj_error)";
    
    try {
        // Ejecutar el procedimiento con los parámetros
        $db->query($sql, $data_to_insert);

        // Obtener el valor de salida
        $query = $db->query("SELECT @resultado as resultado,@msj_error as msj_error");
        $row = $query->getRow();
        //retorno un arreglo con el mensaje de error, en caso que haya
        //y retorno también el resultado que será 0 en caso de error y 1 en caso contrario
        return ['msj_error'=>$row->msj_error,'resultado'=>$row->resultado];
    } catch (\Throwable $e) {
        //retorno el error según la excepción acontecida.
        return Array('resultado'=>0,"msj_error"=>$e->getMessage());
    }



}

public function getArticulos(){
    $db = \Config\Database::connect();
     //la sentencia sql que llama al procedimiento en mysql
    $sql = "CALL obtenerLibros()";
    
    try {
        // Ejecutar el procedimiento con los parámetros
        $query = $this->db->query("CALL obtenerLibros()");
        

        //retorno un arreglo con el mensaje de error, en caso que haya
        //y retorno también el resultado que será 0 en caso de error y 1 en caso contrario
        return ['resultado'=>$query->getResultArray(),"msj_error"=>"Sin errores" ];
    } catch (\Throwable $e) {
        //retorno el error según la excepción acontecida.
        return Array('resultado'=>0,"msj_error"=>$e->getMessage());
    }

}

public function getArticuloPorId($idArticulo){
    $db = \Config\Database::connect();
     //la sentencia sql que llama al procedimiento en mysql
    $sql = "CALL obtenerLibroPorId(?)";
    
    
    try {
        // Ejecutar el procedimiento con los parámetros
        $query=$db->query($sql, $idArticulo);

        $row = $query->getRow();
        

        //retorno un arreglo con el mensaje de error, en caso que haya
        //y retorno también el resultado que será 0 en caso de error y 1 en caso contrario
        return ['resultado'=>$query->getResultArray(),"msj_error"=>"Sin errores" ];
    } catch (\Throwable $e) {
        //retorno el error según la excepción acontecida.
        return Array('resultado'=>0,"msj_error"=>$e->getMessage());
    }

}

}
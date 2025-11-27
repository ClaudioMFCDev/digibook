<?php

namespace App\Controllers;

use App\Models\ArticuloModel;

//https://github.com/bertugfahriozer/ci4shoppingcart carrito
class Home extends BaseController
{

    //la variable que tendrá la conexión a la base de datos y a la tabla roles
    private $roles=null;
    public function __construct()
    {
       
        $session = \Config\Services::session();
    }
   public function index(){
            /*$cart = \Config\Services::cart();
            $cart->insert(array(
                'id'      => 'sku_1234ABCD',
                'qty'     => 1,
                'price'   => '19.56',
                'name'    => 'T-Shirt',
                'options' => array('Size' => 'L', 'Color' => 'Red')
             ));

             dd($cart);*/
            $prodModel=new ArticuloModel();
            $productos=$prodModel->getArticulos();
            $data['titulo'] = 'index';
            echo view('plantillas/head');
            echo view('plantillas/navbar');
            echo view('Contenido/index',['productos'=>$productos['resultado']]);
            echo view('plantillas/footer');
        
        }

   




   
}







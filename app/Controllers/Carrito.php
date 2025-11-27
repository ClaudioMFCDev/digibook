<?php

namespace App\Controllers;

use CodeIgniter\Entity\Cast\StringCast;
use CodeIgniter\HTTP\Request;
use CodeIgniter\Session\Session;

use App\Models\ArticuloModel;
use ci4shoppingcart\Libraries\Cart;
use PhpParser\Node\Stmt\TryCatch;

class Carrito extends BaseController{
    private $cart;
    private $articulo;
    /**
     * Constructor de la clase ProductController
     */
    public function __construct()
    {
        //llamada al constructor de la superclase
        parent::__construct(); 
        $this->cart=new Cart(); 
        $this->articulo=New ArticuloModel(); 
    }

    public function showCart(){
        $cart_data=$this->getCart();
        return view('plantillas/head') .
        view('plantillas/navbar') .
        view('contenido/VistaCarrito',['cart' => $cart_data]) .
        view('plantillas/footer');
        
    }

    /**
     * Permite obtener los datos guardados en el carrito
     */
    public function getCart(){
        return $this->cart->contents();
    }

    public function getTotal(){
        return $this->cart->total();
    }

    public function destruirCart(){
        $this->cart->destroy();
    }


    private function buscarArticulo($idProd){
        try {
           
            $articulo=$this->articulo->getArticuloPorId(intval($idProd));
            return $articulo;
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
    /**
     * 
     */
    private function agregarCarrito($datosArticulo){
        try {
            $this->cart->insert($datosArticulo);
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('cart_errors', 'Error al agregar el producto al artículo');
        }
    }

    /**
     * Permite agregar un nuevo producto al carrito
     */
    public function controlAgregar($idProd){
        
        $productoExiste = false;
        //controla que el producto ya no esté en el carrito
        foreach ($this->getCart() as $item) {
            if ($item['id'] === $idProd) {
                $productoExiste = true;
                break;
            }
        }
        if ($productoExiste){
            return redirect()->back()->withInput()->with('cart_errors', 'El producto ya está en el carrito');
        }
        else{
            $articulo=$this->buscarArticulo($idProd);
            //dd($articulo['resultado'][0]['id']);
            if (count($articulo)>=0){
                $datosArticulo=['id'      => $idProd,
                    'qty'     => 1,
                    'price'   => $articulo['resultado'][0]['Precio'],
                    'name'    => $articulo['resultado'][0]['Título'],
                    'genre'=>$articulo['resultado'][0]['Género'],
                    'editorial'=>$articulo['resultado'][0]['Editorial'],
                    'author'=>$articulo['resultado'][0]['Autores'],
                    
                ];
                 $this->agregarCarrito($datosArticulo);
                 
                return redirect()->to(base_url('cart'));
            }
           
        }
            
    }

    /**
     * Permite eliminar un elemento del carrito según su id
     */
    public function eliminarArticulo($idProd){
        
        $encontrado = false;
         foreach ($this->getCart() as $rowid => $item) {
            if ($item['id'] == $idProd) {
                $this->cart->remove($rowid);
                $encontrado=true;
                break;
            }
        }
         if ($this->request->isAJAX()) {
            if ($encontrado) {
                  return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Producto eliminado del carrito.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Producto no encontrado en el carrito.'
                ]);
            }
        }
        
    }

    /**
     * Método que permite la eliminación total del carrito
     */
    public function vaciarCarrito()
    {
        try {
                $this->cart->destroy();
                return $this->response->setJSON([
                    'success' => true,
                
                        'message' => 'Carrito eliminado.'
                    ]);
        } catch (\Throwable $th) {
              return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar el carrito.'
                ]);
        }
        

       
    }

}
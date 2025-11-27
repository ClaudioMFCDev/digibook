<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CompraModel;
use ci4shoppingcart\Libraries\Cart; // Usamos la librería directo, es más seguro

class Compra extends Controller
{
    protected $cart;
    protected $compraModel;

    public function __construct()
    {
        $this->cart = new Cart(); // Instancia limpia del carrito
        $this->compraModel = new CompraModel();
    }

    public function controlarCompra()
    {
        // 1. Validación básica
        if (!$this->cart->contents()) {
             return "<h1>El carrito está vacío</h1><a href='".base_url()."'>Volver</a>";
        }

        // 2. Datos para la compra (Asegúrate que el DNI 32837262 existe en tu BD)
        $dni = 32837262; 
        $fecha = date('Y-m-d'); 
        $total = $this->cart->total();

        // 3. Preparar JSON de productos
        $items = [];
        foreach ($this->cart->contents() as $item) {
            $items[] = [
                'id'     => intval($item['id']),
                'qty'    => intval($item['qty']),
                'precio' => floatval($item['price'])
            ];
        }
        $jsonDetalles = json_encode($items);

        // 4. Intentar guardar
        try {
            // Llamamos al modelo. Si falla, saltará al catch.
            $this->compraModel->realizarCompra($total, $fecha, $dni, $jsonDetalles);

            // --- ZONA DE ÉXITO ---
            // Si el código llegó hasta acá, es que MySQL no se quejó.
            
            $this->cart->destroy(); // Vaciamos el carrito

            // Mostramos el mensaje de éxito para la captura de pantalla del TP
            return "
            <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
                <h1 style='color:green;'>¡Compra Exitosa!</h1>
                <p>La transacción ha sido registrada en la base de datos.</p>
                <p>El carrito se ha vaciado correctamente.</p>
                <br>
                <a href='".base_url()."' style='padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Volver al Inicio</a>
            </div>";

        } catch (\Throwable $th) {
            // --- ZONA DE ERROR ---
            return "
            <div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
                <h1 style='color:red;'>Error en la compra</h1>
                <p>Detalle técnico: " . $th->getMessage() . "</p>
                <a href='".base_url('cart')."'>Volver al Carrito</a>
            </div>";
        }
    }


}
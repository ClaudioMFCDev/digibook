<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index',['as'=>'index']);
$routes->get('products', 'Articulo::show_product_form');
$routes->post('create_book', 'Articulo::validarDatos');
$routes->get('cart', 'Carrito::showCart');
$routes->post('cart/add/(:num)', 'Carrito::controlAgregar/$1');
$routes->get('cart/remove/(:any)', 'Carrito::eliminarArticulo/$1');
$routes->get('cart/delete', 'Carrito::vaciarCarrito');
$routes->get('buy/showCart','Carrito::showCart');
$routes->get('buy/registrar', 'Compra::controlarCompra');








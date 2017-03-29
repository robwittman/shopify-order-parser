<?php

namespace App\Controller;

use App\Model\Product;
use App\Model\ProductVariant;

class Products
{
    protected $view;
    protected $flash;

    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {

    }

    public function show($request, $response, $arguments)
    {
        $product = Product::with('variants')->find($arguments['productId']);
        return $this->view->render($response, 'products/show.html', array(
            'product' => $product
        ));
    }

    public function create($request, $response)
    {

    }

    public function update($request, $response, $arguments)
    {

    }

    public function delete($request, $response, $arguments)
    {

    }
}

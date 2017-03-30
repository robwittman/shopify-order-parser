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

    public function index($request, $response, $arguments)
    {
        if ($request->isPost()) {
            $search = $request->getParsedBody()['search'];
            $product = Product::where('id', '=', $search)
                ->orWhere('handle', '=', $search)
                ->first();
            if(empty($product)) {
                return $this->view->render($response, 'products/index.html', array(
                    'error' => "No products found"
                ));
            } else {
                return $response->withRedirect('/products/'.$product->id);
            }
        } else {
            return $this->view->render($response, 'products/index.html');
        }
    }

    public function show($request, $response, $arguments)
    {
        $product = Product::with('variants')->find($arguments['productId']);
        return $this->view->render($response, 'products/show.html', array(
            'product' => $product
        ));
    }

    public function update($request, $response, $arguments)
    {
        $product = Product::find($arguments['productId']);
        if (empty($product)) {
            exit('Not found');
        }
        $params = $request->getParsedBody();
        $product->color_count = $params['color_count'];
        $product->save();

        $this->flash->addMessage('message', "Product successfully updated");
        return $response->withRedirect('/products/'.$arguments['productId']);
    }
}

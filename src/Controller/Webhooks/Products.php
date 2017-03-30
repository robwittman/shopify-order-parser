<?php

namespace App\Controller\Webhooks;

use App\Model\Product;
use App\Model\ProductVariant;

class Products
{
    public function create($request, $response)
    {
        $product = $request->getParsedBody();
        $this->save($product);
    }

    public function update($request, $response)
    {
        $product = $request->getParsedBody();
        $this->save($product);
    }

    public function delete($request, $response)
    {
        $product = $request->getParsedBody();
        Product::delete($product['id']);
    }

    public function save($data)
    {
        $product = Product::find($data['id']);
        if (empty($product)) {
            // Create product
        } else {
            // Update product
        }

        foreach ($data['variants'] as $var) {
            $variant = ProductVariant::find($var['id']);
            if (empty($variant)) {
                // Create variant
            } else {
                // Update variant
            }
        }
    }
}

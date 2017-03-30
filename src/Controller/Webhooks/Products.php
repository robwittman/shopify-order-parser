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
            $p = new Product();
            $p->id          = $data['id'];
            $p->vendor      = $data['vendor'];
            $p->body_html   = $data['body_html'];
            $p->created_at  = $data['created_at'];
            $p->handle      = $data['handle'];
            $p->images      = $data['images'];
            $p->options     = $data['options'];
            $p->product_type= $data['product_type'];
            $p->tags        = $data['tags'];
            $p->vendor      = $data['vendor'];
            $p->shop_id     = $request->getAttribute('shop_id');
            $p->save();
        } else {
            $product->vendor    = $data['vendor'];
            $product->body_html = $data['body_html'];
            $product->handle    = $data['handle'];
            $product->images    = $data['images'];
            $product->options   = $data['options'];
            $product->product_type = $data['product_type'];
            $product->tags      = $data['tags'];
            $product->vendor    = $data['vendor'];
            $product->save();
        }

        foreach ($data['variants'] as $var) {
            $variant = ProductVariant::find($var['id']);
            if (empty($variant)) {
                $v = new ProductVariant();
                $v->id                  = $var['id'];
                $v->shop_id             = $request->getAttribute('shop_id');
                $v->product_id          = $data['id'];
                $v->barcode             = $var['barcode'];
                // $v->fulfillment_status = $var['fulfillment_status'];
                $v->grams               = $var['grams'];
                $v->image_id            = $var['image_id'];
                $v->inventory_management= $var['inventory_management'];
                $v->inventory_policy    = $var['inventory_policy'];
                $v->option1             = $var['option1'];
                $v->option2             = $var['option2'];
                $v->option3             = $var['option3'];
                $v->position            = $var['position'];
                $v->price               = $var['price'];
                $v->sku                 = $var['sku'];
                $v->title               = $var['title'];
                $v->save();
            } else {
                $variant->barcode               = $var['barcode'];
                // $variant->fulfillment_status = $var['fulfillment_status'];
                $variant->grams                 = $var['grams'];
                $variant->image_id              = $var['image_id'];
                $variant->inventory_management  = $var['inventory_management'];
                $variant->inventory_policy      = $var['inventory_policy'];
                $variant->option1               = $var['option1'];
                $variant->option2               = $var['option2'];
                $variant->option3               = $var['option3'];
                $variant->position              = $var['position'];
                $variant->price                 = $var['price'];
                $variant->sku                   = $var['sku'];
                $variant->save();
            }
        }
    }
}

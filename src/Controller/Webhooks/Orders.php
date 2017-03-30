<?php

namespace App\Controller\Webhooks;

use App\Model\Order;
use App\Model\LineItem;

class Orders
{
    public function create($request, $response)
    {
        $order = $request->getParsedBody();
        $this->save($order);
    }

    public function update($request, $response)
    {
        $order = $request->getParsedBody();
        $this->save($order);
    }

    public function delete($request, $response)
    {
        $order = $request->getParsedBody();
        Order::delete($order['id']);
    }

    public function save($data)
    {
        $order = Order::find($data['id']);
        if (empty($order)) {
            $o = new Order();
            $o->id                  = $data['id'];
            $o->shop_id             = $request->getAttribute('shop_id');
            $o->created_at          = $data['created_at'];
            $o->closed_at           = $data['closed_at'];
            $o->cancelled_at        = $data['cancelled_at'];
            $o->email               = $data['email'];
            $o->financial_status    = $data['financial_status'];
            $o->fulfillment_status  = $data['fulfillment_status'];
            $o->tags                = $data['tags'];
            $o->name                = $data['name'];
            $o->number              = $data['number'];
            $o->order_number        = $data['order_number'];
            $o->processed_at        = $data['processed_at'];
            $o->subtotal_price      = $data['subtotal_price'];
            $o->total_discounts     = $data['total_discounts'];
            $o->total_line_items_price = $data['total_line_items_price'];
            $o->total_price         = $data['total_price'];
            $o->total_tax           = $data['total_tax'];
            $o->save();
        } else {
            $order->closed_at               = $data['closed_at'];
            $order->cancelled_at            = $data['cancelled_at'];
            $order->email                   = $data['email'];
            $order->financial_status        = $data['financial_status'];
            $order->fulfillment_status      = $data['fulfillment_status'];
            $order->tags                    = $data['tags'];
            $order->name                    = $data['name'];
            $order->number                  = $data['number'];
            $order->order_number            = $data['order_number'];
            $order->processed_at            = $data['processed_at'];
            $order->subtotal_price          = $data['subtotal_price'];
            $order->total_discounts         = $data['total_discounts'];
            $order->total_line_items_price  = $data['total_line_items_price'];
            $order->total_price             = $data['total_price'];
            $order->total_tax               = $data['total_tax'];
            $order->save();
        }

        foreach ($data['line_items'] as $item) {
            $line_item = LineItem::find($item['id']);
            if (empty($line_item)) {
                $li = new LineItem();
                $li->id             = $item['id'];
                $li->product_id     = $item['product_id'];
                $li->variant_id     = $item['variant_id'];
                $li->vendor         = $item['vendor'];
                $li->variant_title  = $item['variant_title'];
                $li->quantity       = $item['quantity'];
                $li->price          = $item['price'];
                $li->title          = $item['title'];
                $li->order_id       = $data['id'];
                $li->shop_id        = $request->getAttribute('shop_id');
                $li->save();
            } else {
                $line_item->product_id  = $item['product_id'];
                $line_item->variant_id  = $item['variant_id'];
                $line_item->vendor      = $item['vendor'];
                $line_item->variant_title = $item['variant_title'];
                $line_item->quantity    = $item['quantity'];
                $line_item->price       = $item['price'];
                $line_item->title       = $item['title'];
                $line_item->save();
            }
        }
    }
}

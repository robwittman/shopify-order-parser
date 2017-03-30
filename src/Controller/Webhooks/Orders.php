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
            // Create order
        } else {
            // Update order
        }

        foreach ($data['line_items'] as $li) {
            $line_item = LineItem::find($li['id']);
            if (empty($line_item)) {
                // Create the lineitem
            } else {
                // Update the lineitem
            }
        }
    }
}

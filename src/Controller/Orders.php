<?php

namespace App\Controller;

use App\Model\Order;
use App\Model\LineItem;

class Orders
{
    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {
        $page = $request->getParam('page');
        $limit = 25;
        $offset = ($limit * $page) - $limit;
        $orders = Order::skip($offset)->take($limit)->orderBy('order_number', 'DESC')->get();
        return $this->view->render($response, 'orders/index.html', array(
            'orders' => $orders
        ));
    }

    public function show($request, $response, $arguments)
    {
        $order = Order::find($arguments['orderId']);
        if (empty($order)) {
            exit('Not found');
        }
        $line_items = LineItem::where('order_id', '=', $order->id)->get();
        return $this->view->render($response, 'orders/show.html', array(
            'order' => $order,
            'line_items' => $line_items
        ));
    }
}

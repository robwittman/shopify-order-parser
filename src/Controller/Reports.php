<?php

namespace App\Controller;

use App\Model\Shop;

class Reports
{
    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {
        //Render our index page
        $shops = Shop::all();
        $this->view->render($response, 'reports/index.html', [
            'shops' => $shops
        ]);
    }

    public function create($request, $response)
    {
        $params = $request->getParsedBody();
        $shop = Shop::find($params['shop_id']);
        $products = array();
        $garments = array();
        $start = date("c", strtotime($params['date'].' 00:00:00'));
        $end = date("c", strtotime($params['date'].' 23:59:59'));

        $orders = $this->chunk($shop, '/admin/orders.json', array(
            'limit' => 250,
            'page' => 1,
            'created_at_min' => $start,
            'created_at_max' => $end
        ));
        foreach ($orders as $order) {
            foreach ($order->line_items as $line_item) {
                if ($line_item->vendor != 'BPP') {
                    continue;
                }

                $title = $line_item->title;
                if (!isset($products[$title])) {
                    $products[$title] = $line_item->quantity;
                } else {
                    $products[$title] += $line_item->quantity;
                }

                var_dump($line_item);
            }
        }
        echo "<pre>";
        echo json_encode($products, JSON_PRETTY_PRINT);
        echo "</pre>";
    }
}

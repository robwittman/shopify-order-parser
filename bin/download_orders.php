<?php
define("DIR", dirname(dirname(__FILE__)));
require_once DIR.'/vendor/autoload.php';
require_once DIR.'/src/common.php';

use App\Model\Shop;
use App\Model\Order;
use App\Model\LineItem;

$dbUrl = getenv("DATABASE_URL");
$dbConfig = parse_url($dbUrl);

$settings = array(
    'db' => array(
        'driver' => 'pgsql',
        'host' => $dbConfig['host'],
        'database' => ltrim($dbConfig['path'], '/'),
        'username' => $dbConfig['user'],
        'password' => $dbConfig['pass'],
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    )
);
$app = new Slim\App();
$container = $app->getContainer();
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($settings['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
$capsule->getContainer()->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\CustomException::class
);

$shopId = $argv[1];
$shop = \App\Model\Shop::find($shopId);

$params = [
    'limit' => 250,
    'page' => 1
];

do {
    $res = callShopify($shop, '/admin/orders.json', 'GET', $params);
    foreach ($res->orders as $order) {
        $o = Order::find($order->id);
        if (empty($o)) {
            $o = new Order();
            $o->id = $order->id;
            $o->shop_id = $shopId;
            $o->created_at = $order->created_at;
            $o->closed_at = $order->closed_at;
            $o->cancelled_at = $order->cancelled_at;
            $o->email = $order->email;
            $o->financial_status = $order->financial_status;
            $o->fulfillment_status = $order->fulfillment_status;
            $o->tags = $order->tags;
            $o->name = $order->name;
            $o->number = $order->number;
            $o->order_number = $order->order_number;
            $o->processed_at = $order->processed_at;
            $o->subtotal_price = $order->subtotal_price;
            $o->total_discounts = $order->total_discounts;
            $o->total_line_items_price = $order->total_line_items_price;
            $o->total_price = $order->total_price;
            $o->total_tax = $order->total_tax;
            $o->created_date = date('Y-m-d', strtotime($order->created_at));
            $o->save();
        } else {
            // Update order
            $o->created_date = date('Y-m-d', strtotime($order->created_at));
            $o->save();
        }
        foreach ($order->line_items as $line_item) {
            $li = LineItem::find($line_item->id);
            if (empty($li)) {
                $li = new LineItem();
                $li->id = $line_item->id;
                $li->product_id = $line_item->product_id;
                $li->variant_id = $line_item->variant_id;
                $li->vendor = $line_item->vendor;
                $li->variant_title = $line_item->variant_title;
                $li->quantity= $line_item->quantity;
                $li->price = $line_item->price;
                $li->title = $line_item->title;
                $li->order_id = $order->id;
                $li->shop_id = $shopId;
                $li->save();
            } else {
                // Update line item
            }
        }
    }
    $params['page']++;
} while (count($res->orders) == $params['limit']);

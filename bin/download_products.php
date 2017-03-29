<?php
define("DIR", dirname(dirname(__FILE__)));
require_once DIR.'/vendor/autoload.php';
require_once DIR.'/src/common.php';

use App\Model\Shop;
use App\Model\Product;
use App\Model\ProductVariant;

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
    $res = callShopify($shop, '/admin/products.json', 'GET', $params);
    foreach ($res->products as $product) {
        $p = Product::find($product->id);
        if (empty($p)) {
            $p = new Product();
            $p->id = $product->id;
            $p->vendor = $product->vendor;
            $p->body_html = $product->body_html;
            $p->created_at = $product->created_at;
            $p->handle = $product->handle;
            $p->images = $product->images;
            $p->options = $product->options;
            $p->product_type = $product->product_type;
            $p->tags = $product->tags;
            $p->vendor = $product->vendor;
            $p->shop_id = $shopId;
            $p->save();
        } else {
            // Add update
        }

        foreach ($product->variants as $variant) {
            $v = ProductVariant::find($variant->id);
            if (empty($v)) {
                $v = new ProductVariant();
                $v->id = $variant->id;
                $v->shop_id = $shopId;
                $v->product_id = $product->id;
                $v->barcode = $variant->barcode;
                $v->fulfillment_service = $variant->fulfillment_service;
                $v->grams = $variant->grams;
                $v->image_id = $variant->image_id;
                $v->inventory_management = $variant->inventory_management;
                $v->inventory_policy = $variant->inventory_policy;
                $v->option1 = $variant->option1;
                $v->option2 = $variant->option2;
                $v->option3 = $variant->option3;
                $v->position = $variant->position;
                $v->price = $variant->price;
                $v->sku = $variant->sku;
                $v->title = $variant->title;
                $v->save();
            } else {
                // Update variants that are already present
            }
        }
    }
    $params['page']++;
} while (count($res->products) == $params['limit']);

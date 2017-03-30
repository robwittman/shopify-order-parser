<?php

namespace App\Middleware;

use App\Model\Shop;
class ValidateWebhook
{
    protected $key;

    public function __construct($key = null)
    {
        $this->key = $key;
    }

    public function __invoke($request, $response, $next)
    {
        $header = $request->getHeader('X-Shopify-Shop-Domain');
        $shop = Shop::where('myshopify_domain', '=', $header[0])->first();
        if (empty($shop)) {
            error_log("Failed finding store with domain {$header[0]}");
            throw new \Exception("Shop not found");
        }
        $request = $request->withAttribute('shop_id', $shop->id);
        return $next($request, $response);
    }
}

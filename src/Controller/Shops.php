<?php

namespace App\Controller;

use App\Model\Shop;

class Shops
{
    public function __construct($view, $flash)
    {
        $this->view = $view;
        $this->flash = $flash;
    }

    public function index($request, $response)
    {
        $shops = Shop::all();
        return $this->view->render($response, 'shops/index.html', array(
            'shops' => $shops
        ));
    }

    public function create($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can manage shops");
            return $response->withRedirect('/shops');
        }

        if ($request->isGet()) {
            return $this->view->render($response, 'shops/new.html');
        }

        $params = $request->getParsedBody();
        $shop = new Shop();
        $shop->myshopify_domain = $params['myshopify_domain'];
        $shop->api_key = $params['api_key'];
        $shop->password = $params['password'];
        $shop->shared_secret = $params['shared_secret'];

        $res = callShopify($shop, '/admin/shop.json');
        $shop->id = $res->shop->id;

        try {
            $shop->save();
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
            return $response->withRedirect('/shops/new');
        }

        $this->flash->addMessage('message', "Shop successfully created");
        return $response->withRedirect('/shops');
    }

    public function show($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can manage shops");
            return $response->withRedirect('/shops');
        }

        $shop = Shop::find($arguments['shopId']);
        $res = callShopify($shop, '/admin/shop.json');
        return $this->view->render($response, 'shops/show.html',array(
            'shop' => $shop,
            'data' => $res->shop
        ));
    }

    public function delete($request, $response, $arguments)
    {
        if ($request->getAttribute('user')->role != 'admin') {
            $this->flash->addMessage('error', "Only admin can manage shops");
            return $response->withRedirect('/shops');
        }

        $shop = Shop::find($arguments['id']);
        if (empty($shop)) {
            $this->flash->addMessage('error', "Shop {$arguments['id']} not found");
            return $response->withRedirect('/shops');
        }

        if ($request->isGet()) {
            return $this->view->render($response, 'shops/confirm.html', array(
                'shop' => $shop
            ));
        } else {
            $shop->delete();
            $this->flash->addMessage('message', 'Shop succesfully deleted');
            return $response->withRedirect('/shops');
        }
    }
}

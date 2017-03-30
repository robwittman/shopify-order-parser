<?php

$app->get('/', function($request, $response) {
    return $response->withRedirect('/reports');
});

$app->map(['GET', "POST"], '/login', 'AuthController:login');

$app->any('/logout', 'AuthController:logout');

$app->group('/shops', function() use ($app) {
    $app->get('', "ShopController:index");
    $app->get('/new', "ShopController:create");
    $app->post("", "ShopController:create");
    $app->get('/{shopId}', "ShopController:show");
    $app->delete("/{shopId}", "ShopController:delete");
    $app->get("/{shopId}/delete", "ShopController:delete");
})->add(new \App\Middleware\Authorization())->add(new \App\Middleware\Session());

$app->group('/users', function () use ($app) {
    $app->get('', "UserController:index");
    $app->get('/create', "UserController:create");
    $app->post('', "UserController:create");
    $app->group('/{id}', function () use ($app) {
        $app->get('', "UserController:show");
        $app->post('', "UserController:update");
        $app->map(array("GET", "POST"), '/delete', "UserController:delete");
    });
})->add(new \App\Middleware\Authorization())->add(new \App\Middleware\Session());

$app->group('/reports', function() use ($app) {
    $app->get('', 'ReportController:index');
    $app->post('', 'ReportController:create');
    // $app->get('/test', 'ReportController:scheduleTest');
})->add(new \App\Middleware\Authorization())->add(new \App\Middleware\Session());

$app->group('/products', function() use ($app) {
    $app->map(['GET','POST'], '', 'ProductController:index');
    $app->get('/{productId}', 'ProductController:show');
    $app->put('/{productId}', 'ProductController:up1date');
})->add(new \App\Middleware\Authorization())->add(new \App\Middleware\Session());

$app->group('/orders', function() use ($app) {
    $app->get('', 'OrderController:index');
    $app->get('/{orderId}', 'OrderController:show');
})->add(new \App\Middleware\Authorization())->add(new \App\Middleware\Session());

$app->group('/webhooks', function() use ($app) {
    $app->group('/products', function() use ($app) {
        $app->post('/create', 'ProductWebhookController:create');
        $app->post('/update', 'ProductWebhookController:update');
        $app->post('/delete', 'ProductWebhookController:delete');
    });
    $app->group('/orders', function() use ($app) {
        $app->post('/create', 'OrderWebhookController:create');
        $app->post('/updated', 'OrderWebhookController:update');
        $app->post('/delete', 'OrderWebhookController:delete');
    });

    $app->group('/shop', function() use ($app) {
        $app->post('/update', 'ShopWebhookController:update');
    });
})->add(new \App\Middleware\ValidateWebhook());

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
});

$app->group('/users', function () use ($app) {
    $app->get('', "UserController:index");
    $app->get('/create', "UserController:create");
    $app->post('', "UserController:create");
    $app->group('/{id}', function () use ($app) {
        $app->get('', "UserController:show");
        $app->post('', "UserController:update");
        $app->map(array("GET", "POST"), '/delete', "UserController:delete");
    });
});

$app->group('/reports', function() use ($app) {
    $app->get('', 'ReportController:index');
    $app->post('', 'ReportController:create');
    // $app->get('/test', 'ReportController:scheduleTest');
});

$app->group('/products', function() use ($app) {
    $app->map(['GET','POST'], '', 'ProductController:index');
    $app->get('/{productId}', 'ProductController:show');
    $app->put('/{productId}', 'ProductController:update');
});

$app->group('/orders', function() use ($app) {
    $app->get('', 'OrderController:index');
    $app->get('/{orderId}', 'OrderController:show');
});

$app->add(new \App\Middleware\Authorization());
$app->add(new \App\Middleware\Session());

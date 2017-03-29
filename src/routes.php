<?php

$app->get('/', function($request, $response) {
    return $response->withJson(array(
        'success' => true
    ));
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

$app->group('/reports', function() use ($app) {
    $app->get('', 'ReportController:index');
    $app->post('', 'ReportController:create');
});

$app->group('/products', function() use ($app) {
    $app->get('', 'ProductController:index');
    $app->get('/{productId}', 'ProductController:show');
    $app->put('/{productId}', 'ProductController:update');
});

$app->add(new \App\Middleware\Authorization());

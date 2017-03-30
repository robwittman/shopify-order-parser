<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

$container = $app->getContainer();
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig('../views');
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));

    $view->getEnvironment()->addGlobal('flash', $c['flash']);
    $view->getEnvironment()->addGlobal('store', getenv("MYSHOPIFY_DOMAIN"));
    return $view;
};
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$capsule->getContainer()->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\CustomException::class
);

$container['db'] = function ($c) {
    return $capsule;
};

$container['flash'] = function ($c) {
    return new Slim\Flash\Messages();
};

$container['AuthController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Auth($view, $flash);
};

$container['UserController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Users($view, $flash);
};

$container['ShopController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Shops($view, $flash);
};

$container['ReportController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Reports($view, $flash);
};

$container['ProductController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Products($view, $flash);
};

$container['OrderController'] = function ($c) {
    $view = $c->get('view');
    $flash = $c->get('flash');
    return new \App\Controller\Orders($view, $flash);
};

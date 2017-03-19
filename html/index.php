<?php 

require_once 'vendor/autoload.php';

$app = new Slim\App();

$app->get('/', function ($request, $response, $arguments) {
	return $response->withJson(array(
		'success' => true
	));
});

$app->run();

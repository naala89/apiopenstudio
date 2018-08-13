<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin;

// Login
$app->get('/login', function (Request $request, Response $response) {
  return $this->get('view')->render($response, 'login.twig');
})->setName('route.login');

$app->post('/login', function (Request $request, Response $response) {
  $message = $request->getAttribute('foo');
  return $this->get('view')->render($response, 'login.twig', ['message' => $message]);
})->setName('route.login');

$app->get('/', function (Request $request, Response $response) {
  $response->getBody()->write("It works! This is the default welcome page.");
  return $response;
})->setName('route.home')
  ->add(new Admin\Middleware\Authentication($settings, '/login'));

$app->get('/hello/{name}', function (Request $request, Response $response) {
  $name = $request->getAttribute('name');
  $response->getBody()->write("Hello, $name");
  return $response;
});

$app->get('/time', function (Request $request, Response $response) {
  $viewData = [
    'now' => date('Y-m-d H:i:s')
  ];
  return $this->get('view')->render($response, 'time.twig', $viewData);
});

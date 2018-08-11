<?php

/** @var Slim\App $app */
$app = require dirname(__DIR__) . '/../includes/admin/bootstrap.php';

// Start
$app->run();

//require_once dirname(__DIR__) . '/../vendor/autoload.php';
//
//use \Psr\Http\Message\ServerRequestInterface;
//use \Psr\Http\Message\ResponseInterface;
//use Datagator\Config;
//
//Config::load();
//$app = new \Slim\App();
//
//$container = $app->getContainer();
//$container['view'] = function ($container) {
//  $view = new \Slim\Views\Twig(Config::$adminTemplates, [
//    'cache' => FALSE//Config::$twigCache
//  ]);
//  // Instantiate and add Slim specific extension
//  $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
//  $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
//  return $view;
//};
//
//$app->get('/hello/{name}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
//  return $this->view->render($response, 'hello.html', [
//    'name' => $args['name']
//  ]);
//});
//
//$app->run();

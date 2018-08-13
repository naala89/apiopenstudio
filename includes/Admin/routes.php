<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin;

/**
 * Login.
 */
$app->get('/login', 'Login:login');
$app->post('/login', 'Login:login')->add(new Admin\Middleware\Authentication($settings, '/login'));
$app->get('/logout', 'Login:logout');
$app->post('/logout', 'Login:logout');
//$app->get('/login', function (Request $request, Response $response) {
//  return $this->get('view')->render($response, 'login.twig');
//});
//
//$app->post('/login', function (Request $request, Response $response) {
//  return $this->get('view')->render($response, 'login.twig');
//})->add(new Admin\Middleware\Authentication($settings, '/login'));
//
///**
// * Logout.
// */
//$app->get('/logout', function (Request $request, Response $response) {
//  unset($_SESSION['token']);
//  return $this->get('view')->render($response, 'login.twig');
//});
//
//$app->post('/logout', function (Request $request, Response $response) {
//  unset($_SESSION['token']);
//  return $this->get('view')->render($response, 'login.twig');
//});

/**
 * Home.
 */
$app->get('/', 'Home:index')->add(new Admin\Middleware\Authentication($settings, '/login'));
$app->post('/', 'Home:index')->add(new Admin\Middleware\Authentication($settings, '/login'));

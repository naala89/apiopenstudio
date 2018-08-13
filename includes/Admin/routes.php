<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin;

/**
 * Login.
 */
$app->get('/login', 'Login:login');
$app->post('/login', 'Login:login');
$app->get('/logout', 'Login:logout');
$app->post('/logout', 'Login:logout');

/**
 * Home.
 */
$app->get('/', 'Home:index')->add(new Admin\Middleware\Authentication($settings, '/login'));
$app->post('/', 'Home:index')->add(new Admin\Middleware\Authentication($settings, '/login'));

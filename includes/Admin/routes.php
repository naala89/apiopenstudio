<?php

use Datagator\Admin\Middleware\Authentication;

$container = $app->getContainer();

/**
 * Home.
 */
$app->get('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));

/**
 * Login.
 */
$app->get('/login', 'CtrlLogin:login');
$app->post('/login', 'CtrlLogin:login')->add(new Authentication($container, $settings, '/login'));
$app->get('/logout', 'CtrlLogin:logout');
$app->post('/logout', 'CtrlLogin:logout');

/**
 * Account.
 */
$app->get('/accounts', 'CtrlAccount:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/create', 'CtrlAccount:create')->add(new Authentication($container, $settings, '/login'));


/**
 * Application.
 */
$app->get('/applications', 'CtrlApplication:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/applications/create', 'CtrlApplication:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/applications/edit', 'CtrlApplication:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/applications/delete', 'CtrlApplication:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * User.
 */
$app->get('/users', 'CtrlUser:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/invite', 'CtrlUser:invite')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/register/{token}', 'CtrlUser:register')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/register', 'CtrlUser:register')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/delete/{uaid}', 'CtrlUser:delete')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/edit/{uaid}', 'CtrlUserRole:edit')->add(new Authentication($container, $settings, '/login'));

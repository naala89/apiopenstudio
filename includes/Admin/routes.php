<?php

use Gaterdata\Admin\Middleware\Authentication;

$container = $app->getContainer();

/**
 * Login/logout.
 */
$app->get('/login', 'CtrlLogin:login');
$app->post('/login', 'CtrlLogin:login')->add(new Authentication($container, $settings, '/login'));
$app->get('/logout', 'CtrlLogin:logout');
$app->post('/logout', 'CtrlLogin:logout');

/**
 * Home.
 */
$app->get('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/home', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));

/**
 * Accounts.
 */
$app->get('/accounts', 'CtrlAccount:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/create', 'CtrlAccount:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/edit', 'CtrlAccount:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/delete', 'CtrlAccount:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Applications.
 */
$app->get('/applications', 'CtrlApplication:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/create', 'CtrlApplication:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/edit', 'CtrlApplication:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/delete', 'CtrlApplication:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Users.
 */
$app->get('/users', 'CtrlUsers:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/create', 'CtrlUser:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/create', 'CtrlUser:create')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/view/{uid}', 'CtrlUser:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/edit/{uid}', 'CtrlUser:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/edit/{uid}', 'CtrlUser:update')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/delete/{uid}', 'CtrlUser:delete')->add(new Authentication($container, $settings, '/login'));
//$app->post('/user/invite', 'CtrlUser:invite')->add(new Authentication($container, $settings, '/login'));
//$app->get('/user/register_token/{token}', 'CtrlUser:register')->add(new Authentication($container, $settings, '/login'));
//$app->post('/user/register', 'CtrlUser:register')->add(new Authentication($container, $settings, '/login'));

/**
 * User roles
 */
$app->get('/user/roles', 'CtrlUserRole:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/role/create', 'CtrlUserRole:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/role/delete', 'CtrlUserRole:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Roles.
 */
//$app->get('/roles', 'CtrRole:index')->add(new Authentication($container, $settings, '/login'));

/**
 * Vars.
 */

/**
 * Resources.
 */
$app->get('/resources', 'CtrlResource:index')->add(new Authentication($container, $settings, '/login'));

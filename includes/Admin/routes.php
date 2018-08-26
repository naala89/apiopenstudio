<?php

use Datagator\Admin\Middleware\Authentication;

/**
 * Home.
 */
$app->get('/', 'CtrlHome:index')->add(new Authentication($settings, '/login'));
$app->post('/', 'CtrlHome:index')->add(new Authentication($settings, '/login'));

/**
 * Login.
 */
$app->get('/login', 'CtrlLogin:login');
$app->post('/login', 'CtrlLogin:login');
$app->get('/logout', 'CtrlLogin:logout');
$app->post('/logout', 'CtrlLogin:logout');

/**
 * Application.
 */
$app->get('/applications', 'CtrlApplication:index')->add(new Authentication($settings, '/login'));
$app->post('/applications/create', 'CtrlApplication:create')->add(new Authentication($settings, '/login'));
$app->post('/applications/edit', 'CtrlApplication:edit')->add(new Authentication($settings, '/login'));
$app->post('/applications/delete', 'CtrlApplication:delete')->add(new Authentication($settings, '/login'));

/**
 * Users.
 */
$app->get('/users', 'CtrlUser:index')->add(new Authentication($settings, '/login'));
$app->post('/user/invite', 'CtrlUser:invite')->add(new Authentication($settings, '/login'));
$app->get('/user/register/{token}', 'CtrlUser:register');
$app->post('/user/register', 'CtrlUser:register');

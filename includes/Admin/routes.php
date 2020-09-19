<?php
/**
 * Slim PHP routes file for GaterData admin.
 *
 * @package   Gaterdata
 * @license   This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *            If a copy of the MPL was not distributed with this file,
 *            You can obtain one at https://mozilla.org/MPL/2.0/.
 * @author    john89 (https://gitlab.com/john89)
 * @copyright 2020-2030 GaterData
 * @link      https://gaterdata.com
 */

use Gaterdata\Admin\Middleware\Authentication;

$container = $app->getContainer();

/**
 * Applications.
 */
$app->get('/applications', 'CtrlApplication:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/create', 'CtrlApplication:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/edit', 'CtrlApplication:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/application/delete', 'CtrlApplication:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Accounts.
 */
$app->get('/accounts', 'CtrlAccount:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/create', 'CtrlAccount:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/edit', 'CtrlAccount:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/account/delete', 'CtrlAccount:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Home.
 */
$app->get('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/home', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/', 'CtrlHome:index')->add(new Authentication($container, $settings, '/login'));

/**
 * Invites
 */
$app->get('/invites', 'CtrlInvite:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/invite', 'CtrlInvite:invite')->add(new Authentication($container, $settings, '/login'));
$app->get('/invite/delete/{iid}', 'CtrlInvite:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Login/logout and non-authenticated calls
 */
$app->get('/login', 'CtrlLogin:login');
$app->post('/login', 'CtrlLogin:login')->add(new Authentication($container, $settings, '/login'));
$app->get('/logout', 'CtrlLogin:logout');
$app->post('/logout', 'CtrlLogin:logout');
$app->get('/invite/accept/{token}', 'CtrlLogin:inviteAccept');
$app->get('/password/reset', 'CtrlLogin:passwordReset');
$app->post('/password/reset', 'CtrlLogin:passwordReset');
$app->get('/password/set/{token}', 'CtrlLogin:setPassword');
$app->post('/password/set', 'CtrlLogin:setPassword');

/**
 * Resources.
 */
$app->get('/resources', 'CtrlResource:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/resource/create', 'CtrlResource:create')->add(new Authentication($container, $settings, '/login'));
$app->get('/resource/edit/{resid}', 'CtrlResource:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/resource/upload', 'CtrlResource:upload')->add(new Authentication($container, $settings, '/login'));
$app->post('/resource/delete', 'CtrlResource:delete')->add(new Authentication($container, $settings, '/login'));
$app->get('/resource/download/{format}/{resid}', 'CtrlResource:download')
    ->add(new Authentication($container, $settings, '/login'));
$app->post('/resource/import', 'CtrlResource:import')->add(new Authentication($container, $settings, '/login'));

/**
 * Roles.
 */
$app->get('/roles', 'CtrlRole:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/role/create', 'CtrlRole:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/role/update', 'CtrlRole:update')->add(new Authentication($container, $settings, '/login'));
$app->post('/role/delete', 'CtrlRole:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Users.
 */
$app->get('/users', 'CtrlUsers:index')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/create', 'CtrlUser:create')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/view/{uid}', 'CtrlUser:view')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/edit/{uid}', 'CtrlUser:edit')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/upload', 'CtrlUser:upload')->add(new Authentication($container, $settings, '/login'));
$app->get('/user/delete/{uid}', 'CtrlUser:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * User roles
 */
$app->get('/user/roles', 'CtrlUserRole:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/role/create', 'CtrlUserRole:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/user/role/delete', 'CtrlUserRole:delete')->add(new Authentication($container, $settings, '/login'));

/**
 * Vars.
 */
$app->get('/vars', 'CtrlVars:index')->add(new Authentication($container, $settings, '/login'));
$app->post('/var/create', 'CtrlVars:create')->add(new Authentication($container, $settings, '/login'));
$app->post('/var/edit', 'CtrlVars:update')->add(new Authentication($container, $settings, '/login'));
$app->post('/var/delete', 'CtrlVars:delete')->add(new Authentication($container, $settings, '/login'));

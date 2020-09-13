<?php
/**
 * Class CtrlRole.
 *
 * @package Gaterdata
 * @subpackage Admin\Controllers
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Admin\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlRole.
 *
 * Controller for the roles page.
 */
class CtrlRole extends CtrlBase
{
    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    protected $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
    ];

    /**
     * List user roles.
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allParams = $request->getParams();

        $query = [];
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['direction'])) {
            $query['order_by'] = 'name';
            $query['direction'] = $allParams['direction'];
        }

        try {
            $result = $this->apiCall('get', 'role/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => $query,
                ]
            );
            $roles = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow($e->getMessage());
            $roles = [];
        }

        // Pagination.
        $page = isset($allParams['page']) ? $allParams['page'] : 1;
        $pages = ceil(count($roles) / $this->settings['admin']['pagination_step']);
        $roles = array_slice(
            $roles,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true
        );

        return $this->view->render($response, 'roles.twig', [
            'menu' => $menu,
            'roles' => $roles,
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Create a role
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allParams = $request->getParams();

        $name = !empty($allParams['name']) ? $allParams['name'] : '';

        try {
            $result = $this->apiCall('post', "role",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => ['name' => $name],
                ]
            );
            $success = json_decode($result->getBody()->getContents(), true);
            if ($success == 'true') {
                $this->flash->addMessageNow('info', 'Role successfully created.');
            } else {
                $this->flash->addMessageNow('error', 'Role creation failed, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }

    /**
     * Update a role
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function update(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allParams = $request->getParams();

        $name = !empty($allParams['name']) ? $allParams['name'] : '';
        $rid = !empty($allParams['rid']) ? $allParams['rid'] : '';

        try {
            $result = $this->apiCall('put', 'role',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'json' => ['rid' => $rid, 'name' => $name],
                ]
            );
            $success = json_decode($result->getBody()->getContents(), true);
            if ($success == 'true') {
                $this->flash->addMessageNow('info', 'Role successfully updated.');
            } else {
                $this->flash->addMessageNow('error', 'Role update failed, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }

    /**
     * Delete a role
     *
     * @param Request $request Request object.
     * @param Response $response Response object.
     * @param array $args Request args.
     *
     * @return ResponseInterface Response.
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allParams = $request->getParams();

        $rid = !empty($allParams['rid']) ? $allParams['rid'] : '';

        try {
            $result = $this->apiCall('delete', "role/$rid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $success = json_decode($result->getBody()->getContents(), true);
            if ($success == 'true') {
                $this->flash->addMessageNow('info', 'Role successfully deleted.');
            } else {
                $this->flash->addMessageNow('error', 'Role deletion failed, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }
}

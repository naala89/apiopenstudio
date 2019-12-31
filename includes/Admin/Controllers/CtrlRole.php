<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Gaterdata\Admin\Account;
use Gaterdata\Admin\Application;
use Gaterdata\Admin\UserAccount;
use Gaterdata\Admin\UserRole;

/**
 * Class CtrlRole.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlRole extends CtrlBase
{

    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    const PERMITTED_ROLES = [
        'Administrator',
        'Account manager',
        'Application manager',
    ];

    /**
     * List user roles.
     *
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
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
                ],
                $response
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
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
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
                ],
                $response
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
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function update(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
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
                ],
                $response
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
     * @param Request $request
     *   Request object.
     * @param Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return ResponseInterface
     *   Response.
     *
     * @throws GuzzleException
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allParams = $request->getParams();

        $rid = !empty($allParams['rid']) ? $allParams['rid'] : '';

        try {
            $result = $this->apiCall('delete', "role/$rid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ],
                $response
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

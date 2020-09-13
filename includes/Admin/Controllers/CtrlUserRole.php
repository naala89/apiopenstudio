<?php
/**
 * Class CtrlUserRole.
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
use Gaterdata\Admin\Account;
use Gaterdata\Admin\Application;
use Gaterdata\Admin\UserAccount;
use Gaterdata\Admin\UserRole;

/**
 * Class CtrlUserRole.
 *
 * Controller for the user role pages.
 */
class CtrlUserRole extends CtrlBase
{
    /**
     * {@inheritdoc}
     *
     * @var array Roles permitted to view these pages.
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
            $this->flash->addMessage('error', 'View user roles: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $params = $request->getQueryParams();
        $token = $_SESSION['token'];

        try {
            $result = $this->apiCall('GET', 'user/role', [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                'query' => $params,
            ]);
            $userRoles = json_decode($result->getBody()->getContents(), true);

            $result = $this->apiCall('GET', 'user', [
                'headers' => ['Authorization' => "Bearer $token"],
            ]);
            $users = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
            return $this->view->render($response, 'user-roles.twig', [
                'menu' => $menu,
                'user_roles' => [],
                'messages' => $this->flash->getMessages(),
            ]);
        }

        $sortedUsers = [];
        foreach ($users as $user) {
            $sortedUsers[$user['uid']] = $user;
        }
        $sortedAccounts = [];
        foreach ($this->userAccounts as $account) {
            $sortedAccounts[$account['accid']] = $account;
        }

        return $this->view->render($response, 'user-roles.twig', [
            'menu' => $menu,
            'params' => $params,
            'user_roles' => $userRoles,
            'accounts' => $sortedAccounts,
            'applications' => $this->userApplications,
            'users' => $sortedUsers,
            'roles'=> $this->allRoles,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Create a user role.
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
            $this->flash->addMessage('error', 'Create user role: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $token = $_SESSION['token'];

        try {
            $this->apiCall('POST', 'user/role', [
            'headers' => [
                    'Authorization' => "Bearer $token",
                ],
                    'form_params' => [
                    'uid' => $allPostVars['uid'],
                    'accid' => $allPostVars['accid'],
                    'appid' => $allPostVars['appid'],
                    'rid' => $allPostVars['rid'],
                ],
            ]);
        } catch (\Exception $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                return $response->withStatus(302)->withHeader('Location', '/login');
                break;
                default:
                return $response->withStatus(302)->withHeader('Location', '/user/roles');
                break;
            }
        }

        $this->flash->addMessage('info', 'User role created.');
        return $response->withStatus(302)->withHeader('Location', '/user/roles');
    }

    /**
     * Delete a user role.
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
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        $urid = $allPostVars['urid'];
        if (empty($urid)) {
            $this->flash->addMessage('error', 'Cannot delete user role, user role unspecified.');
            return $response->withStatus(302)->withHeader('Location', '/user/roles');
        }

        $token = $_SESSION['token'];

        try {
            $this->apiCall('DELETE', 'user/role/' . $urid, [
                'headers' => [
                    'Authorization' => "Bearer $token",
                ],
            ]);
        } catch (\Exception $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', $this->getErrorMessage($e));
            switch ($result->getStatusCode()) {
                case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                default:
                    return $response->withStatus(302)->withHeader('Location', '/user/roles');
                    break;
            }
        }

        $this->flash->addMessage('info', 'User role deleted.');
        return $response->withStatus(302)->withHeader('Location', '/user/roles');
    }
}

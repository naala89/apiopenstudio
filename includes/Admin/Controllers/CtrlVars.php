<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use phpmailerException;

/**
 * Class CtrlVars.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlVars extends CtrlBase
{

    /**
     * Roles allowed to visit the page.
     *
     * @var array
     */
    const PERMITTED_ROLES = [
        'Application manager',
        'Developer'
    ];

    /**
     * List vars
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
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $applications = $this->getApplications($response);

        try {
            $result = $this->apiCall('get', 'var_store/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ],
                $response,
                );
            $vars = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'role/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => ['uid' => $_SESSION['uid']],
                ],
                $response,
                );
            $roles = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'user/role',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => ['uid' => $_SESSION['uid']],
                ],
                $response,
                );
            $userRoles = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'account/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ],
                $response,
                );
            $accounts = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'applications/all',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ],
                $response,
                );
            $applications = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        $filteredAccounts = $filteredApplications = $permittedRoles = [];
        foreach ($roles as $role) {
            if (in_array($role['name'], self::PERMITTED_ROLES)) {
                $permittedRoles[$role['rid']] = $role['name'];
            }
        }
        foreach ($userRoles as $userRole) {
            if (isset($permittedRoles[$userRole['rid']]) && !isset($filteredApplications[$userRole['appid']])) {
                $filteredApplications[$userRole['appid']] = (array) $applications->{$userRole['appid']};
            }
        }
        foreach ($filteredApplications as $filteredApplication) {
            if (!isset($filteredAccounts[$filteredAccount['accid']])) {
                $filteredAccounts[$filteredApplication['accid']] = $accounts[$filteredApplication['accid']];
            }
        }

        return $this->view->render($response, 'vars.twig', [
            'menu' => $menu,
            'vars' => $vars,
            'applications' => $filteredApplications,
            'accounts' => $filteredAccounts,
        ]);
    }

    /**
     * Create vars
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
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();
        $params = [];
        if (isset($allPostVars['appid'])) {
            $params['appid'] = $allPostVars['appid'];
        }
        if (isset($allPostVars['key'])) {
            $params['key'] = $allPostVars['key'];
        }
        if (isset($allPostVars['val'])) {
            $params['val'] = $allPostVars['val'];
        }

        try {
            $result = $this->apiCall('post', 'var_store',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => $params,
                ],
                $response,
                );
            if (json_decode($result->getBody()->getContents() == 'true')) {
                $this->flash->addMessage('info', 'Var successfully created.');
            } else {
                $this->flash->addMessage('error', 'Some went wrong, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }
}

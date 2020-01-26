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
    protected $permittedRoles = [
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
     * @throws \Exception
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access vars: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allParams = $request->getParams();
        $query = [];
        if (isset($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (isset($allParams['order_by'])) {
            $query['order_by'] = $allParams['order_by'];
        }
        if (isset($allParams['direction'])) {
            $query['direction'] = $allParams['direction'];
        }

        $applications = $this->getApplications($query);

        try {
            $result = $this->apiCall('get', 'var_store/all', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);
            $vars = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'role/all', [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'query' => ['uid' => $_SESSION['uid']],
                ]);
            $roles = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'user/role', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => ['uid' => $_SESSION['uid']],
            ]);
            $userRoles = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'account/all', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ]);
            $accounts = json_decode($result->getBody()->getContents(), true);
            $result = $this->apiCall('get', 'application', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ]);
            $applications = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        $filteredAccounts = $filteredApplications = $permittedRoles = $filtereredVars = [];
        foreach ($roles as $role) {
            if (in_array($role['name'], self::PERMITTED_ROLES)) {
                $permittedRoles[$role['rid']] = $role['name'];
            }
        }
        foreach ($userRoles as $userRole) {
            if (isset($permittedRoles[$userRole['rid']]) && !isset($filteredApplications[$userRole['appid']])) {
                $filteredApplications[$userRole['appid']] = $applications[$userRole['appid']];
            }
        }
        foreach ($filteredApplications as $filteredApplication) {
            if (!isset($filteredAccounts[$filteredAccount['accid']])) {
                $filteredAccounts[$filteredApplication['accid']] = $accounts[$filteredApplication['accid']];
            }
        }
        if (!empty($allParams['filter_by'])) {
            foreach ($vars as $vid => $var) {
                if ($var['appid'] == $allParams['filter_by']) {
                    $filtereredVars[$vid] = $var;
                }
            }
        } else {
            $filtereredVars = $vars;
        }

        // Filter by application.
        $filterBy = [];
        foreach ($filteredApplications as $appid => $application) {
            $filterBy[$appid] = $filteredAccounts[$application['accid']] . ' - ' . $application['name'];
        }

        // Pagination.
        $page = isset($params['page']) ? $allParams['page'] : 1;
        $pages = ceil(count($vars) / $this->settings['admin']['pagination_step']);
        $users = array_slice($vars,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true);

        return $this->view->render($response, 'vars.twig', [
            'menu' => $menu,
            'vars' => $filtereredVars,
            'applications' => $filteredApplications,
            'accounts' => $filteredAccounts,
            'filterBy' => $filterBy,
            'filter_by' => $allParams['filter_by'],
            'order_by' => $allParams['order_by'],
            'direction' => $allParams['direction'],
            'keyword' => $allParams['keyword'],
            'page' => $page,
            'pages' => $pages,
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Create var
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
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();
        $params = [];
        if (isset($allPostVars['create-var-appid'])) {
            $params['appid'] = $allPostVars['create-var-appid'];
        }
        if (isset($allPostVars['key'])) {
            $params['key'] = $allPostVars['create-var-key'];
        }
        if (isset($allPostVars['val'])) {
            $params['val'] = $allPostVars['create-var-val'];
        }

        try {
            $result = $this->apiCall('post', 'var_store', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'form_params' => $params,
            ]);
            if (json_decode($result->getBody()->getContents() == 'true')) {
                $this->flash->addMessageNow('info', 'Var successfully created.');
            } else {
                $this->flash->addMessageNow('error', 'Var not created, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }

    /**
     * Update var
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
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();
        $params = [];
        if (isset($allPostVars['edit-var-vid'])) {
            $params['vid'] = $allPostVars['edit-var-vid'];
        }
        if (isset($allPostVars['edit-var-val'])) {
            $params['val'] = $allPostVars['edit-var-val'];
        }

        try {
            $result = $this->apiCall('put', 'var_store/' . $params['vid'], [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'body' => $params['val'],
            ]);
            if (json_decode($result->getBody()->getContents() == 'true')) {
                $this->flash->addMessageNow('info', 'Var successfully updated.');
            } else {
                $this->flash->addMessageNow('error', 'Var not updated, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }

    /**
     * Delete var
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
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Access admin: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $menu = $this->getMenus();
        $allPostVars = $request->getParams();
        $vid = isset($allPostVars['delete-var-vid']) ? $allPostVars['delete-var-vid'] : '';

        if (empty($vid)) {
            $this->flash->addMessageNow('error', 'Var not deleted, no vid received.');
            return $this->index($request, $response, $args);
        }

        try {
            $result = $this->apiCall('delete', "var_store/$vid", [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
            ]);
            if (json_decode($result->getBody()->getContents() == 'true')) {
                $this->flash->addMessageNow('info', 'Var successfully deleted.');
            } else {
                $this->flash->addMessageNow('error', 'Var not deleted, please check the logs.');
            }
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        return $this->index($request, $response, $args);
    }
}

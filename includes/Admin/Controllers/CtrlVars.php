<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

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
        'Developer',
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
        $allParams['filter_by_account'] = !empty($allParams['filter_by_application'])
            ? ''
            : $allParams['filter_by_account'];

        $query = [];
        if (!empty($allParams['filter_by_application'])) {
            $query['appid'] = $allParams['filter_by_application'];
        }
        if (!empty($allParams['keyword'])) {
            $query['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['order_by']) && $allParams['order_by'] != 'account') {
            $query['order_by'] = $allParams['order_by'];
        }
        if (!empty($allParams['direction'])) {
            $query['direction'] = $allParams['direction'];
        }

        try {
            $result = $this->apiCall('get', 'var_store', [
                'headers' => [
                    'Authorization' => "Bearer " . $_SESSION['token'],
                    'Accept' => 'application/json',
                ],
                'query' => $query,
            ]);
            $vars = json_decode($result->getBody()->getContents(), true);
        } catch (\Exception $e) {
            $this->flash->addMessageNow('error', $e->getMessage());
        }

        // Filter by account.
        if (!empty($allParams['filter_by_account'])) {
            $filterApps = [];
            foreach ($this->userAccounts as $userAccount) {
                if ($userAccount['accid'] == $allParams['filter_by_account']) {
                    foreach ($this->userApplications as $userApplication) {
                        if ($userApplication['accid'] == $userAccount['accid']) {
                            $filterApps[] = $userApplication['appid'];
                        }
                    }
                }
            }
            foreach ($vars as $index => $var) {
                if (!in_array($var['appid'], $filterApps)) {
                    unset($vars[$index]);
                }
            }
        }

        $sortedVars = [];
        if ($allParams['order_by'] == 'account') {
            // Sort by account name.
            foreach ($this->userAccounts as $userAccount) {
                foreach ($this->userApplications as $userApplication) {
                    foreach ($vars as $index => $var) {
                        if ($userAccount['accid'] == $userApplication['accid']
                                && $userApplication['appid'] == $var['appid']) {
                            $sortedVars[] = $var;
                            unset($vars[$index]);
                        }
                    }
                }
            }
        } elseif ($allParams['order_by'] == 'application') {
            // Sort by application name.
            foreach ($this->userApplications as $userApplication) {
                foreach ($vars as $index => $resource) {
                    if ($userApplication['appid'] == $var['appid']) {
                        $sortedVars[] = $var;
                        unset($vars[$index]);
                    }
                }
            }
        } else {
            // All other sorts.
            $sortedVars = $vars;
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
            'vars' => $sortedVars,
            'applications' => $this->userApplications,
            'accounts' => $this->userAccounts,
            'params' => $allParams,
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
        if (isset($allPostVars['create-var-key'])) {
            $params['key'] = $allPostVars['create-var-key'];
        }
        if (isset($allPostVars['create-var-val'])) {
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

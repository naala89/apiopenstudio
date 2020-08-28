<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class CtrlAccount.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlAccount extends CtrlBase
{

    /**
     * {@inheritdoc}
     */
    protected $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
        'Developer',
    ];

    /**
     * Accounts page.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     *   Response.
     *
     * @throws \Exception
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View accounts: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
    
        $menu = $this->getMenus();

        // Filter params and current page.
        $allParams = $request->getParams();
        $params = [];
        if (!empty($allParams['keyword'])) {
            $params['keyword'] = $allParams['keyword'];
        }
        $params['order_by'] = 'name';
        $params['direction'] = isset($allParams['direction']) ? $allParams['direction'] : 'asc';
        $page = isset($allParams['page']) ? $allParams['page'] : 1;

        $accounts = $this->apiCallAccountAll($params);

        // Get total number of pages and current page's accounts to display.
        $pages = ceil(count($accounts) / $this->settings['admin']['pagination_step']);
        $accounts = array_slice($accounts,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true);

        return $this->view->render($response, 'accounts.twig', [
            'keyword' => isset($params['keyword']) ? $params['keyword'] : '',
            'direction' => strtoupper($params['direction']),
            'page' => $page,
            'pages' => $pages,
            'menu' => $menu,
            'accounts' => $accounts,
            'isAdmin' => in_array('Administrator', $this->userRoles),
            'messages' => $this->flash->getMessages(),
        ]);
    }

    /**
     * Create an account.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return Response
     *   Response.
     *
     * @throws \Exception
     */
    public function create(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View accounts: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        // Validate the input.
        $allPostVars = $request->getParsedBody();
        if (empty($name = $allPostVars['name'])) {
            $this->flash->addMessage('error', 'Cannot create account, no name defined.');
            return $response->withRedirect('/accounts');
        }
        // Create the new account.
        try {
            $result = $this->apiCall(
                'post',
                'account',
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'name' => $name,
                    ],
                ]
            );
            if (json_decode($result->getBody()->getContents()) == 'true') {
                $this->flash->addMessage('info', "Account $name created");
            } else {
                $this->flash->addMessage('error', "Account $name creation failed, check the logs for details.");
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }

        return $response->withStatus(302)->withHeader('Location', '/accounts');
    }

    /**
     * Edit an account.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return Response
     *   Response.
     *
     * @throws \Exception
     */
    public function edit(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View accounts: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        // Validate the input.
        $allPostVars = $request->getParsedBody();
        if (empty($accid = $allPostVars['accid']) || empty($name = $allPostVars['name'])) {
            $this->flash->addMessage('error', 'Cannot edit account, invalid accid or name.');
            return $response->withRedirect('/accounts');
        }

        // Edit the account.
        try {
            $result = $this->apiCall(
                'put',
                "account/$accid/" . urlencode($name),
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                    'form_params' => [
                        'name' => $name,
                    ],
                ],
            );
            if (json_decode($result->getBody()->getContents()) == 'true') {
                $this->flash->addMessage('info', "Account '$accid' updated to '$name'.");
            } else {
                $this->flash->addMessage(
                    'error',
                    "Account '$accid' update to '$name failed, check the log for details.'"
                );
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }
        return $response->withStatus(302)->withHeader('Location', '/accounts');
    }

    /**
     * Delete an account.
     *
     * @param \Slim\Http\Request $request
     *   Request object.
     * @param \Slim\Http\Response $response
     *   Response object.
     * @param array $args
     *   Request args.
     *
     * @return Response
     *   Response.
     *
     * @throws \Exception
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View accounts: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        // Validate the input.
        $allPostVars = $request->getParsedBody();
        if (empty($accid = $allPostVars['accid'])) {
            $this->flash->addMessage('error', 'Cannot delete account, no accid defined.');
            return $response->withRedirect('/accounts');
        }

        try {
            $result = $this->apiCall(
                'delete',
                "account/$accid",
                [
                    'headers' => [
                        'Authorization' => "Bearer " . $_SESSION['token'],
                        'Accept' => 'application/json',
                    ],
                ]
            );
            if (json_decode($result->getBody()->getContents()) == 'true') {
                $this->flash->addMessage('info', "Account '$accid' deleted.");
            } else {
                $this->flash->addMessage('error', "Account '$accid' delete failed, check the log for details.'");
            }
        } catch (\Exception $e) {
            $this->flash->addMessage('error', $e->getMessage());
        }
        return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
}

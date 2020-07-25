<?php

namespace Gaterdata\Admin\Controllers;

use mysql_xdevapi\Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Gaterdata\Core\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class CtrlApplication.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlApplication extends CtrlBase
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
     * Applications page.
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
            $this->flash->addMessage('error', 'View applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        // Filter params and current page.
        $allParams = $request->getParams();
        $appParams = [];
        if (!empty($allParams['keyword'])) {
            $appParams['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['account_id'])) {
            $appParams['account_id'] = $allParams['account_id'];
        }
        $appParams['order_by'] = 'name';
        $appParams['direction'] = isset($allParams['direction']) ? $allParams['direction'] : 'asc';

        $accParams = [
            'order_by' => 'name',
            'direction' => isset($allParams['direction']) ? $allParams['direction'] : 'asc',
        ];

        $page = isset($allParams['page']) ? $allParams['page'] : 1;
    
        $menu = $this->getMenus();
        $accounts = $this->apiCallAccountAll($accParams);
        $applications = $this->apiCallApplicationAll($appParams);

        $sortedAccs = [];
        foreach ($accounts as $account) {
            $sortedAccs[$account['accid']] = $account['name'];
        }

        // Order by account name or app name.
        $sortedApps = [];
        if ($allParams['order_by'] == 'account') {
            foreach ($accounts as $accid => $account) {
                foreach ($applications as $appid => $application) {
                    if ($accid == $application['accid']) {
                        $application['account'] = $accounts[$application['accid']];
                        $sortedApps[$appid] = $application;
                    }
                }
            }
        } else {
            foreach ($applications as $appid => $application) {
                $application['account'] = $accounts[$application['accid']];
                $sortedApps[$appid] = $application;
            }
        }

        // Get total number of pages and current page's applications to display.
        $pages = ceil(count($sortedApps) / $this->settings['admin']['pagination_step']);
        $sortedApps = array_slice($sortedApps,
            ($page - 1) * $this->settings['admin']['pagination_step'],
            $this->settings['admin']['pagination_step'],
            true
        );

        return $this->view->render($response, 'applications.twig', [
            'menu' => $menu,
            'params' => $allParams,
            'page' => $page,
            'pages' => $pages,
            'accounts' => $sortedAccs,
            'applications' => $sortedApps,
            'messages' => $this->flash->getMessages(),
            'roles' => $this->userRoles,
        ]);
    }

    /**
     * Create an application.
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
            $this->flash->addMessage('error', 'Create applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($appName = $allPostVars['create-app-name']) || empty($accid = $allPostVars['create-app-accid'])) {
            $this->flash->addMessage('error', 'Cannot create application, no name or account ID defined.');
        } else {
            // Create the new account.
            try {
                $result = $this->apiCall(
                    'post',
                    'application',
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                        'form_params' => [
                            'accid' => $accid,
                            'name' => $appName,
                        ],
                    ]
                );
                if (json_decode($result->getBody()->getContents()) == 'true') {
                    $this->flash->addMessage('info', "Application $appName created.");
                } else {
                    $this->flash->addMessage('error', "Application $appName create failed, check the logs for details.");
                }
            } catch (\Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
            }
        }

        return $response->withRedirect('/applications');
    }

    /**
     * Edit an application.
     *
     * @param Request $request
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
            $this->flash->addMessage('error', 'Update applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($appid = $allPostVars['edit-app-appid']) ||
            empty($accid = $allPostVars['edit-app-accid']) ||
            empty($name = $allPostVars['edit-app-name'])) {
            $this->flash->addMessage('error', 'Cannot edit application, Account ID, Application ID or name defined.');
        } else {
            try {
                $result = $this->apiCall(
                    'put',
                    "application/$appid/$accid/$name",
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                    ]
                );
                if (json_decode($result->getBody()->getContents()) == 'true') {
                    $this->flash->addMessage('info', "Application $appid edited.");
                } else {
                    $this->flash->addMessage('error', "Application $appid edit failed, check the logs for details.");
                }
            } catch (\Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
            }
        }

        return $response->withRedirect('/applications');
    }

    /**
     * Delete an application.
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
            $this->flash->addMessage('error', 'Update applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($appid = $allPostVars['delete-app-appid'])) {
            $this->flash->addMessage('error', 'Cannot delete application, application ID not defined.');
        } else {
            try {
                $result = $this->apiCall(
                    'delete',
                    "application/$appid",
                    [
                        'headers' => [
                            'Authorization' => "Bearer " . $_SESSION['token'],
                            'Accept' => 'application/json',
                        ],
                    ]
                );
                if (json_decode($result->getBody()->getContents()) == 'true') {
                    $this->flash->addMessage('info', "Application $appid deleted.");
                } else {
                    $this->flash->addMessage('error', "Application $appid delete failed, check the logs for details.");
                }
            } catch (Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
            }
        }

        return $response->withRedirect('/applications');
    }
}

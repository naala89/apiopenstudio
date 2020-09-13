<?php
/**
 * Class CtrlApplication.
 *
 * @package Gaterdata
 * @subpackage Admin\Controllers
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlApplication.
 *
 * Controller for the application page.
 */
class CtrlApplication extends CtrlBase
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
        'Developer',
    ];

    /**
     * Applications page.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return \Psr\Http\Message\ResponseInterface|Response Response.
     */
    public function index(Request $request, Response $response, array $args)
    {
        // Validate access.
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'View applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }
        $menu = $this->getMenus();

        // Filter params and current page.
        $allParams = $request->getParams();
        $appParams = $accParams = [];
        if (!empty($allParams['keyword'])) {
            $appParams['keyword'] = $allParams['keyword'];
        }
        if (!empty($allParams['account_id'])) {
            $appParams['account_id'] = $allParams['account_id'];
        }
        if (!empty($allParams['direction'])) {
            $accParams['direction'] = $appParams['direction'] = $allParams['direction'];
        }
        $accParams['order_by'] = $appParams['order_by'] = 'name';
        $page = isset($allParams['page']) ? $allParams['page'] : 1;

        $accounts = $this->apiCallAccountAll($accParams);
        $applications = $this->apiCallApplicationAll($appParams);

        // Order by account name or app name.
        $sortedApps = [];
        if ($allParams['order_by'] == 'account') {
            foreach ($accounts as $account) {
                foreach ($applications as $index => $application) {
                    if ($application['accid'] == $account['accid']) {
                        $sortedApps[] = $application;
                        unset($applications[$index]);
                    }
                }
            }
        } else {
            $sortedApps = $applications;
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
            'accounts' => $accounts,
            'applications' => $sortedApps,
            'messages' => $this->flash->getMessages(),
            'roles' => $this->userRoles,
        ]);
    }

    /**
     * Create an application.
     *
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return Response Response.
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
                    $this->flash->addMessage(
                        'error',
                        "Application $appName create failed, check the logs for details."
                    );
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
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return Response Response.
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
     * @param \Slim\Http\Request $request Request object.
     * @param \Slim\Http\Response $response Response object.
     * @param array $args Request args.
     *
     * @return Response Response.
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
            } catch (\Exception $e) {
                $this->flash->addMessage('error', $e->getMessage());
            }
        }

        return $response->withRedirect('/applications');
    }
}

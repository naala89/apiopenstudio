<?php

namespace Gaterdata\Admin\Controllers;

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
class CtrlApplication extends CtrlBase {

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
   * Applications page.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
    public function index(Request $request, Response $response, array $args)
    {
      // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
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
        if (!empty($allParams['account_filter'])) {
            $appParams['account_filter'] = $allParams['account_filter'];
        }
        $appParams['order_by'] = 'name';
        $appParams['direction'] = isset($allParams['direction']) ? $allParams['direction'] : 'asc';
        $accParams = [
        'order_by' => 'name',
        'direction' => isset($allParams['direction']) ? $allParams['direction'] : 'asc',
        ];
        $page = isset($allParams['page']) ? $allParams['page'] : 1;
    
        $menu = $this->getMenus();
        $accounts = $this->getAccounts($response, $accParams);
        $applications = (array) $this->getApplications($response, $appParams);

      // Order by account or app name.
        $sortedApps = [];
        if ($allParams['order_by'] == 'account') {
            foreach ($accounts as $accid => $account) {
                foreach ($applications as $appid => $application) {
                    if ($accid == $application->accid) {
                        $application->account = $account;
                        $sortedApps[$appid] = $application;
                    }
                }
            }
        } else {
            foreach ($applications as $appid => $application) {
                $application->account = $accounts[$application->accid];
                $sortedApps[$appid] = $application;
            }
        }

      // Get total number of pages and current page's applications to display.
        $pages = ceil(count($sortedApps) / $this->settings['admin']['paginationStep']);
        $sortedApps = array_slice($sortedApps,
        ($page - 1) * $this->settings['admin']['paginationStep'],
        $this->settings['admin']['paginationStep'],
        true);

        return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'params' => $allParams,
        'page' => $page,
        'pages' => $pages,
        'accounts' => $accounts,
        'applications' => $sortedApps,
        'messages' => $this->flash->getMessages(),
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
     * @return \Psr\Http\Message\ResponseInterface
     *   Response.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Request $request, Response $response, array $args)
    {
      // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Create applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($appName = $allPostVars['create-app-name']) || empty($accid = $allPostVars['create-app-accid'])) {
            $this->flash->addMessage('error', 'Cannot create application, no name or account ID defined.');
        } else {
            try {
                $domain = $this->settings['api']['url'];
                $account = $this->settings['api']['core_account'];
                $application = $this->settings['api']['core_application'];
                $token = $_SESSION['token'];
                $client = new Client(['base_uri' => "$domain/$account/$application/"]);

                $result = $client->request('POST', 'application', [
                'headers' => [
                'Authorization' => "Bearer $token",
                ],
                'form_params' => [
                'accid' => $accid,
                'name' => $appName,
                ],
                ]);
                $result = json_decode($result->getBody()->getContents());
                $this->flash->addMessage('info', "Application $appName created.");
            } catch (ClientException $e) {
                $result = $e->getResponse();
                switch ($result->getStatusCode()) {
                    case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                    default:
                        $this->flash->addMessage('error', $this->getErrorMessage($e));
                    break;
                }
            }
        }

        return $response->withRedirect('/applications');
    }

  /**
   * Edit an application.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
    public function edit(Request $request, Response $response, array $args)
    {
      // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
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
                $domain = $this->settings['api']['url'];
                $account = $this->settings['api']['core_account'];
                $application = $this->settings['api']['core_application'];
                $token = $_SESSION['token'];
                $client = new Client(['base_uri' => "$domain/$account/$application/"]);

                $result = $client->request('PUT', "application/$appid/$accid/$name", [
                'headers' => [
                'Authorization' => "Bearer $token",
                ],
                ]);
                $result = json_decode($result->getBody()->getContents());
                $this->flash->addMessage('info', "Application $appid edited.");
            } catch (ClientException $e) {
                $result = $e->getResponse();
                switch ($result->getStatusCode()) {
                    case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                    default:
                        $this->flash->addMessage('error', $this->getErrorMessage($e));
                    break;
                }
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
    public function delete(Request $request, Response $response, array $args)
    {
      // Validate access.
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
        $this->getAccessRights($response, $uid);
        if (!$this->checkAccess()) {
            $this->flash->addMessage('error', 'Update applications: access denied');
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        $allPostVars = $request->getParsedBody();
        if (empty($appid = $allPostVars['delete-app-appid'])) {
            $this->flash->addMessage('error', 'Cannot delete application, application ID not defined.');
        } else {
            try {
                $domain = $this->settings['api']['url'];
                $account = $this->settings['api']['core_account'];
                $application = $this->settings['api']['core_application'];
                $token = $_SESSION['token'];
                $client = new Client(['base_uri' => "$domain/$account/$application/"]);

                $result = $client->request('DELETE', "application/$appid", [
                'headers' => [
                'Authorization' => "Bearer $token",
                ],
                ]);
                $result = json_decode($result->getBody()->getContents());
                $this->flash->addMessage('info', "Application $appid deleted.");
            } catch (ClientException $e) {
                $result = $e->getResponse();
                switch ($result->getStatusCode()) {
                    case 401:
                    return $response->withStatus(302)->withHeader('Location', '/login');
                    break;
                    default:
                        $this->flash->addMessage('error', $this->getErrorMessage($e));
                    break;
                }
            }
        }

        return $response->withRedirect('/applications');
    }

}

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
   */
  public function index(Request $request, Response $response, array $args) {
    // Validate access.
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'View applications: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    // Filter params and currect page.
    $allParams = $request->getParams();
    $params = [];
    if (!empty($allParams['keyword'])) {
      $params['keyword'] = $allParams['keyword'];
    }
    if (!empty($allParams['account_filter'])) {
      $params['account_filter'] = $allParams['account_filter'];
    }
    $params['order_by'] = !empty($allParams['order_by']) ? $allParams['order_by'] : 'name';
    $params['direction'] = isset($allParams['direction']) ? $allParams['direction'] : 'asc';
    $page = isset($allParams['page']) ? $allParams['page'] : 1;
    
    $menu = $this->getMenus();
    $accounts = $this->getAccounts($response);
    $applications = (array) $this->getApplications($response, $params);

    // Get total number of pages and current page's applications to display.
    // $pages = ceil(count($applications) / $this->paginationStep);
    // $applications = array_slice($applications, ($page - 1) * $this->paginationStep, $this->paginationStep, TRUE);

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'params' => $params,
      'page' => 1,
      'pages' => 1,
      'accounts' => $accounts,
      'applications' => $applications,
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
   */
  public function create(Request $request, Response $response, array $args) {
    // Validate access.
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
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
   */
  public function edit(Request $request, Response $response, array $args) {
    // Validate access.
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Update applications: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($appid = $allPostVars['edit-app-appid']) || empty($accid = $allPostVars['edit-app-accid']) || empty($name = $allPostVars['edit-app-name'])) {
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
   * TODO: Delete all associated resources and remove user roles.
   */
  public function delete(Request $request, Response $response, array $args) {
    // Validate access.
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
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

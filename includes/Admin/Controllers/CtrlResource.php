<?php

namespace Gaterdata\Admin\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class CtrlResource.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlResource extends CtrlBase {

  /**
   * Roles allowed to visit the page.
   * 
   * @var array
   */
  const PERMITTED_ROLES = [
    'Developer',
  ];

  /**
   * Resources page.
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
  public function index(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'View accounts: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();
    $applications = $this->getApplications($response, []);
    $appids = implode(',', array_keys((array) $applications));
    $allParams = $request->getParams();

    $query = [];
    if (!empty($allParams['keyword'])) {
      $query['keyword'] = $allParams['keyword'];
    }
    if (!empty($allParams['order_by'])) {
      $query['order_by'] = $allParams['order_by'];
    }
    if (!empty($allParams['direction'])) {
      $query['direction'] = $allParams['direction'];
    }
    if (!empty($appids)) {
      $query['app_id'] = $appids;
    }

    $domain = $this->settings['api']['url'];
    $account = $this->settings['api']['core_account'];
    $application = $this->settings['api']['core_application'];
    $token = $_SESSION['token'];
    $client = new Client(['base_uri' => "$domain/$account/$application/"]);

    try {
      $result = $client->request('GET', 'resource', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => $query,
      ]);
      $resources = (array) json_decode($result->getBody()->getContents());

      $result = $client->request('GET', 'account/all', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $accounts = (array) json_decode($result->getBody()->getContents());
    }
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401:
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $resources = [];
          $accounts = [];
          $applications = [];
          break;
      }
    }

    // Pagination.
    $page = isset($allParams['page']) ? $allParams['page'] : 1;
    $pages = ceil(count($resources) / $this->settings['admin']['paginationStep']);
    $resources = array_slice($resources,
      ($page - 1) * $this->settings['admin']['paginationStep'],
      $this->settings['admin']['paginationStep'],
      TRUE);

    return $this->view->render($response, 'resources.twig', [
      'menu' => $menu,
      'params' => $query,
      'resources' => $resources,
      'page' => $page,
      'pages' => $pages,
      'accounts' => $accounts,
      'applications' => (array) $applications,
      'messages' => $this->flash->getMessages(),
    ]);
  }

  /**
   * Create a resource page.
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
  public function create(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'View accounts: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();

    $domain = $this->settings['api']['url'];
    $account = $this->settings['api']['core_account'];
    $application = $this->settings['api']['core_application'];
    $token = $_SESSION['token'];
    $client = new Client(['base_uri' => "$domain/$account/$application/"]);

    try {
      $result = $client->request('GET', 'functions/all', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $functions = json_decode($result->getBody()->getContents(), TRUE);
      $result = $client->request('GET', 'account/all', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $accounts = json_decode($result->getBody()->getContents(), TRUE);
      $result = $client->request('GET', 'application', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $applications = json_decode($result->getBody()->getContents(), TRUE);
    }
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401:
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $accounts = [];
          $applications = [];
          $functions = [];
          break;
      }
    }

    $sortedFunctions = [];
    foreach ($functions as $function) {
      $sortedFunctions[$function['menu']][] = $function;
    }

    return $this->view->render($response, 'resource-create.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'functions' => $sortedFunctions,
      'messages' => $this->flash->getMessages(),
    ]);
  }

}

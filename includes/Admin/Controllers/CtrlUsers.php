<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;
use phpmailerException;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Hash;
use GuzzleHttp\Client;

/**
 * Class CtrlUsers.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlUsers extends CtrlBase {

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
   * Display the users page.
   *
   * @param Request $request
   *   Request object.
   * @param Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function index(Request $request, Response $response, array $args) {
    // Validate access.
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    $menu = $this->getMenus();

    // Filter params.
    $query = [];
    $allParams = $request->getParams();
    if (!empty($allParams['keyword'])) {
      $query['keyword'] = $allParams['keyword'];
    }
    if (!empty($allParams['order_by'])) {
      $query['order_by'] = $allParams['order_by'];
    }
    if (!empty($allParams['direction'])) {
      $query['direction'] = $allParams['direction'];
    }

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('GET', 'user', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => $query,
      ]);
      $users = (array) json_decode($result->getBody()->getContents());
    } 
    catch (ClientException $e) {
      $result = $e->getResponse();
      $this->flash->addMessage('error', $this->getErrorMessage($e));
      switch ($result->getStatusCode()) {
        case 401: 
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $users = [];
          break;
      }
    }

    // Pagination.
    $page = isset($params['page']) ? $allParams['page'] : 1;
    $pages = ceil(count($users) / $this->settings['admin']['paginationStep']);
    $users = array_slice($users,
      ($page - 1) * $this->settings['admin']['paginationStep'],
      $this->settings['admin']['paginationStep'],
      TRUE);

    return $this->view->render($response, 'users.twig', [
      'menu' => $menu,
      'users' => $users,
      'page' => $page,
      'pages' => $pages,
      'params' => $allParams,
      'messages' => $this->flash->getMessages(),
    ]);
  }

}

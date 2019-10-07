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
class CtrlAccount extends CtrlBase {

  protected $permittedRoles = ['Administrator', 'Manager'];

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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function index(Request $request, Response $response, array $args) {
    $this->permittedRoles = ['Administrator', 'Account manager'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'View accounts: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }
    
    $menu = $this->getMenus();
    $roles = $this->getRoles();
    $accounts = $this->getAccounts();

    // Filter params and currect page.
    $allParams = $request->getParams();
    $params = [];
    if (!empty($allParams['keyword'])) {
      $params['keyword'] = $allParams['keyword'];
    }
    $params['order_by'] = !empty($allParams['order_by']) ? $allParams['order_by'] : 'name';
    $params['direction'] = isset($allParams['direction']) ? $allParams['direction'] : 'asc';
    $page = isset($allParams['page']) ? $allParams['page'] : 1;

    try {
      // Fetch the accounts for the page.
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $query = ['accountName' => 'all'];
      foreach($params as $key => $value) {
        $query[$key] = $value;
      } 

      $result = $client->request('GET', 'account', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => $query,
      ]);
      $result = json_decode($result->getBody()->getContents());
      if (!in_array('Administrator', $roles)) {
        $_accounts = array_intersect((array) $result, $accounts);
      } else {
        $_accounts = (array) $result;
      }
      $accounts = [];
      foreach ($_accounts as $accid => $name) {
        $accounts[] = ['accid' => $accid, 'name' => $name];
      }

    } catch (ClientException $e) {
      $result = $e->getResponse();
      switch ($result->getStatusCode()) {
        case 401: 
          return $response->withStatus(302)->withHeader('Location', '/login');
          break;
        default:
          $this->flash->addMessage('error', $this->getErrorMessage($e));
          $accounts = [];
          break;
      }
    }

    // Get total number of pages and current page's accounts to display.
    $pages = ceil(count($accounts) / $this->settings['admin']['paginationStep']);
    $accounts = array_slice($accounts, ($page - 1) * $this->settings['admin']['paginationStep'], $this->settings['admin']['paginationStep'], TRUE);

    return $this->view->render($response, 'accounts.twig', [
      'keyword' => isset($params['keyword']) ? $params['keyword'] : '',
      'order_by' => $params['order_by'],
      'dir' => strtoupper($params['dir']),
      'page' => $page,
      'pages' => $pages,
      'menu' => $menu,
      'accounts' => $accounts,
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function create(Request $request, Response $response, array $args) {
    // Validate user has permissions.
    $this->permittedRoles = ['Administrator'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Create account: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    // Validate the input.
    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['create-acc-name'])) {
      $this->flash->addMessage('error', 'Cannot create account, no name defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      // Create the new account.
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];

      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('POST', 'account', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'form_params' => [
          'accountName' => $name,
        ],
      ]);
      $result = json_decode($result->getBody()->getContents());

      $this->flash->addMessage('info', "Account $name created");
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
    catch (ClientException $e) {
      $message = $this->getErrorMessage($e);
      $this->flash->addMessage('error', $message);
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function edit(Request $request, Response $response, array $args) {
    // Validate user has permissions.
    $this->permittedRoles = ['Administrator'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Delete account: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    // Validate the input.
    $allPostVars = $request->getParsedBody();
    if (empty($newName = $allPostVars['new-acc-name']) || empty($name = $allPostVars['acc-name'])) {
      $this->flash->addMessage('error', 'Cannot edit account, no new or original name defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      // Edit the account.
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];

      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('POST', 'account', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'form_params' => [
          'accountName' => $newName,
          'oldName' => $name,
        ],
      ]);
      $result = json_decode($result->getBody()->getContents());

      $this->flash->addMessage('info', "Account '$name' updated to '$newName'");
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
    catch (ClientException $e) {
      $message = $this->getErrorMessage($e);
      $this->flash->addMessage('error', $message);
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function delete(Request $request, Response $response, array $args) {
    // Validate user has permissions.
    $this->permittedRoles = ['Administrator'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Delete account: access denied');
      return $response->withStatus(302)->withHeader('Location', '/');
    }

    // Validate the input.
    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['acc-name'])) {
      $this->flash->addMessage('error', 'Cannot delete account, no account name defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      // Delete the account.
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];

      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('DELETE', 'account/' . urlencode($name), [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
      ]);
      $result = json_decode($result->getBody()->getContents());

      $this->flash->addMessage('info', "Account '$name' deleted.");
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
    catch (ClientException $e) {
      $message = $this->getErrorMessage($e);
      $this->flash->addMessage('error', $message);
      return $response->withStatus(302)->withHeader('Location', '/accounts');
    }
  }

}

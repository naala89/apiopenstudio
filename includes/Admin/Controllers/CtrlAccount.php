<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Gaterdata\Core\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

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

    // Filter params.
    $allParams = $request->getParams();
    $params = [];
    if (!empty($allParams['keyword'])) {
      $params['keyword'] = $allParams['keyword'];
    }
    $params['order_by'] = !empty($allParams['order_by']) ? $allParams['order_by'] : 'name';
    $params['dir'] = isset($allParams['dir']) ? $allParams['dir'] : 'ASC';
    $page = isset($allParams['page']) ? $allParams['page'] : 1;

    try {
      $domain = $this->settings['api']['url'];
      $account = $this->settings['api']['core_account'];
      $application = $this->settings['api']['core_application'];
      $token = $_SESSION['token'];
      $client = new Client(['base_uri' => "$domain/$account/$application/"]);
      $result = $client->request('GET', 'account', [
        'headers' => [
          'Authorization' => "Bearer $token",
        ],
        'query' => [
          'accountName' => 'all',
        ],
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
      // @TODO: This may not be the best way to trap unauthorized or timed out token.
      return $response->withStatus(302)->withHeader('Location', '/login');
    } catch (RequestException $e) {
      // @TODO: This may not be the best way to trap unauthorized or timed out token.
      return $response->withStatus(302)->withHeader('Location', '/login');
    }

    // Get total number of pages and current page's accounts to display.
    $pages = ceil(count($accounts) / $this->paginationStep);
    $accounts = array_slice($accounts, ($page - 1) * $this->paginationStep, $this->paginationStep, TRUE);

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
      $this->flash->addMessage('error', 'View accounts: access denied');
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
    $this->permittedRoles = ['Administrator'];
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'Edit account: access denied');
      return $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['edit-acc-name']) || empty($accid = $allPostVars['edit-acc-id'])) {
      $this->flash->addMessage('error', 'Cannot edit account, no name or ID defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      $accountHlp = new Account($this->dbSettings);
      $accountHlp->findByAccountId($accid);
      $accountHlp->updateName($name);
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      return $response->withRedirect('/accounts');
    }

    $this->flash->addMessage('info', 'Account updated');
    return $response->withRedirect('/accounts');
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
    $this->permittedRoles = ['Administrator'];
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'Delete account: access denied');
      return $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['delete-acc-name']) || empty($accid = $allPostVars['delete-acc-id'])) {
      $this->flash->addMessage('error', 'Cannot delete account, no name or ID defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      $applicationUserRoleHlp = new ApplicationUserRole($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
      $accountHlp = new Account($this->dbSettings);
      $managerHlp = new Manager($this->dbSettings);

      // Find all applications for the account.
      $applications = $applicationHlp->findByAccid($accid);
      foreach ($applications as $application) {
        // Find all application user roles for each application.
        $applicationUserRoles = $applicationUserRoleHlp->findByAppid( $application['appid']);
        foreach ($applicationUserRoles as $applicationUserRole) {
          // Delete each application user role.
          $applicationUserRoleHlp->findByAurid($applicationUserRole['aurid']);
          $applicationUserRoleHlp->delete();
        }
        // Delete each application
        $applicationHlp->findByApplicationId($application['appid']);
        $applicationHlp->delete();
      }

      // Delete all managers for the account.
      $managers = $managerHlp->findByAccountId($accid);
      foreach ($managers as $manager) {
        $managerHlp->findByManagerId($manager['mid']);
        $managerHlp->delete();
      }

      // Delete the account.
      $accountHlp->findByAccountId($accid);
      $accountHlp->delete();
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      return $response->withRedirect('/accounts');
    }

    $this->flash->addMessage('info', 'Account deleted');
    return $response->withRedirect('/accounts');
  }

}

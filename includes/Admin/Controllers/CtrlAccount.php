<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Application;
use Datagator\Admin\Manager;
use Datagator\Admin\ApplicationUserRole;
use Datagator\Core\ApiException;
use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\Account;

/**
 * Class CtrlAccount.
 *
 * @package Datagator\Admin\Controllers
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
    $this->permittedRoles = ['Administrator', 'Manager'];
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'View accounts: access denied');
      return $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    // Filter params.
    $allParams = $request->getParams();
    $params = [];
    if (!empty($allParams['search'])) {
      $params['keyword'] = $allParams['keyword'];
    }
    $params['order_by'] = !empty($allParams['order_by']) ? $allParams['order_by'] : 'name';
    $params['dir'] = isset($allParams['dir']) ? $allParams['dir'] : 'ASC';
    $page = isset($allParams['page']) ? $allParams['page'] : 1;

    try {
      $accountHlp = new Account($this->dbSettings);
      if (in_array('Administrator', $roles)) {
        $accounts = $accountHlp->findAll($params);
      } else {
        $managerHlp = new Manager($this->dbSettings);
        $managers = $managerHlp->findByUserId($uid);
        $accounts = [];
        foreach ($managers as $manager) {
          $accounts[] = $accountHlp->findByAccountId($manager['accid'], $params);
        }
      }
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      $accounts = [];
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
    $this->permittedRoles = ['Administrator'];
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'Create account: access denied');
      return $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['create-acc-name'])) {
      $this->flash->addMessage('error', 'Cannot create account, no name defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      $accountHlp = new Account($this->dbSettings);
      $account = $accountHlp->findByName($name);
      if (!$account) {
        if (empty($name = $allPostVars['create-acc-name'])) {
          $this->flash->addMessage('error', 'Something went wrong while creating your account. Please check the logs.');
          return $response->withRedirect('/accounts');
        }
      }
      if (!empty($account['accid'])) {
        $this->flash->addMessage('error', 'An account with this name already exists.');
        return $response->withRedirect('/accounts');
      }
      if (!$accountHlp->create($name)) {
        $this->flash->addMessage('error', 'Something went wrong while creating your account. Please check the logs.');
        return $response->withRedirect('/accounts');
      } else {
        $this->flash->addMessage('info', 'Application created');
        return $response->withRedirect('/accounts');
      }
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      return $response->withRedirect('/accounts');
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

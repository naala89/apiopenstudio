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

  protected $permittedRoles = ['Administrator'];

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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      return $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    try {
      $accountHlp = new Account($this->dbSettings);
      if (in_array('Administrator', $roles)) {
        $accounts = $accountHlp->findAll();
      } else {
        $managerHlp = new Manager($this->dbSettings);
        $managers = $managerHlp->findByUserId($uid);
        $accounts = [];
        foreach ($managers as $manager) {
          $accounts[] = $accountHlp->findByAccountId($manager['accid']);
        }
      }
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      $accounts = [];
    }

    return $this->view->render($response, 'accounts.twig', [
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      return $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['edit-acc-name']) || empty($accid = $allPostVars['edit-acc-id'])) {
      $this->flash->addMessage('error', 'Cannot delete account, no name or ID defined.');
      return $response->withRedirect('/accounts');
    }

    try {
      $applicationUserRoleHlp = new ApplicationUserRole($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
      $accountHlp = new Account($this->dbSettings);
      $managerHlp = new Manager($this->dbSettings);
      $managers = $managerHlp->findByAccountId($accid);

      $applications = $applicationHlp->findByAccid($accid);
      foreach ($applications as $application) {
        $applicationUserRoles = $applicationUserRoleHlp->findByAppid( $application['appid']);
        foreach ($applicationUserRoles as $applicationUserRole) {
          $applicationUserRoleHlp->findByAurid($applicationUserRole['aurid']);
          $applicationUserRoleHlp->delete();
        }
      }

      foreach ($managers as $manager) {
        $managerHlp->findByManagerId($manager['mid']);
        $managerHlp->delete();
      }
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

<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Account;
use Datagator\Admin\ApplicationUserRole;
use Datagator\Admin\Manager;
use Datagator\Core\ApiException;
use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\Application;

/**
 * Class CtrlApplication.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlApplication extends CtrlBase {

  protected $permittedRoles = ['Administrator', 'Manager'];

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
    $this->permittedRoles = ['Administrator', 'Manager'];
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'View Applications: access denied');
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    // Filter params.
    $allParams = $request->getParams();
    $params = [];
    if (!empty($allParams['account'])) {
      $params['account'] = $allParams['account'];
    }
    if (!empty($allParams['keyword'])) {
      $params['keyword'] = $allParams['keyword'];
    }
    $params['order_by'] = !empty($allParams['order_by']) ? $allParams['order_by'] : 'accid';
    $params['dir'] = isset($allParams['dir']) ? $allParams['dir'] : 'ASC';
    $page = isset($allParams['page']) ? $allParams['page'] : 1;

    try {
      $accountHlp = new Account($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
      $applicationUserRoleHlp = new ApplicationUserRole($this->dbSettings);

      // Find all accounts for the user.
      if (in_array('Administrator', $roles)) {
        $accounts = $accountHlp->findAll();
        $allAccounts = [];
        foreach ($accounts as $account) {
          $allAccounts[$account['accid']] = $account;
        }
      } else {
        $allAccounts = [];
        if (in_array('Manager', $roles)) {
          $managerHlp = new Manager($this->dbSettings);
          $managers = $managerHlp->findByUserId($uid);
          foreach ($managers as $manager) {
            $allAccounts[$manager['accid']] = $accountHlp->findByAccountId($manager['accid']);
          }
        }
        $allAccounts = array_merge($allAccounts, $applicationUserRoleHlp->findAccountsByUid($uid));
      }

      // Filter the viewed accounts.
      if (!isset($params['account'])) {
        $accounts = $allAccounts;
      } elseif (isset($allAccounts[$params['account']])) {
        $accounts = [
          $params['account'] => $allAccounts[$params['account']]
        ];
      } else {
        $accounts = [];
      }

      // Find all applications for each account.
      $accids = array_keys($accounts);
      $applications = $applicationHlp->findByAccidMult($accids, $params);
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      $accounts = [];
      $applications = [];
    }

    // Get total number of pages and current page's applications to display.
    $pages = ceil(count($applications) / $this->paginationStep);
    $applications = array_slice($applications, ($page - 1) * $this->paginationStep, $this->paginationStep, TRUE);
    echo "<pre>";var_dump($applications);
    echo "<pre>";var_dump($accounts);

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'params' => $params,
      'page' => $page,
      'pages' => $pages,
      'allAccounts' => $allAccounts,
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'View Applications: access denied.');
      $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($appName = $allPostVars['create-app-name']) || empty($accid = $allPostVars['create-app-accid'])) {
      $this->flash->addMessage('error', 'Cannot create application, no name or account ID defined.');
    } else {
      try {
        $applicationHlp = new Application($this->dbSettings);
        $applicationHlp->create($accid, $appName);
        $this->flash->addMessage('info', 'Application created.');
      } catch (ApiException $e) {
        $this->flash->addMessage('error', $e->getMessage());
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'Edit Applications: access denied.');
      $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($accid = $allPostVars['edit-app-accid']) || empty($appid = $allPostVars['edit-app-appid']) || empty($appName = $allPostVars['edit-app-name'])) {
      $this->flash->addMessage('error', 'Cannot create application, no application ID, name or account ID defined.');
    } else {
      try {
        $applicationHlp = new Application($this->dbSettings);
        $application = $applicationHlp->findByApplicationId($appid);
        $application['accid'] = $accid;
        $application['name'] = $appName;
        $applicationHlp->update($application);
        $this->flash->addMessage('info', 'Application updated.');
      } catch (ApiException $e) {
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
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   *
   * TODO: Delete all associated resources and remove user roles.
   */
  public function delete(Request $request, Response $response, array $args) {
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      $this->flash->addMessage('error', 'Edit Applications: access denied.');
      $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($appid = $allPostVars['delete-app-appid'])) {
      $this->flash->addMessage('error', 'Cannot delete application, no application ID defined.');
    } else {
      try {
        $applicationHlp = new Application($this->dbSettings);
        $applicationUserRoleHlp = new ApplicationUserRole($this->dbSettings);
        // Delete the user roles for this application.
        $applicationUserRoles = $applicationUserRoleHlp->findByAppid($appid);
        foreach ($applicationUserRoles as $applicationUserRole) {
          $applicationUserRoleHlp->delete($applicationUserRole);
        }
        // Delete the application.
        $applicationHlp->findByApplicationId($appid);
        $applicationHlp->delete();
        $this->flash->addMessage('info', 'Application deleted.');
      } catch (ApiException $e) {
        $this->flash->addMessage('error', $e->getMessage());
      }
    }

    return $response->withRedirect('/applications');
  }

}

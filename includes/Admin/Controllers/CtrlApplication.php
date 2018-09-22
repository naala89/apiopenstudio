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

    try {
      $accountHlp = new Account($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
      // Find all accounts for the user.
      if (in_array('Administrator', $roles)) {
        $allAccounts = $accountHlp->findAll();
      } else {
        $allAccounts = [];
        $managerHlp = new Manager($this->dbSettings);
        $managers = $managerHlp->findByUserId($uid);
        foreach ($managers as $manager) {
          $allAccounts[$manager['accid']] = $accountHlp->findByAccountId($manager['accid']);
        }
      }
      // Filter the viewed applications by account.
      $allGetVars = $request->getQueryParams();
      $filter = isset($allGetVars['filter']) ? $allGetVars['filter'] : '';
      if ($filter == '') {
        $accounts = $allAccounts;
      } elseif (isset($allAccounts[$filter])) {
        $accounts = [
          $filter => $allAccounts[$filter]
        ];
      } else {
        $accounts = [];
      }
      echo "<pre>";
      var_dump($filter);
      var_dump($accounts);
      // Find all applications for each account.
      $applications = [];
      $accids = array_keys($accounts);
      foreach ($accids as $accid) {
        $applications[$accid] = $applicationHlp->findByAccid($accid);
      }
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      $accounts = [];
      $applications = [];
    }

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'filter' => $filter,
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

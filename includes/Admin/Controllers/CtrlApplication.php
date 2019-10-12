<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
// use Gaterdata\Admin\Account;
// use Gaterdata\Admin\ApplicationUserRole;
// use Gaterdata\Admin\Manager;
use Gaterdata\Core\ApiException;
// use Gaterdata\Admin\Application;

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
      $this->flash->addMessage('error', 'View accounts: access denied');
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
    // echo "<pre>";var_dump($applications);die();

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

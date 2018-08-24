<?php

namespace Datagator\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\User;
use Datagator\Admin\UserRole;
use Datagator\Admin\Role;
use Datagator\Admin\Application;

/**
 * Class User.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlUser extends CtrlBase {
  protected $permittedRoles = ['Owner'];

  /**
   * Display the users page.
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
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);
    $title = 'Users';
    $accId = $_SESSION['accountId'];

    // Fetch all applications for the account.
    $applicationHlp = new Application($this->dbSettings);
    $applications = $applicationHlp->getByAccount($accId);

    // fetch all roles.
    $roleHlp = new Role($this->dbSettings);
    $roles = $roleHlp->findAll();

    // Fetch all user roles for the account
    $userRoleHlp = new UserRole($this->dbSettings);
    $userRoles = $userRoleHlp->findByAccId($accId);

    // Fetch all user roles for each application.
    foreach ($applications as $appId => $application) {
      $results = $userRoleHlp->findByAppId($appId);
      foreach ($results as $result) {
        $userRoles[] = $result;
      }
    }

    // Fetch distinct users for each user role.
    $userHlp = new User($this->dbSettings);
    $users = [];
    foreach ($userRoles as $userRole) {
      $uid = $userRole['uid'];
      if (!isset($user[$uid])) {
        $users[$uid] = $userHlp->findByUid($uid);
      }
    }

    // Add applications => roles to users array.
    foreach ($users as $uid => $user) {
      $user['applications'] = [];
      // Find all user roles for this user.
      echo "<pre>";
      var_dump($userRoles);
      foreach ($userRoles as $userRole) {
        if ($userRole['uid'] == $uid) {
          // Add application if not exists.
          $application = $applications[$userRole['appId']];
          $appId = $application['appId'];
          if (!isset($user['applications'][$appId])) {
            $user['applications'][$appId] = $application;
          }
          // Add role.
          $roleId = $userRole['rid'];
          $user['applications'][$appId]['roles'][$roleId] = $roles[$roleId];
        }
      }
    }

    return $this->view->render($response, 'users.twig', [
      'menu' => $menu,
      'title' => $title,
      'applications' => $applications,
      'users' => $users,
    ]);
  }

}

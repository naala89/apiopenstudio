<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlHome.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlHome extends CtrlBase {

  /**
   * Roles allowed to visit the page.
   * 
   * @var array
   */
  const PERMITTED_ROLES = [
    'Administrator',
    'Account manager',
    'Application manager',
    'Developer'
  ];

  /**
   * Home page.
   *
   * @param \Slim\Http\Request $request
   *   Request object.
   * @param \Slim\Http\Response $response
   *   Response object.
   * @param array $args
   *   Request args.
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function index(Request $request, Response $response, array $args) {
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
    $this->getAccessRights($response, $username);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access admin: access denied');
      return $response->withStatus(302)->withHeader('Location', '/logout');
    }
    $roles = $this->getRoles();
    $menu = $this->getMenus();
    $accounts = $this->getAccounts();
    $applications = $this->getApplications();

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'roles' => $roles,
      'flash' => $this->flash,
    ]);
  }

}

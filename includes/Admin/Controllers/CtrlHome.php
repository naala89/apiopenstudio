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
    if (!$this->getAccessRights($username)) {
      return $response->withStatus(302)->withHeader('Location', '/login');
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

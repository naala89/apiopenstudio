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
    $this->getRoles($username);
    $menu = $this->getMenus($this->userRoles);
    $accounts = $this->getAccounts($this->userRoles);
    $applications = $this->getApplications($this->userRoles);

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'flash' => $this->flash,
    ]);
  }

}

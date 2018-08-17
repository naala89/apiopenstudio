<?php

namespace Datagator\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Login.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlLogin extends CtrlBase {

  /**
   * Login page controller.
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
  public function login(Request $request, Response $response, array $args) {
    $title = 'Login';
    $menu = $this->getMenus([]);
    return $this->view->render($response, 'login.twig', ['menu' => $menu, 'title' => $title]);
  }

  /**
   * Logout page controller.
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
  public function logout(Request $request, Response $response, array $args) {
    $title = 'Login';
    $menu = $this->getMenus([]);
    unset($_SESSION['token']);
    unset($_SESSION['accountName']);
    unset($_SESSION['accountId']);
    return $this->view->render($response, 'login.twig', ['menu' => $menu, 'title' => $title]);
  }

}

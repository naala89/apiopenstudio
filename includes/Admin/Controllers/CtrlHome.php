<?php

namespace Datagator\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Home.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlHome extends CtrlBase {

  /**
   * Display the home page.
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
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : '';
    $accountId = isset($_SESSION['accountId']) ? $_SESSION['accountId'] : '';
    $roles = $this->getRoles($token, $accountId);
    $menu = $this->getMenus($roles);
    $title = 'Home';
    return $this->view->render($response, 'home.twig', ['menu' => $menu, 'title' => $title]);
  }

}

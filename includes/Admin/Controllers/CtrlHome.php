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
    $uaid = isset($_SESSION['userAccountId']) ? $_SESSION['userAccountId'] : '';
    $roles = $this->getRoles($uaid);
    $menu = $this->getMenus($roles);
    return $this->view->render($response, 'home.twig', ['menu' => $menu]);
  }

}

<?php

namespace Datagator\Admin\Controllers;

/**
 * Class Home
 * @package Datagator\Admin\Controllers
 */
class Home extends Base
{
  /**
   * Display the home page.
   *
   * @param $request
   *   Request object.
   * @param $response
   *   Response object.
   * @param $args
   *   Request args,
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function index($request, $response, $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['account']);
    $menu = $this->getMenus($roles);
    $title = 'Home';
    return $this->view->render($response, 'home.twig', ['menu' => $menu, 'title' => $title]);
  }

}
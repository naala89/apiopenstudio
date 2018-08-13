<?php

namespace Datagator\Admin\Controllers;

class Home extends Base
{
  public function index($request, $response, $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['account']);
    $menu = $this->getMenus($roles);
    $title = 'Home';
    return $this->view->render($response, 'home.twig', ['menu' => $menu, 'title' => $title]);
  }
}
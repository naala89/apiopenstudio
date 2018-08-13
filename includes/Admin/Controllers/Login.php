<?php

namespace Datagator\Admin\Controllers;

class Login extends Base
{
  public function login($request, $response, $args) {
    $title = 'Login';
    $menu = ['Login' => '/login'];
    return $this->view->render($response, 'home.twig', ['menu' => $menu, 'title' => $title]);
  }

  public function logout($request, $response, $args) {
    $title = 'Logout';
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['account']);
    $menu = $this->getMenus($roles);
    return $this->view->render($response, 'home.twig', ['menu' => $menu, 'title' => $title]);
  }
}
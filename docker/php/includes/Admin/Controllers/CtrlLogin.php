<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Class CtrlLogin.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlLogin extends CtrlBase {

  /**
   * CtrlLogin constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param \Slim\Views\Twig $view
   *   View container.
   * @param \Slim\Flash\Messages $flash
   *   Flash messages container.
   */
  public function __construct(array $dbSettings, Twig $view, Messages $flash) {
    parent::__construct($dbSettings, 0, $view, $flash);
  }

  /**
   * Login page.
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
    $menu = $this->getMenus([]);
    return $this->view->render($response, 'login.twig', [
      'menu' => $menu,
      'messages' => [$this->flash->getMessages()],
    ]);
  }

  /**
   * Logout page.
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
    $menu = $this->getMenus([]);
    unset($_SESSION['token']);
    unset($_SESSION['uid']);
    return $this->view->render($response, 'login.twig', [
      'menu' => $menu,
      'messages' => [$this->flash->getMessages()],
    ]);
  }

}

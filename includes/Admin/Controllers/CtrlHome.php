<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Slim\Collection;
use Gaterdata\Admin\Account;
use Gaterdata\Admin\Application;
use Gaterdata\Admin\UserAccount;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Debug;

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
    $roles = $this->getRoles($username);
    $menu = $this->getMenus($roles);

    // try {

      // $accountHlp = new Account($this->settings);
      // $applicationHlp = new Application($this->settings);
      // $accounts = $accountHlp->findAll();

      // $applications = $applicationHlp->findAll();
    // } catch(ApiException $e) {
    //   $this->flash->addMessage('error', $e->getMessage());
    //   $applications = [];
    //   $accounts = [];
    // }

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => [],
      'applications' => [],
      'flash' => $this->flash,
    ]);
  }

}

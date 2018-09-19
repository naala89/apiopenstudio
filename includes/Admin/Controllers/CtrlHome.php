<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Account;
use Datagator\Admin\Application;
use Datagator\Admin\UserAccount;
use Datagator\Core\ApiException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlHome.
 *
 * @package Datagator\Admin\Controllers
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (empty($roles)) {
      unset ($_SESSION['uid']);
      unset ($_SESSION['token']);
      return $response->withRedirect('/login');
    }
    $menu = $this->getMenus($roles);

    try {
      $accountHlp = new Account($this->dbSettings);
      $accounts = $accountHlp->findAll();
      $applicationHlp = new Application($this->dbSettings);
      $applications = $applicationHlp->findAll();
    } catch(ApiException $e) {
      $applications = [];
      $accounts = [];
    }
    $this->flash->addMessage('error', 'sdfuygdsrkjhg');

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'flash' => $this->flash,
    ]);
  }

}

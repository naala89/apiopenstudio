<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Gaterdata\Admin\Account;
use Gaterdata\Admin\Application;
use Gaterdata\Admin\UserAccount;
use Gaterdata\Core\ApiException;

/**
 * Class CtrlHome.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlHome extends CtrlBase {

  /**
   * CtrlHome constructor.
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
    $menu = $this->getMenus($roles);

    try {
      $accountHlp = new Account($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
      $accounts = $accountHlp->findAll();
      $applications = $applicationHlp->findAll();
    } catch(ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      $applications = [];
      $accounts = [];
    }

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'flash' => $this->flash,
    ]);
  }

}

<?php

namespace Datagator\Admin\Controllers;

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
    $uaid = isset($_SESSION['uaid']) ? $_SESSION['uaid'] : '';
    $roles = $this->getRoles($uaid);
    $menu = $this->getMenus($roles);

    try {
      $userAccountHlp = new UserAccount($this->dbSettings);
      $userAccount = $userAccountHlp->findByUaid($uaid);
      if (!$userAccount) {
        $applications = [];
      } else {
        $applicationHlp = new Application($this->dbSettings);
        $applications = $applicationHlp->findByAccountId($userAccount['accid']);
      }
    } catch(ApiException $e) {
      // This will trap any exceptions while instantiating the helper classes, which may fail on DB connection.
      $applications = [];
    }

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'applications' => $applications,
    ]);
  }

}

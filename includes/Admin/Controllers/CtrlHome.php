<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Application;
use Datagator\Admin\User;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Home.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlHome extends CtrlBase {

  /**
   * @param Request $request
   * @param Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Datagator\Core\ApiException
   */
  public function index(Request $request, Response $response, array $args) {
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $accid = isset($_SESSION['accid']) ? $_SESSION['accid'] : '';
    $roles = $this->getRoles($uid, $accid);
    $menu = $this->getMenus($roles);

    $applicationHlp = new Application($this->dbSettings);
    $applications = $applicationHlp->findByAccountId($accid);

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'applications' => $applications,
    ]);
  }

}

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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $accid = isset($_SESSION['accid']) ? $_SESSION['accid'] : '';
    $roles = $this->getRolesByAccid($accid, $uid);
    var_dump($roles);exit;
    $menu = $this->getMenus($roles);

    try {
      $applicationHlp = new Application($this->dbSettings);
      $applications = $applicationHlp->findByAccid($accid);
    } catch(ApiException $e) {
      $applications = [];
    }

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'applications' => $applications,
    ]);
  }

}

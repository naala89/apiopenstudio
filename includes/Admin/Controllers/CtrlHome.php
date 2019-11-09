<?php

namespace Gaterdata\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CtrlHome.
 *
 * @package Gaterdata\Admin\Controllers
 */
class CtrlHome extends CtrlBase {

  /**
   * Roles allowed to visit the page.
   * 
   * @var array
   */
  const PERMITTED_ROLES = [
    'Administrator',
    'Account manager',
    'Application manager',
    'Developer'
  ];

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
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function index(Request $request, Response $response, array $args) {
    // Validate access.
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $this->getAccessRights($response, $uid);
    if (!$this->checkAccess()) {
      $this->flash->addMessage('error', 'Access denied');
      return $response->withStatus(302)->withHeader('Location', '/logout');
    }

    $menu = $this->getMenus();
    $accounts = $this->getAccounts($response);
    $applications = $this->getApplications($response);

    return $this->view->render($response, 'home.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'applications' => $applications,
      'roles' => $roles,
      'flash' => $this->flash,
    ]);
  }

}

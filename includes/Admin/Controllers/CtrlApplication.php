<?php

namespace Datagator\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin;

/**
 * Class Application
 * @package Datagator\Admin\Controllers
 */
class CtrlApplication extends CtrlBase
{
  /**
   * Display the applications page.
   *
   * @param $request
   *   Request object.
   * @param $response
   *   Response object.
   * @param $args
   *   Request args,
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function index(Request $request, Response $response, $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['account']);
    if (!in_array('Owner', $roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $application = new Admin\Application($this->dbSettings);
    $applications = $application->getByAccount($_SESSION['accountId']);

    return $this->view->render($response, 'applications.twig', ['menu' => $menu, 'title' => $title, 'applications' => $applications]);
  }

  /**
   * Create an application
   *
   * @param $request
   *   Request object.
   * @param $response
   *   Response object.
   * @param $args
   *   Request args,
   *
   * @return \Psr\Http\Message\ResponseInterface
   *   Response.
   */
  public function create(Request $request, Response $response, $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['account']);
    if (!in_array('Owner', $roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $allPostVars = $request->getParsedBody();
    $application = new Admin\Application($this->dbSettings);

    $message = [
      'type' => 'info',
      'text' => 'Application created'
    ];
    if (isset($allPostVars['name'])) {
      $result = $application->create($allPostVars['name'], $_SESSION['accountId']);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to create application'
        ];
      }
    } else {
      $message = [
        'type' => 'error',
        'text' => 'Could not create application - no name received'
      ];
    }

    $applications = $application->getByAccount($_SESSION['accountId']);
    return $this->view->render($response, 'applications.twig', ['menu' => $menu, 'title' => $title, 'applications' => $applications, 'message' => $message]);
  }

}
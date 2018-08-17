<?php

namespace Datagator\Admin\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\Application;

/**
 * Class Application.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlApplication extends CtrlBase {

  protected $permittedRoles = ['Owner'];

  /**
   * Display the applications page.
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
  public function index(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $application = new Application($this->dbSettings);
    $applications = $application->getByAccount($_SESSION['accountId']);

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'title' => $title,
      'applications' => $applications,
    ]);
  }

  /**
   * Create an application.
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
  public function create(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $allPostVars = $request->getParsedBody();
    $application = new Application($this->dbSettings);

    $message = [
      'type' => 'info',
      'text' => 'Application created',
    ];
    if (!empty($allPostVars['create-app-name'])) {
      $result = $application->create($_SESSION['accountId'], $allPostVars['create-app-name']);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to create application',
        ];
      }
    }
    else {
      $message = [
        'type' => 'error',
        'text' => 'Could not create application - no name received',
      ];
    }

    $applications = $application->getByAccount($_SESSION['accountId']);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'title' => $title,
      'applications' => $applications,
      'message' => $message,
    ]);
  }

  /**
   * Edit an application.
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
  public function edit(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $allPostVars = $request->getParsedBody();
    $application = new Application($this->dbSettings);

    $message = [
      'type' => 'info',
      'text' => 'Application edited',
    ];
    if (!empty($allPostVars['edit-app-id']) && !empty($allPostVars['edit-app-name'])) {
      $result = $application->update($allPostVars['edit-app-id'], $allPostVars['edit-app-name']);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to edit application',
        ];
      }
    }
    else {
      $message = [
        'type' => 'error',
        'text' => 'Could not edit application - no name or ID received',
      ];
    }

    $applications = $application->getByAccount($_SESSION['accountId']);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'title' => $title,
      'applications' => $applications,
      'message' => $message,
    ]);
  }

  /**
   * Delete an application.
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
  public function delete(Request $request, Response $response, array $args) {
    $roles = $this->getRoles($_SESSION['token'], $_SESSION['accountId']);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }

    $menu = $this->getMenus($roles);
    $title = 'Applications';
    $allPostVars = $request->getParsedBody();
    $application = new Application($this->dbSettings);

    $message = [
      'type' => 'info',
      'text' => 'Application deleted',
    ];
    if (!empty($allPostVars['delete-app-id'])) {
      $result = $application->delete($allPostVars['delete-app-id']);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to edit application',
        ];
      }
    }
    else {
      $message = [
        'type' => 'error',
        'text' => 'Could not delete application - no ID received',
      ];
    }

    $applications = $application->getByAccount($_SESSION['accountId']);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'title' => $title,
      'applications' => $applications,
      'message' => $message,
    ]);
  }

}

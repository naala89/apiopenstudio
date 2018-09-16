<?php

namespace Datagator\Admin\Controllers;

use Datagator\Core\ApiException;
use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\Application;

/**
 * Class CtrlApplication.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlApplication extends CtrlBase {

  protected $permittedRoles = ['Owner'];

  /**
   * Applications page.
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
    $uaid = isset($_SESSION['uaid']) ? $_SESSION['uaid'] : '';
    $roles = $this->getRoles($uaid);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    try {
      $applicationHlp = new Application($this->dbSettings);
      $applications = $applicationHlp->findByUserAccountId($uaid);
    } catch (ApiException $e) {
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => [],
        'message' => [
          'type' => 'error',
          'text' => $e->getMessage(),
        ],
      ]);
    }

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
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
    $uaid = isset($_SESSION['uaid']) ? $_SESSION['uaid'] : '';
    $roles = $this->getRoles($uaid);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    $allPostVars = $request->getParsedBody();
    if (empty($appName = $allPostVars['create-app-name'])) {
      $message = [
        'type' => 'error',
        'text' => 'Cannot create application, no name defined.',
      ];
    } else {
      try {
        $applicationHlp = new Application($this->dbSettings);
        $applicationHlp->createByUserAccIdName($uaid, $appName);
        $applications = $applicationHlp->findByUserAccountId($uaid);
        $message = [
          'type' => 'info',
          'text' => 'Application created',
        ];
      } catch (ApiException $e) {
        $applications = [];
        $message = [
          'type' => 'error',
          'text' => $e->getMessage(),
        ];
      }
    }

    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
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
    $uaid = isset($_SESSION['uaid']) ? $_SESSION['uaid'] : '';
    $roles = $this->getRoles($uaid);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    try {
      $applicationHlp = new Application($this->dbSettings);
    } catch (ApiException $e) {
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => [],
        'message' => [
          'type' => 'error',
          'text' => $e->getMessage(),
        ],
      ]);
    }

    $allPostVars = $request->getParsedBody();
    if (empty($appName = $allPostVars['edit-app-name']) || empty($appId = $allPostVars['edit-app-id'])) {
      $applications = $applicationHlp->findByUserAccountId($uaid);
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => $applications,
        'message' => [
          'type' => 'error',
          'text' => 'Cannot edit application, no name or ID defined.',
        ],
      ]);
    } else {
      $applicationHlp->findByApplicationId($appId);
      $applicationHlp->update($appName);
      $applications = $applicationHlp->findByUserAccountId($uaid);
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => $applications,
        'message' => [
          'type' => 'info',
          'text' => 'Application name updated.',
        ],
      ]);
    }
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
   *
   * TODO: Delete all associated resources and remove user roles.
   */
  public function delete(Request $request, Response $response, array $args) {
    $uaid = isset($_SESSION['uaid']) ? $_SESSION['uaid'] : '';
    $roles = $this->getRoles($uaid);
    if (!$this->checkAccess($roles)) {
      $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    try {
      $applicationHlp = new Application($this->dbSettings);
    } catch (ApiException $e) {
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => [],
        'message' => [
          'type' => 'error',
          'text' => $e->getMessage(),
        ],
      ]);
    }

    $allPostVars = $request->getParsedBody();
    if (empty($appId = $allPostVars['delete-app-id'])) {
      $applications = $applicationHlp->findByUserAccountId($uaid);
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => $applications,
        'message' => [
          'type' => 'error',
          'text' => 'Cannot delete application, no application ID defined.',
        ],
      ]);
    } else {
      $applicationHlp->findByApplicationId($appId);
      $applicationHlp->delete();
    }

    $applications = $applicationHlp->findByUserAccountId($uaid);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'applications' => $applications,
      'message' => [
        'type' => 'info',
        'text' => 'Application deleted.',
      ],
    ]);
  }

}

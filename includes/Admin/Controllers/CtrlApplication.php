<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\UserAccount;
use Datagator\Core\ApiException;
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
      $message = [
        'type' => 'error',
        'text' => $e->getMessage(),
      ];
      return $this->view->render($response, 'applications.twig', [
        'menu' => $menu,
        'applications' => [],
        'message' => $message,
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
    $applicationHlp = new Application($this->dbSettings);
    if (empty($appName = $allPostVars['create-app-name'])) {
      $message = [
        'type' => 'error',
        'text' => 'Cannot create application, no name defined.',
      ];
    } else {
      try {
        $applicationHlp->createByUserAccIdName($uaid, $appName);
        $message = [
          'type' => 'info',
          'text' => 'Application created',
        ];
      } catch (ApiException $e) {
        $message = [
          'type' => 'error',
          'text' => $e->getMessage(),
        ];
      }
    }

    $applications = $applicationHlp->findByUserAccountId($uaid);
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

    $allPostVars = $request->getParsedBody();
    $applicationHlp = new Application($this->dbSettings);
    if (empty($appName = $allPostVars['edit-app-name']) || empty($appId = $allPostVars['edit-app-id'])) {
      $message = [
        'type' => 'error',
        'text' => 'Cannot edit application, no name or ID defined.',
      ];
    } else {
      try {
        $message = [
          'type' => 'info',
          'text' => 'Application created',
        ];
      } catch (ApiException $e) {
        $message = [
          'type' => 'error',
          'text' => $e->getMessage(),
        ];
      }
    }

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

    $applications = $application->findByAccount($userAccount['accId']);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
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
    $allPostVars = $request->getParsedBody();
    $application = new Application($this->dbSettings);

    $userAccountHlp = new UserAccount($this->dbSettings);
    $userAccount = $userAccountHlp->findByUserAccountId($uaid);
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

    $applications = $application->findByAccount($userAccount['accId']);
    return $this->view->render($response, 'applications.twig', [
      'menu' => $menu,
      'applications' => $applications,
      'message' => $message,
    ]);
  }

}

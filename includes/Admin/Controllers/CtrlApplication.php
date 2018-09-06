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
      $userAccountHlp = new UserAccount($this->dbSettings);
      $userAccount = $userAccountHlp->findByUaid($uaid);
      $applicationHlp = new Application($this->dbSettings);
      $applications = $applicationHlp->findByAccountId($userAccount['accid']);
    } catch (ApiException $e) {
      // This will trap any exceptions while instantiating the helper classes, which may fail on DB connection.
      $applications = [];
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

    try {
      $userAccountHlp = new UserAccount($this->dbSettings);
      $applicationHlp = new Application($this->dbSettings);
    } catch (ApiException $e) {
      if (empty($allPostVars['create-app-name'])) {
        $message = [
          'type' => 'error',
          'text' => 'There was an error at the DB layer, please check the logs.',
        ];
    }

    $allPostVars = $request->getParsedBody();
    if (empty($allPostVars['create-app-name'])) {
      $message = [
        'type' => 'error',
        'text' => 'Could not create application - no name received',
      ];
    } else {
      try {
        $userAccount = $userAccountHlp->findByUaid($uaid);
        $result = $applicationHlp->create($userAccount['accid'], $allPostVars['create-app-name']);
        if ($result == FALSE) {
          $message = [
            'type' => 'error',
            'text' => 'An error occurred creating the application',
          ];
        } else {
          $message = [
            'type' => 'info',
            'text' => 'Application created',
          ];
        }
      } catch (ApiException $e) {
        $message = [
          'type' => 'error',
          'text' => 'An error occurred creating the application',
        ];
      }
    }

    $applications = $applicationHlp->findByAccountId($userAccount['accid']);
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
    if (empty($allPostVars['edit-app-name'])) {
      $message = [
        'type' => 'error',
        'text' => 'Could not edit application - no name received',
      ];
    } else {
      try {
        $userAccountHlp = new UserAccount($this->dbSettings);
        $userAccount = $userAccountHlp->findByUaid($uaid);
        $applicationHlp = new Application($this->dbSettings);
        $application = $applicationHlp->findByAccIdAppName($userAccount['uaid'], $allPostVars['edit-app-name']);

      } catch (ApiException $e) {
        $message = [
          'type' => 'error',
          'text' => 'An error occurred creating the application',
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

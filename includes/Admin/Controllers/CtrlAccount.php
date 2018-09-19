<?php

namespace Datagator\Admin\Controllers;

use Datagator\Core\ApiException;
use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\Account;

/**
 * Class CtrlAccount.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlAccount extends CtrlBase {

  protected $permittedRoles = ['Administrator'];

  /**
   * Accounts page.
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      return $response->withRedirect('/');
    }
    $menu = $this->getMenus($roles);

    try {
      $accountHlp = new Account($this->dbSettings);
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
    }

//    var_dump($this->flash->getMessages());exit;
    $accounts = $accountHlp->findAll();
    return $this->view->render($response, 'accounts.twig', [
      'menu' => $menu,
      'accounts' => $accounts,
      'messages' => $this->flash->getMessages(),
    ]);
  }

  /**
   * Create an account.
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
    $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : '';
    $roles = $this->getRoles($uid);
    if (!$this->checkAccess($roles)) {
      return $response->withRedirect('/');
    }

    $allPostVars = $request->getParsedBody();
    if (empty($name = $allPostVars['create-acc-name'])) {
      $this->flash->addMessage('error', 'Cannot create account, no name defined.');
      return $response->withRedirect('/accounts');
    }
    try {
      $accountHlp = new Account($this->dbSettings);
      $account = $accountHlp->findByName($name);
      if (!$account) {
        if (empty($name = $allPostVars['create-acc-name'])) {
          $this->flash->addMessage('error', 'Something went wrong while creating your account. Please check the logs.');
          return $response->withRedirect('/accounts');
        }
      }
      if (!empty($account['accid'])) {
        $this->flash->addMessage('error', 'An account with this name already exists.');
        return $response->withRedirect('/accounts');
      }
      if (!$accountHlp->create($name)) {
        $this->flash->addMessage('error', 'Something went wrong while creating your account. Please check the logs.');
        return $response->withRedirect('/accounts');
      } else {
        $this->flash->addMessage('info', 'Application created');
        return $response->withRedirect('/accounts');
      }
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      return $response->withRedirect('/accounts');
    }
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

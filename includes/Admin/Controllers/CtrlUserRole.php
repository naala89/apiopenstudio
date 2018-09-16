<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Account;
use Datagator\Admin\Application;
use Datagator\Admin\UserAccount;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;
use Datagator\Admin\UserRole;

/**
 * Class CtrlUserRole.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlUserRole extends CtrlBase {

  protected $permittedRoles = ['Owner'];

  /**
   * CtrlUser constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param array $mailSettings
   *   Mail settings array.
   * @param \Slim\Views\Twig $view
   *   View container.
   */
  public function __construct(array $dbSettings, array $mailSettings, Twig $view) {
    $this->mailSettings = $mailSettings;
    parent::__construct($dbSettings, $view);
  }

  /**
   * Edit a users roles page.
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
    if (empty($args['uid'])) {
      return $this->view->render($response, 'users.twig', [
        'menu' => $menu,
        'message' => [
          'type' => 'error',
          'text' => 'No user account ID received.',
        ],
      ]);
    }

    // Fetch the user details.
    $userHlp = new UserAccount($this->dbSettings);
    $user = $userHlp->findByUserId($args['uid']);

    // Fetch all user accounts for the user.
    $userAccountHlp = new UserAccount($this->dbSettings);
    $userAccounts = $userAccountHlp->findByUserId($user['uid']);

    // Fetch the application for each of the user's user accounts.
    $applicationHlp = new Application($this->dbSettings);
    $applications = [];
    foreach ($userAccounts as $userAccount) {
      if (!empty($userAccount['appid'])) {
        $application = $applicationHlp->findByApplicationId($userAccount['appid']);
        $applications[$application['appid']] = $application;
      }
    }

    // Fetch the roles for each user account.
  }

}

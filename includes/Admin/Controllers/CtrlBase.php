<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\User;
use Datagator\Core\ApiException;
use Slim\Flash\Messages;
use Slim\Views\Twig;

/**
 * Class CtrlBase.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlBase {

  /**
   * @var array
   */
  protected $dbSettings;
  /**
   * @var int
   */
  protected $paginationStep;
  /**
   * @var Twig
   */
  protected $view;
  /**
   * @var Messages.
   */
  protected $flash;
  /**
   * @var array.
   */
  protected $menu;
  /**
   * @var array
   */
  protected $permittedRoles = [];

  /**
   * Base constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param int $paginationStep
   *   Pagination step.
   * @param \Slim\Views\Twig $view
   *   View container.
   * @param \Slim\Flash\Messages $flash
   *   Flash messages container.
   */
  public function __construct(array $dbSettings, $paginationStep, Twig $view, Messages $flash) {
    $this->dbSettings = $dbSettings;
    $this->paginationStep = $paginationStep;
    $this->view = $view;
    $this->flash = $flash;
  }

  /**
   * Fetch the roles for a user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return array
   *   Array of role names.
   */
  protected function getRoles($uid) {
    // If no account, no roles.
    if (empty($uid)) {
      return [];
    }

    // Get user roles for a user account.
    $roles = [];
    try {
      $userHlp = new User($this->dbSettings);
      $userHlp->findByUserId($uid);
      if ($userHlp->isAdministrator()) {
        $roles[] = 'Administrator';
      }
      if ($userHlp->isManager()) {
        $roles[] = 'Manager';
      }
      return array_merge($roles, $userHlp->findRoles());
    } catch (ApiException $e) {
      $this->flash->addMessage('error', $e->getMessage());
      return [];
    }
  }

  /**
   * Get available menu items for user's roles.
   *
   * @param array $roles
   *   Array of users roles.
   *
   * @return array
   *   Associative array of menu title and links.
   */
  protected function getMenus(array $roles) {
    $menus = [];

    if (empty($roles)) {
      $menus += [
        'Login' => '/login',
      ];
    }
    else {
      $menus += [
        'Home' => '/',
      ];
      if (in_array('Administrator', $roles)) {
        $menus += [
          'Accounts' => '/accounts',
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Manager', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Developer', $roles)) {
        $menus += [
          'Resources' => '/resources',
        ];
      }
      $menus += [
        'Logout' => '/logout',
      ];
    }

    return $menus;
  }

  /**
   * Validate user access by role.
   *
   * @param array $roles
   *   Array of user roles (text).
   *
   * @return bool
   *   Access validated.
   */
  protected function checkAccess(array $roles) {
    if (empty($this->permittedRoles)) {
      return TRUE;
    }
    foreach ($this->permittedRoles as $permittedRole) {
      if (in_array($permittedRole, $roles)) {
        return TRUE;
      }
    }
    return FALSE;
  }

}

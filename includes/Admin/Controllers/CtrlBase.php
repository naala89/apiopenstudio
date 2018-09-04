<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Role;
use Datagator\Admin\User;
use Datagator\Core\ApiException;
use Slim\Views\Twig;

/**
 * Class Base.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlBase {
  protected $dbSettings;
  protected $view;
  protected $menu;
  protected $permittedRoles = [];

  /**
   * Base constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param \Slim\Views\Twig $view
   *   View container.
   */
  public function __construct(array $dbSettings, Twig $view) {
    $this->dbSettings = $dbSettings;
    $this->view = $view;
  }

  /**
   * Fetch the roles for a user account ID.
   *
   * @param int $uid
   *   User ID.
   * @param int $accid
   *   Account ID.
   *
   * @return array
   *   Array of role names.
   */
  protected function getRoles($uid, $accid) {
    $roleNames = [];

    // If no account, no roles.
    if (empty($uid) || empty($accid)) {
      return $roleNames;
    }

    // Get user roles for a user account.
    try {
      $userHelper = new User($this->dbSettings);
    } catch (ApiException $e) {
      return $roleNames;
    }
    $result = $userHelper->findByUserId($uid);
    if (!$result) {
      return $roleNames;
    }
    return $userHelper->findRoles($accid);
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
      if (in_array('Owner', $roles)) {
        $menus += [
          'Applications' => '/applications',
          'Users' => '/users',
        ];
      }
      if (in_array('Administrator', $roles)) {
        $menus += [
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

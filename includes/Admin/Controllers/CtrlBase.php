<?php

namespace Datagator\Admin\Controllers;

use Datagator\Admin\Role;
use Datagator\Admin\User;
use Datagator\Admin\UserRole;
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
   * Fetch the roles for a user account.
   *
   * @param int $uaid
   *   User account id.
   *
   * @return array
   *   Array of role names.
   */
  protected function getRoles($uaid) {
    $roleNames = [];

    // If no account, no roles.
    if (empty($uaid)) {
      return $roleNames;
    }

    // Get user roles for a user account.
    $userRoleHelper = new UserRole($this->dbSettings);
    $userRoles = $userRoleHelper->findByUaid($uaid);
    if (empty($userRoles)) {
      return $roleNames;
    }

    // Get names for the roles.
    $roleHelper = new Role($this->dbSettings);
    foreach ($userRoles as $userRole) {
      $result = $roleHelper->findByRid($userRole['rid']);
      if (!in_array($result['name'], $roleNames)) {
        $roleNames[] = $result['name'];
      }
    }

    return $roleNames;
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

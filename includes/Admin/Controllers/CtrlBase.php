<?php

namespace Datagator\Admin\Controllers;

use Datagator\Db\UserMapper;
use Datagator\Db\AccountMapper;
use Datagator\Db\UserRoleMapper;
use Datagator\Db\RoleMapper;
use Slim\Views\Twig;

/**
 * Class Base.
 *
 * @package Datagator\Admin\Controllers
 */
class CtrlBase {
  protected $db;
  protected $view;
  protected $menu;
  protected $permittedRoles = [];

  /**
   * Base constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param Twig $view
   *   View container.
   */
  public function __construct(array $dbSettings, Twig $view) {
    $this->view = $view;

    $dsnOptions = '';
    if (count($dbSettings['options']) > 0) {
      foreach ($dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($dbSettings['options']) > 0 ? '?' . implode('&', $dbSettings['options']) : '';
    $dsn = $dbSettings['driver'] . '://'
      . $dbSettings['username'] . ':'
      . $dbSettings['password'] . '@'
      . $dbSettings['host'] . '/'
      . $dbSettings['database'] . $dsnOptions;
    $this->db = \ADONewConnection($dsn);
  }

  /**
   * Fetch the roles for a user in an account.
   *
   * @param string $token
   *   User validation Token.
   * @param string $account
   *   Account name.
   *
   * @return array
   *   Array of role names.
   */
  protected function getRoles($token, $account) {
    $userMapper = new UserMapper($this->db);
    $user = $userMapper->findBytoken($token);
    if (empty($uid = $user->getUid())) {
      return [];
    }

    $accountMapper = new AccountMapper($this->db);
    $account = $accountMapper->findByName($account);
    if (empty($accId = $account->getAccId())) {
      return [];
    }

    $userRoleMapper = new UserRoleMapper($this->db);
    $roles = $userRoleMapper->findBy($uid, NULL, NULL, $accId);

    $roleMapper = new RoleMapper($this->db);
    $result = [];
    foreach ($roles as $role) {
      $role = $roleMapper->findByRid($role->getRid());
      $result[] = $role->getName();
    }

    return $result;
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
    $result = [];

    if (empty($roles)) {
      $result += [
        'Login' => '/login'
      ];
    } else {
      if (in_array('Owner', $roles)) {
        $result += [
          'Applications' => '/applications',
          'Users' => '/users'
        ];
      }
      if (in_array('Administrator', $roles)) {
        $result += [
          'Users' => '/users'
        ];
      }
      if (in_array('Developer', $roles)) {
        $result += [
          'Resources' => '/resources'
        ];
      }
      $result += [
        'Logout' => '/logout'
      ];
    }

    return $result;
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

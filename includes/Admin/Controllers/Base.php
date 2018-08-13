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
class Base {
  protected $dbSettings;
  protected $db;
  protected $view;
  protected $menu;

  /**
   * Base constructor.
   *
   * @param array $dbSettings
   *   DB settings array.
   * @param Twig $view
   *   View container.
   */
  public function __construct(array $dbSettings, Twig $view) {
    $this->dbSettings = $dbSettings;
    $this->view = $view;

    $dsnOptions = '';
    if (count($this->dbSettings['options']) > 0) {
      foreach ($this->dbSettings['options'] as $k => $v) {
        $dsnOptions .= count($dsnOptions) == 0 ? '?' : '&';
        $dsnOptions .= "$k=$v";
      }
    }
    $dsnOptions = count($this->dbSettings['options']) > 0 ? '?' . implode('&', $this->dbSettings['options']) : '';
    $dsn = $this->dbSettings['driver'] . '://'
      . $this->dbSettings['username'] . ':'
      . $this->dbSettings['password'] . '@'
      . $this->dbSettings['host'] . '/'
      . $this->dbSettings['database'] . $dsnOptions;
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

  protected function getMenus($roles) {
    $result = [];

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

    return $result;
  }

}

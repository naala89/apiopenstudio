<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * User_role table CRUD.
 */

class UserRole extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'User Role',
    'machineName' => 'user_role',
    'description' => 'CRUD operations for user roles.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'username' => array(
        'description' => 'The username of the user.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'accountName' => array(
        'description' => 'the account name for the user/role. Only used in create or delete.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'applicationName' => array(
        'description' => 'the application name for the user/role. Only used in create or delete.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'roleName' => array(
        'description' => 'The role name for the user in the application. Only used in create or delete.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $username = $this->val('username', TRUE);
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUsername($username);
    $uid = $user->getUid();

    if (empty($uid)) {
      throw new Core\ApiException("Invalid user: $username", 1, $this->id);
    }

    $method = $this->request->getMethod();

    switch ($method) {
      case 'get':
        return $this->getRoles($uid);
        break;

      case 'post':
        return $this->createRole($uid);
        break;

      case 'delete':
        return $this->deleteRole($uid);
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }

  /**
   * Get roles for a uid.
   *
   * @param int $uid
   *
   * @return array
   */
  protected function getRoles($uid)
  {
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $accountMapper = new Db\AccountMapper($this->db);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $roleMapper = new Db\RoleMapper($this->db);

    $userRoles = $userRoleMapper->findByUid($uid);
    $result = [];

    foreach ($userRoles as $userRole) {
      $accId = $userRole->getAccId();
      $appId = $userRole->getAppId();
      $rid = $userRole->getRid();

      if (empty($result[$accId])) {
        $account = $accountMapper->findByAccid($accId);
        $result[$accId] = [
          'account_name' => $account->getName(),
        ];
      }
      if (empty($result[$accId][$appId])) {
        $application = $applicationMapper->findByAppid($appId);
        $result[$accId][$appId] = [
          'application_name' => $application->getName(),
        ];
      }
      $role = $roleMapper->findById($rid);
      $result[$accId][$appId][$rid] = $role->getName();
    }
    return $result;
  }

  /**
   * createRole
   *
   * @param  mixed $uid
   *
   * @return void
   */
  protected function createRole($uid)
  {
    $userRoleMapper = new Db\UserRoleMapper($this->db);

    // Get the role id.
    $roleName = $this->val('roleName', TRUE);
    $rolemapper = new Db\RoleMapper($this->db);
    $role = $rolemapper->findByName($roleName);
    $rid = $role->getRid();
    if (empty($rid)) {
      throw new Core\ApiException("Invalid role: $roleName", 1, $this->id);
    }

    // Get the account id.
    $accountName = $this->val('accountName', TRUE);
    $accountMapper = new Db\AccountMapper($this->db);
    $account = $accountMapper->findByName($accountName);
    $accId = $account->getAccid();
    if ($roleName != 'Administrator' && empty($accId)) {
      throw new Core\ApiException("Invalid, only an administrator role can be assigned without an account,", 1, $this->id);
    }

    // Get the application id.
    $applicationName = $this->val('applicationName', TRUE);
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $application = $applicationMapper->findByAccidAppname($accId, $applicationName);
    $appId = $application->getAppId();
    if (!in_array($roleName, ['Administrator', 'Account manager']) && empty($appId)) {
      throw new Core\ApiException("Invalid, only an administrator role can be assigned without an application,", 1, $this->id);
    }

    // Validate user role does not already exist.
    $roles = $userRoleMapper->findByUid($uid);
    foreach($roles as $role) {
      if ($role->getUid() == $uid
      && $role->getAppid() == $appId
      && $role->getAccid() == $accId
      && $role->getRid() == $rid) {
        throw new Core\ApiException("User role already exists.", 1, $this->id);
      }
    }

    // Create the user role.
    $userRole = new Db\UserRole(NULL, $accId, $appId, $uid, $rid);
    return $userRoleMapper->save($userRole);
  }

  /**
   * deleteRole
   *
   * @param  mixed $uid
   *
   * @return void
   */
  protected function deleteRole($uid)
  {
    $userRoleMapper = new Db\UserRoleMapper($this->db);
    $applicationName = $this->val('applicationName');
    $applicationMapper = new Db\ApplicationMapper($this->db);
    $application = $applicationMapper->findByName($applicationName);
    $appId = $application->getAppId();

    if (empty($appId)) {
      throw new Core\ApiException("Invalid application: $applicationName", 1, $this->id);
    }
    if (empty($rid)) {
      throw new Core\ApiException("Invalid role: $roleName", 1, $this->id);
    }

    $roleName = $this->val('roleName');
    $rolemapper = new Db\RoleMapper($this->db);
    $role = $rolemapper->findByName($roleName);
    $rid = $role->getRid();

    $userRoles = $userRoleMapper->findByMixed($uid, $appId, $rid);
    foreach ($userRoles as $userRole) {
      $userRoleMapper->delete($userRole);
    }
    return true;
  }
}

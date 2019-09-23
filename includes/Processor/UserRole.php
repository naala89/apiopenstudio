<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

/**
 * User_role table CRUD.
 */

class UserRole extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'User Role',
    'machineName' => 'userRole',
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
    Core\Debug::variable($this->meta, 'Processor DatagatorUser', 4);

    $db = $this->getDb();

    $username = $this->val('username');
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findByUsername($username);
    $uid = $user->getUid();

    if (empty($uid)) {
      throw new Core\ApiException("Invalid user: $username", 1, $this->id);
    }

    $method = $this->request->method;

    switch ($method) {
      case 'get':
        return $this->getRoles($db, $uid);
        break;

      case 'post':
        return $this->createRole($db, $uid, $accId, $appId);
        break;

      case 'delete':
        return $this->deleteRole($db, $uid, $accId, $appId);
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }

  protected function getRoles($db, $uid)
  {
    $userRoleMapper = new Db\UserRoleMapper($db);
    $accountMapper = new Db\AccountMapper($db);
    $applicationMapper = new Db\ApplicationMapper($db);
    $roleMapper = new Db\RoleMapper($db);

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

  protected function createRole($db, $uid, $accId, $appId)
  {
    $userRoleMapper = new Db\UserRoleMapper($db);

    // Get the role id.
    $roleName = $this->val('roleName');
    $rolemapper = new Db\RoleMapper($db);
    $role = $rolemapper->findByName($roleName);
    $rid = $role->getRid();
    if (empty($rid)) {
      throw new Core\ApiException("Invalid role: $roleName", 1, $this->id);
    }

    // Get the account id.
    $accountName = $this->val('accountName');
    $accountMapper = new Db\AccountMapper($db);
    $account = $applicationMapper->findByName($accountName);
    $accId = $account->getId();
    if ($roleName != 'Administrator' && empty($accId)) {
      throw new Core\ApiException("Invalid user role, the role should be assigned to an account,", 1, $this->id);
    }

    // Get the application id.
    $applicationName = $this->val('applicationName');
    $applicationMapper = new Db\ApplicationMapper($db);
    $application = $applicationMapper->findByName($applicationName);
    $appId = $application->getAppId();
    if (!in_array($roleName, ['Administrator', 'Account manager']) && empty($appId)) {
      throw new Core\ApiException("Invalid user role, the role should be assigned to an application.", 1, $this->id);
    }

    // Validate user role does not already exist.
    $roles = $userRoleMapper->findByUid($uid);
    foreach($roles as $role) {
      if ($role->getAccid() == $accId
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

  protected function deleteRole($db, $uid, $accId, $appId)
  {
    $userRoleMapper = new Db\UserRoleMapper($db);
    $applicationName = $this->val('applicationName');
    $applicationMapper = new Db\ApplicationMapper($db);
    $application = $applicationMapper->findByName($applicationName);
    $appId = $application->getAppId();

    if (empty($appId)) {
      throw new Core\ApiException("Invalid application: $applicationName", 1, $this->id);
    }
    if (empty($rid)) {
      throw new Core\ApiException("Invalid role: $roleName", 1, $this->id);
    }

    $roleName = $this->val('roleName');
    $rolemapper = new Db\RoleMapper($db);
    $role = $rolemapper->findByName($roleName);
    $rid = $role->getRid();

    $userRoles = $userRoleMapper->findByMixed($uid, $appId, $rid);
    foreach ($userRoles as $userRole) {
      $userRoleMapper->delete($userRole);
    }
    return true;
  }
}

<?php

/**
 * User_role table CRUD.
 */

namespace Datagator\Processor;
use Datagator\Core;
use Datagator\Db;

class DatagatorUserRole extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Datagator User Role',
    'description' => 'CRUD operations for Datagator user roles.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => array(
      'username' => array(
        'description' => 'The username of the user.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'string'),
      ),
      'applicationName' => array(
        'description' => 'the application name for the user/role.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'string'),
      ),
      'roleName' => array(
        'description' => 'The role name for the user in the application.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'string'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor DatagatorUser', 4);

    $db = $this->getDb();

    $username = $this->val($this->meta->username);
    $userMapper = new Db\UserMapper($db);
    $user = $userMapper->findByUsername($username);
    $uid = $user->getUid();

    $applicationName = $this->val($this->meta->applicationName);
    $applicationMapper = new Db\ApplicationMapper($db);
    $application = $applicationMapper->findByName($applicationName);
    $appId = $application->getAppId();

    $roleName = $this->val($this->meta->roleName);
    $rolemapper = new Db\RoleMapper($db);
    $role = $rolemapper->findByName($roleName);
    $rid = $role->getRid();

    $userRoleMapper = new Db\UserRoleMapper($db);
    $method = $this->request->method;

    switch ($method) {

      case 'post':
        if (empty($uid)) {
          throw new Core\ApiException("Invalid user: $username", 1, $this->id);
        }
        if (empty($appId)) {
          throw new Core\ApiException("Invalid application: $applicationName", 1, $this->id);
        }
        if (empty($rid)) {
          throw new Core\ApiException("Invalid role: $roleName", 1, $this->id);
        }
        $userRole = $userRoleMapper->findByUserAppRole($uid, $appId, $rid);
        $userRole->setUid($uid);
        $userRole->setAppId($appId);
        $userRole->setRid($rid);
        return $userRoleMapper->save($userRole);
        break;

      case 'get':
        $userRoles = $userRoleMapper->findByMixed($uid, $appId, $rid);
        $result = array();
        foreach ($userRoles as $userRole) {
          $result[] = $userRole->debug();
        }
        return $result;
        break;

      case 'delete':
        $userRoles = $userRoleMapper->findByMixed($uid, $appId, $rid);
        foreach ($userRoles as $userRole) {
          $userRoleMapper->delete($userRole);
        }
        return true;
        break;

      default:
        throw new Core\ApiException('Invalid action', 1, $this->id);
        break;
    }
  }
}

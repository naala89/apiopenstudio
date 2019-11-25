<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

/**
 * User role delete.
 */

class UserRoleDelete extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User Role delete',
    'machineName' => 'user_role_delete',
    'description' => 'Delete a role for a user.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'urid' => [
        'description' => 'The user role ID.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $urid = $this->val('urid', TRUE);

    $userRoleMapper = new Db\UserRoleMapper($this->db);

    $userRoles = $userRoleMapper->findByFilter(['col' => [
      'urid' => $urid,
    ]]);
    $userRole = $userRoles[0];
    var_dump($userRole->getUrid());die();
    if (empty($userRole->getUrid())) {
      throw new Core\ApiException('User role does not exist', 6, $this->id, 400);
    }
    if ($userRole->rid == 1) {
      $userRoles = $userRoleMapper->findByFilter(['col' => [
        'rid' => 1,
      ]]);
      if (count($userRoles) < 2) {
        throw new Core\ApiException('Cannot delete Administrator role if only one exists', 6, $this->id, 400);
      }
    }

    $userRole = new Db\UserRole($userRole->urid);
    return $userRoleMapper->delete($userRole);
  }

}

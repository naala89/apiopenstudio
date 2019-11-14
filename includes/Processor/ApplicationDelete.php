<?php

/**
 * Delete an application.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Db;
use Gaterdata\Db\UserRoleMapper;

class ApplicationDelete extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Application delete',
    'machineName' => 'application_delete',
    'description' => 'Delete an application.',
    'menu' => 'Admin',
    'input' => [
      'applicationId' => [
        'description' => 'The appication ID of the application.',
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

    $appid = $this->val('applicationId', TRUE);

    $applicationMapper = new Db\ApplicationMapper($this->db);
    $resourceMapper = new Db\ResourceMapper($this->db);
    $userRoleMapper = new Db\UserRoleMapper($this->db);

    $application = $applicationMapper->findByAppid($appid);
    if (empty($application->getAppid())) {
      throw new ApiException("Delete application, no such appid: $appid", 6, $this->id, 417);
    }
    $resources = $resourceMapper->findByAppId($appid);
    if (!empty($resources)) {
      throw new ApiException("Delete application, resources are assigned to this application: $appid", 6, $this->id, 417);
    }
    $userRoles = $userRoleMapper->findByFilter(['col' => ['appid' => $appid]]);
    if (!empty($userRoles)) {
      throw new ApiException("Delete application, users are assigned to this application: $appid", 6, $this->id, 417);
    }

    return $applicationMapper->delete($application);
  }
}

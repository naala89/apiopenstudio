<?php

/**
 * Fetch list of resources.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db\ResourceMapper;

class ResourceRead extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Resource read',
    'machineName' => 'resource_read',
    'description' => 'List resources. If no appid/s ir resid is defined, all will be returned.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'res_id' => [
        'description' => 'The Resource ID to filter by".',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'app_id' => [
        'description' => 'The application IDs to filter by. Comma separated if Multiple".',
        'cardinality' => [0, '*'],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer', 'string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  /**
   * {inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $resourceMapper = new ResourceMapper($this->db);
    $appId = $this->val('app_id', TRUE);
    $resId = $this->val('res_id', TRUE);

    if (!empty($resId)) {
      $resource = $resourceMapper->findId($resId);
      if (empty($resource->getResid())) {
        throw new Core\ApiException('Unknown resource', 6, $this->id, 401);
      }
      return $resource->dump();
    }

    if (!empty($appId)) {
      if (!is_numeric($appId)) {
        $appId = explode(',', $appId);
      }
      $result = $resourceMapper->findByAppId($appId);
      if (empty($result)) {
        throw new Core\ApiException('Unknown resource', 6, $this->id, 401);
      }
    }
    else {
      $result = $resourceMapper->all();
    }

    $resources = [];
    foreach ($result as $item) {
      $resources[] = $item->dump();
    }
    return $resources;
  }
}

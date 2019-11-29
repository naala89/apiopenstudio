<?php

/**
 * Fetch list of resources.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db\ResourceMapper;

class ResourceRead extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Resource read',
    'machineName' => 'resource_read',
    'description' => 'List resources. If no appid/s ir resid is defined, all will be returned.',
    'menu' => 'Admin',
    'input' => [
      'res_id' => [
        'description' => 'The Resource ID to filter by".',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => '',
      ],
      'app_id' => [
        'description' => 'The application IDs to filter by. Comma separated if Multiple.',
        'cardinality' => [0, '*'],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer', 'string'],
        'limitValues' => [],
        'default' => '',
      ],
      'order_by' => [
        'description' => 'order by column',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['accid', 'appid', 'method', 'uri'],
        'default' => '',
      ],
      'direction' => [
        'description' => 'Sort direction',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['asc', 'desc'],
        'default' => '',
      ],
      'keyword' => [
        'description' => 'Keyword search',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => '',
      ],
    ],
  ];

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $resourceMapper = new ResourceMapper($this->db);
    $resId = $this->val('res_id', TRUE);

    if (!empty($resId)) {
      $resource = $resourceMapper->findId($resId);
      if (empty($resource->getResid())) {
        throw new Core\ApiException('Unknown resource', 6, $this->id, 400);
      }
      return $resource->dump();
    }

    $appId = $this->val('app_id', TRUE);
    $keyword = $this->val('keyword', TRUE);
    $orderBy = $this->val('order_by', TRUE);
    $direction = $this->val('direction', TRUE);

    $params = [];
    if (!empty($keyword)) {
      $params['filter'][] = ['keyword' => "%$keyword%", 'column' => 'uri'];
    }
    if (!empty($orderBy)) {
      $params['order_by'] = $orderBy;
    }
    if (!empty($direction)) {
      $params['direction'] = $direction;
    }

    if (!empty($appId)) {
      if (!is_numeric($appId)) {
        $appId = explode(',', $appId);
      }
      $result = $resourceMapper->findByAppId($appId, $params);
      if (empty($result)) {
        throw new Core\ApiException('Unknown resource', 6, $this->id, 400);
      }
    }
    else {
      $result = $resourceMapper->all($params);
      if (empty($result)) {
        throw new Core\ApiException('Unknown resource', 6, $this->id, 400);
      }
    }

    $resources = [];
    foreach ($result as $item) {
      $resources[] = $item->dump();
    }
    return $resources;
  }
}

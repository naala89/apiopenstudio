<?php

/**
 * Fetch a single or multiple applications.
 *
 * @TODO: This currently assumes that account name is unique
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\Config;
use Gaterdata\Core\Debug;
use Gaterdata\Db;

class ApplicationRead extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Application read',
    'machineName' => 'application_read',
    'description' => 'Fetch a single or multiple applications.',
    'menu' => 'Admin',
    'input' => [
      'accountIds' => [
        'description' => 'An array of the IDs of the account to fetch applications by. NULL or empty will fetch for all accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['array'],
        'limitValues' => [],
        'default' => ''
      ],
      'applicationNames' => [
        'description' => 'An array of the application names. NULL or empty will fetch all applications for the accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['array'],
        'limitValues' => [],
        'default' => ''
      ],
      'accountFilter' => [
        'description' => 'Account ID to filter by.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'keyword' => [
        'description' => 'Application keyword to filter by.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string', 'integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'orderBy' => [
        'description' => 'Order by column.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['accid', 'appid', 'name'],
        'default' => ''
      ],
      'direction' => [
        'description' => 'Order by direction.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['asc', 'desc'],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $accountIds = $this->val('accountIds', TRUE);
    $accountIds = empty($accountIds) ? [] : $accountIds;
    $applicationNames = $this->val('applicationNames', TRUE);
    $applicationNames = empty($applicationNames) ? [] : $applicationNames;

    // Filter params.
    $params = [];
    $accountFilter = $this->val('accountFilter', TRUE);
    if (!empty($accountFilter)) {
      $params['filter'] = [
        'column' => 'accid',
        'keyword' => $accountFilter,
      ];
    }
    $keyword = $this->val('keyword', TRUE);
    if (!empty($keyword)) {
      $params['keyword'] = "%$keyword%";
    }
    $orderBy = $this->val('orderBy', TRUE);
    if (!empty($orderBy)) {
      $params['order_by'] = $orderBy;
    }
    $direction = $this->val('direction', TRUE);
    if (!empty($direction)) {
      $params['direction'] = $direction;
    }

    $applicationMapper = new Db\ApplicationMapper($this->db);
    Debug::variable($params, 'params');

    $applications = $applicationMapper->findByAccidsAppnames($accountIds, $applicationNames, $params);
    $result = [];
    foreach($applications as $application) {
      $result[$application->getAppid()] = [
        'name' => $application->getName(),
        'accid' =>$application->getAccid(),
      ];
    }

    return $result;
  }
}

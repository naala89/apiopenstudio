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
        'description' => 'An array of the application names. NULL or empoty will fetch all applications for the accounts.',
        'cardinality' => [0, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['array'],
        'limitValues' => [],
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

    $applicationMapper = new Db\ApplicationMapper($this->db);

    $applications = $applicationMapper->findByAccidsAppnames($accountIds, $applicationNames);
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

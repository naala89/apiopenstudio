<?php

/**
 * Create an applications.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\ApiException;
use Gaterdata\Db;

class ApplicationCreate extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Application create',
    'machineName' => 'application_create',
    'description' => 'Create an application.',
    'menu' => 'Admin',
    'input' => [
      'accid' => [
        'description' => 'The parent account ID for the application.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'name' => [
        'description' => 'The application name.',
        'cardinality' => [1, 1],
        'literalAllowed' => TRUE,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $accid = $this->val('accid', TRUE);
    $name = $this->val('name', TRUE);

    $accountMapper = new Db\AccountMapper($this->db);
    $applicationMapper = new Db\ApplicationMapper($this->db);

    $account = $accountMapper->findByAccid($accid);
    if (empty($account->getAccid())) {
      throw new ApiException('Account does not exist: "' . $accid . '"', 6, $this->id, 417);
    }

    $application = new Db\Application(NULL, $accid, $name);
    return $applicationMapper->save($application);
  }
}

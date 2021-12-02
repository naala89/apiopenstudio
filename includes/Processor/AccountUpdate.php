<?php

/**
 * Class AccountUpdate.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Db\Account;
use ApiOpenStudio\Db\AccountMapper;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Core\Config;

/**
 * Class AccountUpdate
 *
 * Processor class to update an account.
 */
class AccountUpdate extends ProcessorEntity
{
    /**
     * @var AccountMapper
     */
    protected AccountMapper $accountMapper;

    /**
     * @var ApplicationMapper
     */
    protected ApplicationMapper $applicationMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Account update',
        'machineName' => 'account_update',
        'description' => 'Rename an account.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The account ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'name' => [
                'description' => 'The new name for the account.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * AccountRead constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $name = $this->val('name', true);
        if (preg_match('/[^a-z_\-0-9]/i', $name)) {
            throw new ApiException(
                "Invalid account name: $name. Only underscore, hyphen or alhpanumeric characters permitted.",
                6,
                $this->id,
                400
            );
        }
        $accid = $this->val('accid', true);

        $account = $this->accountMapper->findByName($name);
        if (!empty($account->getAccid())) {
            throw new ApiException("Account already exists: $name", 6, $this->id, 400);
        }
        $account = $this->accountMapper->findByAccid($accid);
        if (empty($account->getAccid())) {
            throw new ApiException("Account does not exist: $accid", 6, $this->id, 400);
        }

        $account->setName($name);
        if (!$this->accountMapper->save($account)) {
            throw new ApiException('save account failed, please check the logs', 2, $this->id, 400);
        }
        $this->updateOpenApiForApplications($account);
        return new DataContainer($account->dump(), 'array');
    }

    /**
     * @throws ApiException
     */
    protected function updateOpenApiForApplications(Account $account)
    {
        $settings = new Config();
        $openApiClassName = "\\ApiOpenStudio\\Core\\OpenApi\\OpenApiParent" .
            substr($settings->__get(['api', 'openapi_version']), 0, 1);
        $openApi = new $openApiClassName();
        $applications = $this->applicationMapper->findByAccid($account->getAccid());
        foreach ($applications as $application) {
            $schema = $application->getOpenapi();
            if (!empty($schema)) {
                $openApi->import($application->getOpenapi());
            } else {
                $openApi->setDefault($account->getName(), $application->getName());
            }
            $openApi->setAccount($account->getName());
            $application->setOpenapi($openApi->export());
            $this->applicationMapper->save($application);
        }
    }
}

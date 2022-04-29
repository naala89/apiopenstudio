<?php

/**
 * Class AccountUpdate.
 *
 * @package    ApiOpenStudio\Processor
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
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
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
     * @var Config
     */
    protected Config $settings;

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
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($db, $logger);
        $this->applicationMapper = new ApplicationMapper($db, $logger);
        $this->settings = new Config();
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

        try {
            $account = $this->accountMapper->findByName($name);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($account->getAccid())) {
            throw new ApiException("Account already exists: $name", 6, $this->id, 400);
        }

        try {
            $account = $this->accountMapper->findByAccid($accid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($account->getAccid())) {
            throw new ApiException("Account does not exist: $accid", 6, $this->id, 400);
        }

        try {
            $account->setName($name);
            $this->accountMapper->save($account);
            $this->updateOpenApiForApplications($account);
            $result = new DataContainer($account->dump(), 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * @throws ApiException
     */
    protected function updateOpenApiForApplications(Account $account)
    {
        $openApiParentClassName = Utilities::getOpenApiParentClassPath($this->settings);
        $openApiParentClass = new $openApiParentClassName();
        $openApiParentClass = new $openApiParentClass();
        $applications = $this->applicationMapper->findByAccid($account->getAccid());
        foreach ($applications as $application) {
            $schema = $application->getOpenapi();
            if (!empty($schema)) {
                $openApiParentClass->import($application->getOpenapi());
            } else {
                $openApiParentClass->setDefault($account->getName(), $application->getName());
            }
            $openApiParentClass->setAccount($account->getName());
            $application->setOpenapi($openApiParentClass->export());
            $this->applicationMapper->save($application);
        }
    }
}

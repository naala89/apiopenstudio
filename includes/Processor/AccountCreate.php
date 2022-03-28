<?php

/**
 * Class AccountCreate.
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
use ApiOpenStudio\Db\AccountMapper;

/**
 * Class AccountCreate
 *
 * Processor class to create an account.
 */
class AccountCreate extends ProcessorEntity
{
    /**
     * @var AccountMapper
     */
    protected AccountMapper $accountMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Account create',
        'machineName' => 'account_create',
        'description' => 'Create an account.',
        'menu' => 'Admin',
        'input' => [
            'name' => [
                'description' => 'The name of the account. This must contain alphanumeric characters only.',
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
     * AccountCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->accountMapper = new AccountMapper($this->db, $logger);
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
                "Invalid account name: $name. Only underscore, hyphen or alphanumeric characters permitted.",
                6,
                $this->id,
                400
            );
        }

        $account = $this->accountMapper->findByName($name);
        if (!empty($account->getAccid())) {
            throw new ApiException("Invalid account name: $name. This account already exists.", 6, $this->id, 400);
        }

        $account->setName($name);
        if (!$this->accountMapper->save($account)) {
            throw new ApiException('save account failed, please check the logs', 2, $this->id, 400);
        }
        $account = $this->accountMapper->findByName($name);
        return new DataContainer($account->dump(), 'array');
    }
}

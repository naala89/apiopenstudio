<?php
/**
 * Class AccountDelete.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

/**
 * Class AccountDelete
 *
 * Processor class to delete an account.
 */
class AccountDelete extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Account delete',
        'machineName' => 'account_delete',
        'description' => 'Delete an account.',
        'menu' => 'Admin',
        'input' => [
            'accid' => [
                'description' => 'The account ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => ''
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $accid = $this->val('accid', true);
        $this->logger->debug('Deleting account' . $accid);

        $accountMapper = new Db\AccountMapper($this->db);
        $account = $accountMapper->findByAccid($accid);

        if (empty($account->getAccid())) {
            throw new Core\ApiException("Account does not exist: $accid", 6, $this->id, 400);
        }
        // Do not delete if applications are attached to the account.
        $applicationMapper = new Db\ApplicationMapper($this->db);
        $applications = $applicationMapper->findByAccid($accid);
        if (!empty($applications)) {
            $message = 'Cannot delete the account, applications are assigned to the account';
            throw new Core\ApiException($message, 6, $this->id, 400);
        }

        return $accountMapper->delete($account);
    }
}

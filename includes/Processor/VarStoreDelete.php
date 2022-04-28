<?php

/**
 * Class VarStoreDelete.
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
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreDelete
 *
 * Processor class to delete a var-store variable.
 */
class VarStoreDelete extends Core\ProcessorEntity
{
    /**
     * @var array|string[] Array of permitted roles
     */
    protected array $permittedRoles = [
        'Administrator',
        'Account manager',
        'Application manager',
        'Developer',
    ];

    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private VarStoreMapper $varStoreMapper;

    /**
     * Account mapper class.
     *
     * @var ApplicationMapper
     */
    private ApplicationMapper $applicationMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store delete',
        'machineName' => 'var_store_delete',
        'description' => 'Delete a global variable. This will remove it for all resources within an application group.',
        'menu' => 'Variables',
        'input' => [
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => 'all',
            ],
        ],
    ];

    /**
     * VarStoreDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $vid = $this->val('vid', true);

        try {
            $var = $this->varStoreMapper->findByVid($vid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($var->getVid())) {
            throw new Core\ApiException("unknown vid: $vid", 6, $this->id, 400);
        }
        $appid = $var->getAppid();

        // Validate access to the existing var's application
        $permitted = false;
        $roles = Core\Utilities::getRolesFromToken();
        $accounts = [];
        foreach ($roles as $role) {
            if ($role['role_name'] == 'Administrator' && in_array('Administrator', $this->permittedRoles)) {
                $permitted = true;
            } elseif ($role['role_name'] == 'Account manager' && in_array('Account manager', $this->permittedRoles)) {
                $accid = $role['accid'];
                if (!isset($accounts[$accid])) {
                    try {
                        $accountsObjects = $this->applicationMapper->findByAccid($accid);
                    } catch (ApiException $e) {
                        throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
                    }
                    foreach ($accountsObjects as $accountObject) {
                        $accounts[$accid][] = $accountObject->getAppid();
                    }
                }
                if (in_array($appid, $accounts[$accid])) {
                    $permitted = true;
                }
            } elseif ($role['appid'] == $appid && in_array($role['role_name'], $this->permittedRoles)) {
                $permitted = true;
            }
        }
        if (!$permitted) {
            throw new Core\ApiException("permission denied", 6, $this->id, 400);
        }

        try {
            $this->varStoreMapper->delete($var);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer(true, 'boolean');
    }
}

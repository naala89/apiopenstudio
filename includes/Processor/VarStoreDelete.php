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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\ApplicationMapper;
use ApiOpenStudio\Db\VarStore;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreDelete
 *
 * Processor class to delete a var-store variable.
 */
class VarStoreDelete extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store delete',
        'machineName' => 'var_store_delete',
        // phpcs:ignore
        'description' => 'Delete single or multiple global variables. You can delete variables based on account/application, key or id.',
        'menu' => 'Variables',
        'input' => [
            'validate_access' => [
                // phpcs:ignore
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open. By default this is true for security reasons, but to allow consumers to use this in a resource, you will need to set it to false (otherwise access will be denied).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'vid' => [
                'description' => 'Var store ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'accid' => [
                'description' => 'Var store account ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'Var store application ID.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'key' => [
                'description' => 'Var store key.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'keyword' => [
                'description' => 'Filter variables to delete by keyword',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
        ],
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
     * VarStoreDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->varStoreMapper = new VarStoreMapper($db, $logger);
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

        $validateAccess = $this->val('validate_access', true);
        $vid = $this->val('vid', true);
        $accid = $this->val('accid', true);
        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $keyword = $this->val('keyword', true);

        try {
            $vars = $this->fetchVars($vid, $accid, $appid, $key, $keyword);
            if (empty($vars)) {
                throw new ApiException(
                    'no variables matching matching the criteria',
                    6,
                    $this->id,
                    400
                );
            }
            if ($validateAccess) {
                $beforeFilterCount = sizeof($vars);
                $vars = $this->filterVarsByPerms($vars, Utilities::getRolesFromToken());
                if (sizeof($vars) < $beforeFilterCount) {
                    throw new ApiException(
                        'permission denied. You do not have delete rights to all the variables in the result',
                        4,
                        $this->id,
                        403
                    );
                }
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        try {
            $this->varStoreMapper->deleteMultiple($vars);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new DataContainer(true, 'boolean');
    }

    /**
     * Fetch the variables without any user role validation.
     *
     * @param int|null $vid
     *   Var ID filter.
     * @param int|null $accid
     *   Account ID filter.
     * @param int|null $appid
     *   Application ID filter.
     * @param string|null $key
     *   Var name filter.
     * @param string|null $keyword
     *   Var name search filter.
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function fetchVars(
        ?int $vid,
        ?int $accid,
        ?int $appid,
        ?string $key,
        ?string $keyword
    ): array {
        $params = [];
        if (!empty($vid)) {
            $params['filter'][] = ['value' => $vid, 'column' => '`vid`'];
        }
        if (!empty($accid)) {
            $params['filter'][] = ['value' => $accid, 'column' => '`accid`'];
        }
        if (!empty($appid)) {
            $params['filter'][] = ['value' => $appid, 'column' => '`appid`'];
        }
        if (!empty($key)) {
            $params['filter'][] = ['value' => $key, 'column' => '`key`'];
        }
        if (!empty($keyword)) {
            $params['filter'][] = ['keyword' => "%$keyword%", 'column' => '`key`'];
        }

        return $this->varStoreMapper->findAll($params);
    }

    /**
     * Take an array of variables and return an array of variables the users has permissions for.
     *
     * @param VarStore[] $vars
     * @param array $roles
     *
     * @return VarStore[]
     *
     * @throws ApiException
     */
    protected function filterVarsByPerms(array $vars, array $roles): array
    {
        foreach ($vars as $index => $var) {
            $permitted = false;
            foreach ($roles as $role) {
                if ($role['role_name'] == 'Administrator') {
                    $permitted = true;
                } elseif ($role['role_name'] == 'Account manager') {
                    if (!empty($var->getAccid())) {
                        if ($var->getAccid() == $role['accid']) {
                            $permitted = true;
                        }
                    } else {
                        $application = $this->applicationMapper->findByAppid($var->getAppid());
                        if ($application->getAccid() == $role['accid']) {
                            $permitted = true;
                        }
                    }
                } else {
                    if (!empty($var->getAccid())) {
                        if ($var->getAccid() == $role['accid']) {
                            $permitted = true;
                        }
                    } elseif ($var->getAppid() == $role['appid']) {
                        $permitted = true;
                    }
                }
            }
            if (!$permitted) {
                unset($vars[$index]);
            }
        }

        return array_values($vars);
    }
}

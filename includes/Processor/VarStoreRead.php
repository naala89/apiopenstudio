<?php

/**
 * Class VarStoreRead.
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
 * Class VarStoreRead
 *
 * Processor class to fetch a var-store variable.
 */
class VarStoreRead extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var store read',
        'machineName' => 'var_store_read',
        // phpcs:ignore
        'description' => 'Read a global variable. This is available to all resources within an application group. If the application is core, then all vars are returned.',
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
            'strict' => [
                // phpcs:ignore
                'description' => 'If set to false then an empty array will be returned if the var/s do not exist. If set to true, an exception will be thrown if var does not exist. Default is true.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'vid' => [
                'description' => 'Var ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'accid' => [
                'description' => 'Account ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'appid' => [
                'description' => 'Application ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'key' => [
                'description' => 'Var key. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'order_by' => [
                'description' => 'order by column',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['vid', 'key', 'appid', 'val'],
                'default' => 'vid',
            ],
            'direction' => [
                'description' => 'Sort direction',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
        ],
    ];

    /**
     * @var VarStoreMapper Var store mapper class.
     */
    private VarStoreMapper $varStoreMapper;

    /**
     * @var ApplicationMapper Application mapper class.
     */
    private ApplicationMapper $applicationMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
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
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);
        $strict = $this->val('strict', true);

        try {
            $params = $this->fetchVars($vid, $accid, $appid, $key, $keyword, $orderBy, $direction);
            if (!is_null($accid) && !is_null(!$appid)) {
                // OR logic.
                $vars = $this->varStoreMapper->findAll($params);
            } else {
                // AND logic.
                $vars = $this->varStoreMapper->findAllFilter($params);
            }
            if ($validateAccess) {
                $vars = $this->filterVarsByPerms($vars, Utilities::getClaimFromToken('roles'));
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($vars) && $strict) {
            throw new ApiException('no results found or permission denied', 6, $this->id, 400);
        }

        $result = [];
        foreach ($vars as $var) {
            $result[] = $var->dump();
        }

        return new DataContainer($result, 'array');
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
     * @param string|null $orderBy
     *   Order by filter.
     * @param string|null $direction
     *   Direction filter.
     *
     * @return array
     */
    protected function fetchVars(
        ?int $vid,
        ?int $accid,
        ?int $appid,
        ?string $key,
        ?string $keyword,
        ?string $orderBy,
        ?string $direction
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
        if (!empty($orderBy)) {
            $params['order_by'] = "`$orderBy`";
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        return $params;
    }

    /**
     * Take an array of variables and return an array of variables the users has permissions to read.
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

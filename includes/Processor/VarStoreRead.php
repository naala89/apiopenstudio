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
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\VarStoreMapper;

/**
 * Class VarStoreRead
 *
 * Processor class to fetch a var-store variable.
 */
class VarStoreRead extends Core\ProcessorEntity
{
    /**
     * Var store mapper class.
     *
     * @var VarStoreMapper
     */
    private VarStoreMapper $varStoreMapper;

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
                'description' => 'If set to true then return null if var does not exist. If set to false throw exception if var does not exist. Default is strict. Only used in fetch or delete operations.',
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
                'default' => '',
            ],
            'appid' => [
                'description' => 'Application ID. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'key' => [
                'description' => 'Var key. If empty, all vars that the user has access to will be returned.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer', 'text'],
                'limitValues' => [],
                'default' => '',
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
     * VarStoreRead constructor.
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

        $validateAccess = $this->val('validate_access', true);
        $vid = $this->val('vid', true);
        $appid = $this->val('appid', true);
        $key = $this->val('key', true);
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);
        $strict = $this->val('strict', true);

        if ($validateAccess) {
            $vars = $this->fetchWithValidation($vid, $appid, $key, $keyword, $orderBy, $direction);
            if (empty($vars) && $strict) {
                throw new Core\ApiException('no results found or permission denied', 6, $this->id, 400);
            }
        } else {
            $vars = $this->fetchWithoutValidation($vid, $appid, $key, $keyword, $orderBy, $direction);
            if (empty($vars) && $strict) {
                throw new Core\ApiException('no results found', 6, $this->id, 400);
            }
        }

        $result = [];
        foreach ($vars as $var) {
            $result[] = $var->dump();
        }

        return new Core\DataContainer($result, 'array');
    }

    /**
     * Fetch all variables for the search without any user role validation.
     *
     * @param $vid
     *   Var ID filter.
     * @param $appid
     *   App ID filter.
     * @param $key
     *   Var name filter.
     * @param $keyword
     *   Var name search filter.
     * @param $orderBy
     *   Order by filter.
     * @param $direction
     *   Direction filter.
     *
     * @return array
     *
     * @throws Core\ApiException
     */
    protected function fetchWithoutValidation($vid, $appid, $key, $keyword, $orderBy, $direction): array
    {
        if (!empty($vid)) {
            try {
                $result = $this->varStoreMapper->findByVid($vid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($result->getVid())) {
                throw new Core\ApiException('no results found or permission denied', 6, $this->id, 400);
            }
            return [$result];
        }
        if (!empty($appid) && !empty($key)) {
            try {
                $result = $this->varStoreMapper->findByAppIdKey($appid, $key);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($result->getVid())) {
                throw new Core\ApiException('no results found or permission denied', 6, $this->id, 400);
            }
            return [$result];
        }
        $params = [];
        if (!empty($appid)) {
            $params['filter'][] = ['value' => (int) $appid, 'column' => '`appid`'];
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

        try {
            $result = $this->varStoreMapper->findAll($params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }

    /**
     * Fetch all variables for the search with user role validation.
     *
     * @param $vid
     *   Var ID filter.
     * @param $appid
     *   App ID filter.
     * @param $key
     *   Var name filter.
     * @param $keyword
     *   Var name search filter.
     * @param $orderBy
     *   Order by filter.
     * @param $direction
     *   Direction filter.
     *
     * @return array
     *
     * @throws Core\ApiException
     */
    protected function fetchWithValidation($vid, $appid, $key, $keyword, $orderBy, $direction): array
    {
        try {
            $uid = Core\Utilities::getUidFromToken();
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($vid)) {
            try {
                $result = $this->varStoreMapper->findByUidVid($uid, $vid);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($result->getVid())) {
                throw new Core\ApiException('no results found or permission denied', 6, $this->id, 400);
            }
            return [$result];
        }
        if (!empty($appid) && !empty($key)) {
            try {
                $result = $this->varStoreMapper->findByUidAppidKey($uid, $appid, $key);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (empty($result->getVid())) {
                throw new Core\ApiException('no results found or permission denied', 6, $this->id, 400);
            }
            return [$result];
        }
        $params = [];
        if (!empty($appid)) {
            $params['filter'][] = ['value' => (int) $appid, 'column' => 'vs.appid'];
        }
        if (!empty($key)) {
            $params['filter'][] = ['value' => $key, 'column' => 'vs.key'];
        }
        if (!empty($keyword)) {
            $params['filter'][] = ['keyword' => "%$keyword%", 'column' => 'vs.key'];
        }
        if (!empty($orderBy)) {
            $params['order_by'] = "vs.$orderBy";
        }
        if (!empty($direction)) {
            $params['direction'] = $direction;
        }

        try {
            $result = $this->varStoreMapper->findByUid($uid, $params);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}

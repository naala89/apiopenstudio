<?php

/**
 * Class VarStoreRead.
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
use ApiOpenStudio\Core;
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
        'description' => 'Fetch a single or multiple var store variables. These will be the variables that belong to the application. If the application is core, then all vars are returned.',
        'menu' => 'Var store',
        'input' => [
            'validate_access' => [
                // phpcs:ignore
                'description' => 'If set to true, the calling users roles access will be validated. If set to false, then access is open.',
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
            'keyword' => [
                'description' => 'Keyword search',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
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
     * @param mixed $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, &$request, ADOConnection $db, Core\MonologWrapper $logger)
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
        $keyword = $this->val('keyword', true);
        $orderBy = $this->val('order_by', true);
        $direction = $this->val('direction', true);

        $params = [];
        if (!empty($vid)) {
            $params['filter'][] = ['keyword' => $vid, 'column' => 'vid`'];
        }
        if (!empty($appid)) {
            $params['filter'][] = ['value' => (int) $appid, 'column' => 'appid'];
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

        if ($validateAccess) {
            // return vars in the applications where the user has required app/role access.
            $vars = $this->varStoreMapper->findByUid(Core\Utilities::getUidFromToken(), $params);
        } else {
            $vars = $this->varStoreMapper->findAll($params);
        }
        $result = [];
        foreach ($vars as $var) {
            $result[] = $var->dump();
        }

        return new Core\DataContainer($result, 'array');
    }
}

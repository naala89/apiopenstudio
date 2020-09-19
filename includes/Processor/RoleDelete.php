<?php
/**
 * Class RoleDelete.
 *
 * @package    Gaterdata
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db\RoleMapper;
use Monolog\Logger;

/**
 * Class RoleDelete
 *
 * Processor class to delete a role.
 */
class RoleDelete extends Core\ProcessorEntity
{
    /**
     * Role mapper class.
     *
     * @var RoleMapper
     */
    private $roleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Role delete',
        'machineName' => 'role_delete',
        'description' => 'Delete a role.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'The ID of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
        ],
    ];

    /**
     * RoleDelete constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
    }

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

        $rid = $this->val('rid', true);

        if ($rid < 6) {
            throw new Core\ApiException("Cannot delete core roles.", 7, $this->id);
        }

        $role = $this->roleMapper->findByRid($rid);
        if (empty($role->getRid())) {
            throw new Core\ApiException("A role with RID: $rid does not exist", 7, $this->id);
        }

        return $this->roleMapper->delete($role);
    }
}

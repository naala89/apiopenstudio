<?php

/**
 * Class RoleDelete.
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
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db\RoleMapper;

/**
 * Class RoleDelete
 *
 * Processor class to delete a role.
 */
class RoleDelete extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Role delete',
        'machineName' => 'role_delete',
        'description' => 'Delete a role.',
        'menu' => 'Admin',
        'input' => [
            'rid' => [
                'description' => 'The ID of the role.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
        ],
    ];

    /**
     * Role mapper class.
     *
     * @var RoleMapper
     */
    private RoleMapper $roleMapper;

    /**
     * @var Config ApiOpenStudio settings.
     */
    private Config $settings;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->roleMapper = new RoleMapper($db, $logger);
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

        $rid = $this->val('rid', true);

        // Update to core application and is locked.
        try {
            $coreLock = $this->settings->__get(['api', 'core_resource_lock']);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if ($coreLock && $rid < 6) {
            throw new ApiException("Unauthorised: this is a core resource", 6, $this->id, 400);
        }

        try {
            $role = $this->roleMapper->findByRid($rid);
            $this->roleMapper->delete($role);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($role->getRid())) {
            throw new ApiException("A role with RID: $rid does not exist", 7, $this->id);
        }

        return new DataContainer(true);
    }
}

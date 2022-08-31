<?php

/**
 * Class ModulesRead.
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
use ApiOpenStudio\Core\ModuleHelper;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;

/**
 * Class ModulesRead
 *
 * Processor class to list all 3rd party modules.
 */
class ModulesRead extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Modules Read',
        'machineName' => 'modules_read',
        'description' => 'List all modules.',
        'menu' => 'Admin',
        'input' => [
            'filter' => [
                'description' => 'Filter the results (all, installed, uninstalled). Empty will return all',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['all', 'installed', 'uninstalled'],
                'default' => 'all',
            ],
        ],
    ];

    /**
     * @var ModuleHelper
     */
    protected ModuleHelper $moduleHelper;

    /**
     * @throws ApiException
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        try {
            $this->moduleHelper = new ModuleHelper($this->db);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
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

        $filter = $this->val('filter', true);
        $result = [];

        switch ($filter) {
            case 'installed':
                $modules = $this->moduleHelper->getInstalled();
                break;
            case 'uninstalled':
                $modules = $this->moduleHelper->getUninstalled();
                break;
            default:
                $modules = $this->moduleHelper->getModules();
                break;
        }

        return new DataContainer($modules);
    }
}

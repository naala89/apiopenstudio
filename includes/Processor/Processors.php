<?php

/**
 * Class Processors.
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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ListClassesInDirectory;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\ProcessorHelper;
use ReflectionClass;
use ReflectionException;

/**
 * Class Processors
 *
 * Processor class to list processors.
 */
class Processors extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Processors',
        'machineName' => 'processors',
        'description' => 'Fetch data on a single or all Processors.',
        'menu' => 'System',
        'input' => [
            'machine_name' => [
                'description' => 'The resource machine_name, "all" or empty for all processors.',
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
     * Array of namespaces for all processors.
     *
     * @var array list of namespaces to fetch.
     */
    protected array $namespaces = [
        'Endpoint',
        'Output',
        'Processor',
        'Security',
    ];

    /**
     * @var ProcessorHelper
     */
    protected ProcessorHelper $processorHelper;

    protected ListClassesInDirectory $listClassesInDirectory;

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
        $this->processorHelper = new ProcessorHelper();
        $this->listClassesInDirectory = new ListClassesInDirectory();
        $machineName = $this->val('machine_name', true);
        $settings = new Config();
        $basePath = $settings->__get(['api', 'base_path']);
        $details = [];

        foreach ($this->namespaces as $namespace) {
            $classNames = $this->listClassesInDirectory->listClassesInDirectory($basePath . 'includes/' . $namespace);
            foreach ($classNames as $className) {
                $detail = $this->getDetails($className);
                if ($detail !== false) {
                    $details[$detail['machineName']] =  $detail;
                }
            }
        }
        sort($details);

        if (empty($machineName) || $machineName == 'all') {
            return new DataContainer($details, 'array');
        }

        $result = [];
        foreach ($details as $detail) {
            if ($detail['machineName'] == $machineName) {
                $result = $detail;
            }
        }

        if (empty($result)) {
            throw new ApiException("Invalid machine name: $machineName", 6, $this->id, 401);
        }

        return new DataContainer($result, 'array');
    }

    /**
     * Return the default details attributed from a class.
     *
     * @param string $className The classname.
     *
     * @return array|false
     *
     * @throws ApiException
     */
    private function getDetails(string $className)
    {
        try {
            $reflector = new ReflectionClass($this->processorHelper->getProcessorString($className));
        } catch (ReflectionException $e) {
            throw new ApiException($e->getMessage(), 6, $this->id);
        }
        if (!$reflector->isAbstract()) {
            $properties = $reflector->getDefaultProperties();
            return $properties['details'] ?? false;
        }

        return false;
    }
}

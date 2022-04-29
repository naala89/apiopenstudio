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

use ApiOpenStudio\Core;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;

/**
 * Class Processors
 *
 * Processor class to list processors.
 */
class Processors extends Core\ProcessorEntity
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
                'description' => 'The resource machine_name or "all" or empty for all processors.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * Array of namespaces for all processors.
     *
     * @var array list of namespaces to fetch.
     */
    private array $namespaces = [
        'Endpoint',
        'Output',
        'Processor',
        'Security',
    ];

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
        $machineName = $this->val('machine_name', true);
        $details = [];

        foreach ($this->namespaces as $namespace) {
            $classNames = $this->getClassList($namespace);
            foreach ($classNames as $className) {
                $detail = $this->getDetails($namespace, $className);
                if ($detail !== false) {
                    $details[$detail['machineName']] =  $detail;
                }
            }
        }
        sort($details);

        if (empty($machineName) || $machineName == 'all') {
            return new Core\DataContainer($details, 'array');
        }

        $result = [];
        foreach ($details as $detail) {
            if ($detail['machineName'] == $machineName) {
                $result = $detail;
            }
        }

        if (empty($result)) {
            throw new Core\ApiException("Invalid machine name: $machineName", 6, $this->id, 401);
        }

        return new Core\DataContainer($result, 'array');
    }

    /**
     * Get a list of classes from a directory.
     *
     * @param string $namespace The subdirectory/namespace.
     *
     * @return array The list of class names.
     */
    private function getClassList(string $namespace): array
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/../' . $namespace));
        $objects = new RegexIterator($iterator, '/[a-z0-9]+\.php/i', RegexIterator::GET_MATCH);
        $result = [];
        foreach ($objects as $name => $object) {
            preg_match('/([a-zA-Z0-9]+)\.php$/i', $name, $className);
            $result[] = $className[1];
        }
        return $result;
    }

    /**
     * Return the default details attributed from a class.
     *
     * @param string $namespace The namespace that the class belongs to.
     * @param string $className The classname.
     *
     * @return array|false
     */
    private function getDetails(string $namespace, string $className)
    {
        $reflector = new ReflectionClass("\\ApiOpenStudio\\$namespace\\$className");
        if (!$reflector->isAbstract()) {
            $properties = $reflector->getDefaultProperties();
            return $properties['details'];
        }

        return false;
    }
}

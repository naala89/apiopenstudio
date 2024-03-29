<?php

/**
 * Class ProcessorHelper.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

/**
 * Class ProcessorHelper
 *
 * Helper class for ApiOpenStudio Processors.
 */
class ProcessorHelper
{
    /**
     * List of namespaces to search for processors.
     *
     * @var string[]
     */
    private array $namespaces = [
        'Security',
        'Endpoint',
        'Output',
        'Processor',
    ];

    /**
     * Return processor namespace and class name string.
     *
     * @param string $className Class name of processor.
     * @param array|null $namespaces Namespaces to search.
     *
     * @return string Class string.
     *
     * @throws ApiException Unknown Processor.
     */
    public function getProcessorString(string $className, array $namespaces = null): string
    {
        if (empty($className)) {
            throw new ApiException('empty processor name', 1, -1, 500);
        }
        if (empty($namespaces) && strpos($className, "\\") !== false) {
            return $className;
        }
        $namespaces = empty($namespaces) ? $this->namespaces : $namespaces;
        $className = str_replace('-', '_', $className);
        $parts = explode('_', $className);
        foreach ($parts as $key => $part) {
            $parts[$key] = ucfirst($part);
        }
        $className = implode('', $parts);

        foreach ($namespaces as $namespace) {
            $classStr = "\\ApiOpenStudio\\$namespace\\$className";
            if (class_exists($classStr)) {
                return $classStr;
            }
        }

        throw new ApiException("unknown processor: $className", 1, -1, 500);
    }

    /**
     * Validate whether an object or array is a processor.
     *
     * @param mixed $obj Object to test.
     *
     * @return boolean
     */
    public function isProcessor($obj): bool
    {
        if (is_array($obj) && isset($obj['processor']) && isset($obj['id'])) {
            return true;
        }
        return false;
    }
}

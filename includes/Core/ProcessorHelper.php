<?php
/**
 * Class ProcessorHelper.
 *
 * @package Gaterdata
 * @subpackage Core
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Core;

/**
 * Class ProcessorHelper
 *
 * Helper class for GaterData Processors.
 */
class ProcessorHelper
{
    /**
     * @var string[]
     *
     * List of namespaces to search for processors.
     */
    private $_namespaces = array('Security', 'Endpoint', 'Output', 'Processor', 'Core');

    /**
     * Return processor namespace and class name string.
     *
     * @param string $className Class name of processor.
     * @param array $namespaces Namepsaces to search.
     *
     * @return string Class string.
     *
     * @throws ApiException Unknown Processor/function.
     */
    public function getProcessorString(string $className, array $namespaces = null)
    {
        if (empty($className)) {
            throw new ApiException('empty function name', 1, -1, 406);
        }
        $namespaces = empty($namespaces) ? $this->_namespaces : $namespaces;
        $className = str_replace('-', '_', $className);
        $parts = explode('_', $className);
        foreach ($parts as $key => $part) {
            $parts[$key] = ucfirst($part);
        }
        $className = implode('', $parts);

        foreach ($namespaces as $namespace) {
            $classStr = "\\Gaterdata\\$namespace\\$className";
            if (class_exists($classStr)) {
                return $classStr;
                break;
            }
        }

        throw new ApiException("unknown function: $className", 1, -1, 406);
    }

    /**
     * Validate whether an object or array is a processor.
     *
     * @param mixed $obj Object to test.
     *
     * @return boolean
     */
    public function isProcessor(&$obj)
    {
        if (is_object($obj)) {
            return (isset($obj->function) && isset($obj->id));
        }
        if (is_array($obj)) {
            return (isset($obj['function']) && isset($obj['id']));
        }
        return false;
    }
}

<?php
/**
 * Class Merge.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class Mapper
 *
 * Processor class te merge multiple data sets.
 */
class Merge extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Merge',
        'machineName' => 'merge',
        'description' => 'Merge multiple data-sets.',
        'menu' => 'Data operation',
        'input' => [
          'sources' => [
            'description' => 'The data-sets to be merged.',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'mergeType' => [
            'description' => 'The merge operation to perform.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => ['text'],
            'limitValues' => ['union', 'intersect', 'difference'],
            'default' => 'union',
          ],
          'unique' => [
            'description' => 'Disallow duplicate values.',
            'cardinality' => [0, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => ['boolean'],
            'limitValues' => [],
            'default' => false,
          ],
        ],
    ];

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

        $sources = $this->val('sources', true);
        $unique = $this->val('unique', true);
        $mergeType = $this->val('mergeType', true);
        $method = '_' . strtolower(trim($mergeType));

        if (!method_exists($this, $method)) {
            throw new Core\ApiException("invalid mergeType: $mergeType", 6, $this->id, 407);
        }

        if ($unique === true) {
            return array_unique($this->$method($sources));
        }
        return $this->$method($sources);
    }

    /**
     * Union of arrays.
     *
     * @param array $values Data sets to merge.
     *
     * @return array|mixed
     */
    private function _union(array $values)
    {
        $result = array_shift($values);
        $result = is_array($result) ? $result : array($result);
        foreach ($values as $value) {
            $value = is_array($value) ? $value : array($value);
            $result = array_merge($result, $value);
        }
        return $result;
    }

    /**
     * Union of arrays.
     *
     * @param array $values Data sets to intersect.
     *
     * @return array|mixed
     */
    private function _intersect(array $values)
    {
        $result = array_shift($values);
        $result = is_array($result) ? $result : array($result);
        foreach ($values as $value) {
            $value = is_array($value) ? $value : array($value);
            $result = array_intersect($result, $value);
        }
        return $result;
    }

    /**
     * Outer join of arrays.
     *
     * @param array $values Data sets to outer join.
     *
     * @return array|mixed
     */
    private function _difference(array $values)
    {
        $result = array_shift($values);
        $result = is_array($result) ? $result : array($result);
        foreach ($values as $value) {
            $value = is_array($value) ? $value : array($value);
            $result = array_merge(array_diff($result, $value), array_diff($value, $result));
        }
        return $result;
    }
}

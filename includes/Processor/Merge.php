<?php

/**
 * Class Merge.
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

use ApiOpenStudio\Core;

/**
 * Class Mapper
 *
 * Processor class te merge multiple data sets.
 */
class Merge extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Merge',
        'machineName' => 'merge',
        'description' => 'Merge multiple data-sets.',
        'menu' => 'Data operation',
        'input' => [
          'sources' => [
            'description' => 'The data-sets to be merged.',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitProcessors' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'mergeType' => [
            'description' => 'The merge operation to perform.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitProcessors' => [],
            'limitTypes' => ['text'],
            'limitValues' => ['union', 'intersect', 'difference'],
            'default' => 'union',
          ],
          'unique' => [
            'description' => 'Disallow duplicate values.',
            'cardinality' => [0, 1],
            'literalAllowed' => true,
            'limitProcessors' => [],
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $sources = $this->val('sources', true);
        $unique = $this->val('unique', true);
        $mergeType = $this->val('mergeType', true);
        $method = strtolower(trim($mergeType));

        if (!method_exists($this, $method)) {
            throw new Core\ApiException("invalid mergeType: $mergeType", 6, $this->id, 407);
        }

        if ($unique === true) {
            return new Core\DataContainer(array_unique($this->$method($sources)), 'array');
        }

        return new Core\DataContainer($this->$method($sources), 'array');
    }

    /**
     * Union of arrays.
     *
     * @param array $values Data sets to merge.
     *
     * @return array
     */
    private function union(array $values): array
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
    private function intersect(array $values)
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
     * @return array
     */
    private function difference(array $values): array
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

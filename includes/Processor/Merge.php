<?php

/**
 * Class Merge.
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

use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ApiException;

/**
 * Class Mapper
 *
 * Processor class te merge multiple data sets.
 */
class Merge extends ProcessorEntity
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
                'default' => null,
            ],
            'merge_type' => [
                'description' => 'The merge operation to perform.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['union', 'intersect', 'difference'],
                'default' => 'union',
            ],
            'unique' => [
                'description' => 'Remove duplicate values.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'reset_keys' => [
                // phpcs:ignore
                'description' => 'Reset the result array keys. This will only apply when using numeric keys in the source arrays, after removing duplicates',
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $sources = $this->val('sources', true);
        $unique = $this->val('unique', true);
        $resetKeys = $this->val('reset_keys', true);
        $mergeType = $this->val('merge_type', true);
        $mergeType = strtolower(trim($mergeType));

        if (!method_exists($this, $mergeType)) {
            throw new ApiException("invalid mergeType: $mergeType", 6, $this->id, 400);
        }

        $result = $this->$mergeType($sources);
        if ($unique) {
            $result = array_unique($result);
        }
        if ($resetKeys) {
            $result = array_values($result);
        }

        try {
            $result = new DataContainer($result, 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
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

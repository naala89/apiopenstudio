<?php

/**
 * Class Filter.
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
use Closure;

/**
 * Class Filter
 *
 * Processor class to perform a filter operation.
 */
class Filter extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Filter',
        'machineName' => 'filter',
        'description' => 'Filter values from a data-set.',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The data-set to filter.',
                'cardinality' => [0, '*'],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'filter' => [
                'description' => 'The literal values to filter out.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'array'],
                'limitValues' => [],
                'default' => '',
            ],
            'regex' => [
                // phpcs:ignore
                'description' => 'If set ot true, use the filter string as a regex. If set to false, use the filter string for exact comparison.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'keyOrValue' => [
                'description' => 'Filter by key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['key', 'value'],
                'default' => 'key',
            ],
            'recursive' => [
                // phpcs:ignore
                'description' => 'Recursively filter the data set. If set to false, the filter will only apply to the outer data-set. If set to true, the filter will apply to the entire data-set (warning: use sparingly, this could incur long processing times).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'inverse' => [
                // phpcs:ignore
                'description' => 'If set to true, the filter will keep matching data. If set to false, the filter will only keep non-matching data.',
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

        $filter = $this->val('filter', true);
        $keyOrValue = $this->val('keyOrValue', true);
        $recursive = $this->val('recursive', true);
        $inverse = $this->val('inverse', true);
        $regex = $this->val('regex', true);
        $source = $this->val('source', true);

        // Nothing to filter.
        if (empty($source)) {
            return new Core\DataContainer($source);
        }

        // Test for multiple filters if regex (not allowed because it is inefficient).
        if ($regex === true && is_array($filter)) {
            throw new Core\ApiException('cannot have an array of regexes as a filter', 0, $this->id, 417);
        }

        // Regex filter accepted as a string, convert to array so it is always an array
        if (!$regex && !is_array($filter)) {
            $filter = array($filter);
        }

        $func = 'filterBy' . ucfirst($keyOrValue) . ($recursive ? 'Recursive' : 'Nonrecursive');
        $getCallback = 'callback' . ($inverse ? 'Inverse' : 'Noninverse') . ($regex ? 'Regex' : 'Nonregex');
        $callback = $this->{$getCallback}($filter);

        $result = $this->{$func}($source, $callback);

        return new Core\DataContainer($result);
    }

    /**
     * Perform non-recursive filter on $data, based on key value.
     *
     * @param mixed $data Data to filter.
     * @param mixed $callback Callback function.
     *
     * @return array
     *
     * @see https://wpscholar.com/blog/filter-multidimensional-array-php/
     * @see http://www.phptherightway.com/pages/Functional-Programming.html
     */
    private function filterByKeyNonrecursive($data, $callback): array
    {
        if (!is_array($data)) {
            return $data;
        }

        return array_filter($data, $callback, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Perform recursive filter on $data, based on key value.
     *
     * @param mixed $data Data to filter.
     * @param mixed $callback Callback function.
     *
     * @return array
     */
    private function filterByKeyRecursive($data, $callback): array
    {
        if (!is_array($data)) {
            return $data;
        }

        $data = array_filter($data, $callback, ARRAY_FILTER_USE_KEY);

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $data[$key] = $this->filterByKeyRecursive($item, $callback);
            }
        }

        return $data;
    }

    /**
     * Perform non-recursive filter on $data, based on value.
     *
     * @param mixed $data Data to filter.
     * @param mixed $callback Callback function.
     *
     * @return array|null
     */
    private function filterByValueNonrecursive($data, $callback): ?array
    {
        if (!is_array($data)) {
            return !$callback($data) ? null : $data;
        }

        foreach ($data as $key => $item) {
            if (!is_array($item) && !$callback($item)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Perform non-recursive filter on $data, based on value.
     *
     * @param mixed $data Data to filter.
     * @param mixed $callback Callback function.
     *
     * @return array|null
     */
    private function filterByValueRecursive($data, $callback): ?array
    {
        if (!is_array($data)) {
            return $callback($data) ? null : $data;
        }

        foreach ($data as $key => $item) {
            if (!is_array($item)) {
                if (!$callback($item)) {
                    unset($data[$key]);
                }
            } else {
                $data[$key] = $this->filterByValueRecursive($item, $callback);
            }
        }

        return $data;
    }

    /**
     * Filter callback for non-inverse, non-regex.
     *
     * @param array $filter Filter regex.
     *
     * @return Closure
     */
    private function callbackNoninverseNonregex(array $filter): Closure
    {
        return function ($item) use ($filter) {
            return !in_array($item, $filter);
        };
    }

    /**
     * Filter callback for inverse, non-regex.
     *
     * @param array $filter Filter regex.
     *
     * @return Closure
     */
    private function callbackInverseNonregex(array $filter): Closure
    {
        return function ($item) use ($filter) {
            return in_array($item, $filter);
        };
    }

    /**
     * Filter callback for non-inverse, regex.
     *
     * @param array $filter Filter regex.
     *
     * @return Closure
     */
    private function callbackNoninverseRegex(array $filter): Closure
    {
        return function ($item) use ($filter) {
            return preg_match($filter, $item) == 0;
        };
    }

    /**
     * Filter callback for inverse, regex.
     *
     * @param array $filter Filter regex.
     *
     * @return Closure
     */
    private function callbackInverseRegex(array $filter): Closure
    {
        return function ($item) use ($filter) {
            return preg_match($filter, $item) > 0;
        };
    }
}

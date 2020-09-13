<?php
/**
 * Class Filter.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class Filter
 *
 * Processor class to perform a filter operation.
 */
class Filter extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Filter',
        'machineName' => 'filter',
        'description' => 'Filter values from a data-set.',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The data-set to filter.',
                'cardinality' => [0, '*'],
                'literalAllowed' => false,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'filter' => [
                'description' => 'The literal values to filter out.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'array'],
                'limitValues' => [],
                'default' => '',
            ],
            'regex' => [
                // phpcs:ignore
                'description' => 'If set ot true, use the filter string as a regex. If set to false, use the filter string for exact comparison.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'keyOrValue' => [
                'description' => 'Filter by key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['key', 'value'],
                'default' => 'key',
            ],
            'recursive' => [
                // phpcs:ignore
                'description' => 'Recursively filter the data set. If set to false, the filter will only apply to the outer data-set. If set to true, the filter will apply to the entire data-set (warning: use sparingly, this could incur long processing times).',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
            'inverse' => [
                // phpcs:ignore
                'description' => 'If set to true, the filter will keep matching data. If set to false, the filter will only keep non-matching data.',
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

        $func = '_filterBy' . ucfirst($keyOrValue) . ($recursive ? 'Recursive' : 'Nonrecursive');
        $getCallback = '_callback' . ($inverse ? 'Inverse' : 'Noninverse') . ($regex ? 'Regex' : 'Nonregex');
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
    private function _filterByKeyNonrecursive($data, $callback)
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
    private function _filterByKeyRecursive($data, $callback)
    {
        if (!is_array($data)) {
            return $data;
        }

        $data = array_filter($data, $callback, ARRAY_FILTER_USE_KEY);

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $data[$key] = $this->_filterByKeyRecursive($item, $callback);
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
     * @return array
     */
    private function _filterByValueNonrecursive($data, $callback)
    {
        if (!is_array($data)) {
            return !$callback($data) ? null : $data;
        }

        foreach ($data as $key => $item) {
            var_dump($key);
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
     * @return array
     */
    private function _filterByValueRecursive($data, $callback)
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
                $data[$key] = $this->_filterByValueRecursive($item, $callback);
            }
        }

        return $data;
    }

    /**
     * Filter callback for non-inverse, non-regex.
     *
     * @param array $filter Filter regex.
     *
     * @return \Closure
     */
    private function _callbackNoninverseNonregex(array $filter)
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
     * @return \Closure
     */
    private function _callbackInverseNonregex(array $filter)
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
     * @return \Closure
     */
    private function _callbackNoninverseRegex(array $filter)
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
     * @return \Closure
     */
    private function _callbackInverseRegex(array $filter)
    {
        return function ($item) use ($filter) {
            return preg_match($filter, $item) > 0;
        };
    }
}

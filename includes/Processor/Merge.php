<?php

/**
 * Perform merge of multiple sources.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class Merge extends Core\ProcessorEntity
{
    /**
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
     * @param $values
     * @return array|mixed
     */
    private function _union($values)
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
     * @param $values
     * @return array|mixed
     */
    private function _intersect($values)
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
     * @param $values
     * @return array|mixed
     */
    private function _difference($values)
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

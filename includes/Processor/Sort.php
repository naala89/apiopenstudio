<?php

/**
 * Sort logic gate.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class Sort extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Sort',
        'machineName' => 'sort',
        // phpcs:ignore
        'description' => 'Sort an input of multiple values. The values can be singular items or name/value pairs (sorted by key or value). Singular items cannot be mixed with name/value pairs.',
        'menu' => 'Data operation',
        'input' => [
            'values' => [
                'description' => 'The values to sort.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort ascending or descending.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'sortBy' => [
                'description' => 'Perform the sort on key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => ['key', 'value'],
                'default' => 'key',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $values = $this->val('values', true);

        if (empty($values) || !is_array($values)) {
            return $values;
        }

        $direction = $this->val('direction', true);
        $sortBy = $this->val('sortBy', true);

        Core\Debug::variable($values, 'values before sort');

        if ($sortBy == 'key') {
            if ($direction == 'asc') {
                if (!Core\Utilities::isAssoc($values)) {
                    // do nothing, this is a normal array
                } else {
                    ksort($values);
                }
            } else {
                if (!Core\Utilities::isAssoc($values)) {
                    $values = array_reverse($values);
                } else {
                    krsort($values);
                }
            }
        } else {
            if ($direction == 'asc') {
                if (!Core\Utilities::isAssoc($values)) {
                    sort($values);
                } else {
                    asort($values);
                }
            } else {
                if (!Core\Utilities::isAssoc($values)) {
                    rsort($values);
                } else {
                    arsort($values);
                }
            }
        }

        Core\Debug::variable($values, 'values after sort');

        return $values;
    }
}

<?php

/**
 * Simple field type.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class FieldValue extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Field value',
        'machineName' => 'field_value',
        'description' => 'Returns the key oy value from a field.',
        'menu' => 'Data operation',
        'input' => [
            'field' => [
                'description' => 'The input field.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => ['var_field'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'key_value' => [
                'description' => 'Return the key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => ['key', 'value'],
                'default' => 'value',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $field = $this->val('field', true);
        $keyValue = $this->val('key_value', true);

        $keys = array_keys($field);

        return new Core\DataContainer($keyValue == 'value' ? $field[$keys[0]] : $keys[0]);
    }
}

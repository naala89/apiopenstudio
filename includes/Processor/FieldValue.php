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
        'menu' => 'Primitive',
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
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $field = $this->val('field', true);
        $keyValue = $this->val('key_value', true);

        $keys = array_keys($field);
        $result = $keyValue == 'value' ? $field[$keys[0]] : $keys[0];

        return new Core\DataContainer($result, $this->detectType($result));
    }
}

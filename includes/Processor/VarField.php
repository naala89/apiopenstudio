<?php

/**
 * Simple field type.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class VarField extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (field)',
        'machineName' => 'var_field',
        'description' => 'Create a name value pair. This is primarily for use as a field in object. individual key/values can be input or a whole array. ',
        'menu' => 'Primitive',
        'input' => [
            'key' => [
                'description' => 'The key of the field name/value pair.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['string', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'value' => [
                'description' => 'The value of the field name/value pair.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'array' => [
                'description' => 'Array to be converted to a field. This can only have one index.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => [],
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $key = $this->val('key', true);
        $value = $this->val('value', true);
        $array = $this->val('array', true);

        if (!empty($array)) {
            if (sizeof($array) > 1) {
                throw new Core\ApiException('Cannot have more than one index in an input array.', 0, $this->id, 417);
            }
            $keys = array_keys($array);
            return new Core\DataContainer([$keys[0] => $array[$keys[0]]], 'array');
        }

        if (empty($key) || empty($value)) {
            throw new Core\ApiException('Empty array, and key or value.', 0, $this->id, 417);
        }
        return new Core\DataContainer([$key => $value], 'array');
    }
}

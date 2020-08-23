<?php

/**
 * Variable type boolean.
 *
 * This is a special case, we cannot use val(), because it validates type before it can be cast.
 * thus get vars, etc will always fail.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class VarBool extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Boolean)',
        'machineName' => 'var_bool',
        'description' => 'A boolean variable. It validates the input (0,1,yes,no,true,false) into a boolean value, and \
        returns an error if it is not a boolean.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the variable.',
                'cardinality' => [1, 1],
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

        $value = $this->val('value', true);
        switch ($value) {
            case 'yes':
            case 1:
            case 'true':
                $value = true;
                break;
            case 'no':
            case 0:
            case 'false':
                $value = false;
                break;
        }
        $boolean = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($boolean)) {
            throw new Core\ApiException("$value is not boolean", 7, $this->id);
        }

        return new Core\DataContainer($boolean, 'boolean');
    }
}

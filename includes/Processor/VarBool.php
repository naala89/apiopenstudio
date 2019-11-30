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
    'description' => 'A boolean variable. It validates the input (0,1,yes,no,true,false) into a boolean value, and returns an error if it is not a boolean.',
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
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $result = $this->val('value');
        if (!$this->isDataContainer($result)) {
            $result = new Core\DataContainer($result, 'boolean');
        }
        $boolean = filter_var($result->getData(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($boolean)) {
            throw new Core\ApiException($result->getData() . ' is not boolean', 0, $this->id);
        }
        $result->setData($boolean);
        $result->setType('boolean');
        return $result;
    }
}

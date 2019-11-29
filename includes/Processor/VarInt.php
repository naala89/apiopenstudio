<?php

/**
 * variable type integer
 *
 * This is a special case, we cannot use val(), because it validates type before it can be cast.
 * thus get vars, etc will always fail.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarInt extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Var (Integer)',
    'machineName' => 'var_int',
    'description' => 'An integer variable. It validates the input and returns an error if it is not a integer.',
    'menu' => 'Primitive',
    'input' => [
      'value' => [
        'description' => 'The value of the variable.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => '',
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
      $result = new Core\DataContainer($result, 'integer');
    }
    $integer = filter_var($result->getData(), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    if (is_null($integer)) {
      throw new Core\ApiException($result->getData() . ' is not integer', 0, $this->id);
    }
    $result->setData($integer);
    $result->setType('integer');
    return $result;
  }
}

<?php

/**
 * Variable type random.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarRand extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Var (Rand)',
    'machineName' => 'var_rand',
    'description' => 'A random variable. It produces a random variable of any specified length or mix of character types.',
    'menu' => 'Primitive',
    'input' => [
      'length' => [
        'description' => 'The length of the variable.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => 8,
      ],
      'lower' => [
        'description' => 'Use lower-case alpha characters.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true,
      ],
      'upper' => [
        'description' => 'Use upper-case alpha characters.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true,
      ],
      'numeric' => [
        'description' => 'Use numeric characters.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true,
      ],
      'special' => [
        'description' => 'Use special characters.',
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
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $length = $this->val('length', true);
    $lower = $this->val('lower', true);
    $upper = $this->val('upper', true);
    $numeric = $this->val('numeric', true);
    $special = $this->val('special', true);

    return new Core\DataContainer(Core\Utilities::random_string($length, $lower, $upper, $numeric, $special), 'text');
  }
}

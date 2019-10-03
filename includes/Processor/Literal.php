<?php

/**
 * Literal value.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class Literal extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Literal',
    'machineName' => 'literal',
    'description' => 'A literal string or value.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => [
      'value' => [
        'description' => 'The value of the literal.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => ''
      ],
      'type' => [
        'description' => 'The literal type.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => 'text'
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $value = $this->val('value');
    $type = $this->val('type');

    return new Core\DataContainer($value, $type);
  }
}

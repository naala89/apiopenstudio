<?php

/**
 * Literal value.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class Literal extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Literal',
    'machineName' => 'literal',
    'description' => 'A literal string or value. This accepts only string or number values',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the literal.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string', 'integer', 'float'),
        'limitValues' => array(),
        'default' => ''
      ),
      'type' => array(
        'description' => 'The literal type.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array('text', 'xml', 'json', 'csv'),
        'default' => 'text'
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Literal', 4);

    $value = $this->val('value');
    $type = $this->val('type');

    return new Core\DataContainer($value, $type);
  }
}

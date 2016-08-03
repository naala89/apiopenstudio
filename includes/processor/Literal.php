<?php

/**
 * Literal value.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Literal extends ProcessorEntity {

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

    $value = $this->meta->value;
    if (!is_string($value) && !is_numeric($value)) {
      throw new Core\ApiException('Invalid literal value', 6, $this->id, 307);
    }

    $type = !empty($this->meta->type) ? $this->meta->type : $this->details['input']['type']['default'];
    if (!in_array($type, $this->details['input']['type']['limitValues'])) {
      throw new Core\ApiException('Invalid type value', 6, $this->id, 307);
    }

    $className = ucfirst(trim($type));
    $classStr = "\\Datagator\\Core\\$className";
    return new $classStr($value);
  }
}

<?php

/**
 * Variable type string.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarStr extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (String)',
    'machineName' => 'var_str',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'value' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $result = $this->val('value');
    if (!$this->isDataContainer($result)) {
      $result = new Core\DataContainer($result, 'text');
    }
    $string = $result->getData();
    if (!is_string($string)) {
      throw new Core\ApiException($result->getData() . ' is not text', 0, $this->id);
    }
    $result->setType('text');
    return $result;
  }
}

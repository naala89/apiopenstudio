<?php

/**
 * URI variable
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarUri extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (URI)',
    'machineName' => 'varUri',
    'description' => 'A value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'index' => array(
        'description' => 'The index of the variable, starting with 0 after the client ID, request noun and verb.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => 0
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarUri', 4);
    $index = $this->val('index');
    $args = $this->request->getArgs();

    if (!isset($args[$index])) {
      throw new Core\ApiException('URI index "' . $index . '" does not exist', 6, $this->id, 417);
    }

    return urldecode($args[intval($index)]);
  }
}

<?php

/**
 * Get variable.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarGet extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Get)',
    'machineName' => 'varGet',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nullable' => array(
        'description' => 'Allow the processing to continue if the post variable does not exist.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => false
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarGet', 4);

    $name = $this->val('name');
    $nullable = $this->val('nullable');
    $vars = $this->request->getGetVars();

    if (isset($vars[$name])) {
      return $vars[$name];
    }
    if ($nullable) {
      return '';
    }
    throw new Core\ApiException("post variable ($name) not received", 5, $this->id, 417);
  }
}

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
        'description' => 'Throw exception if the post variable does not exist.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarGet', 4);

    $name = $this->val('name');
    $vars = $this->request->getGetVars();

    if (isset($vars[$name])) {
      return new Core\Text($vars[$name]);
    }
    if ($this->val('nullable')) {
      return new Core\Text('');
    }
    throw new Core\ApiException("post var $name not available", 1, $this->id);
  }
}

<?php

/**
 * Get variable.
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarGet extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Get)',
    'machineName' => 'varGet',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The key or name of the GET variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nullable' => array(
        'description' => 'Allow the processing to continue if the GET variable does not exist.',
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

    $name = $this->val('name', true);
    $vars = $this->request->getGetVars();
    
    if (isset($vars[$name])) {
      return new Core\DataContainer($vars[$name], 'text');
    }
    if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
      return new Core\DataContainer('', 'text');
    }

    throw new Core\ApiException("get variable ($name) not received", 5, $this->id, 417);
  }
}

<?php

/**
 * Get variable.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarGet extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Get)',
    'machineName' => 'var_get',
    'description' => 'A "get" variable. It fetches a urldecoded variable from the get request.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'key' => array(
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

    $key = $this->val('key', true);
    $vars = $this->request->getGetVars();
    
    if (isset($vars[$key])) {
      return new Core\DataContainer(urldecode($vars[$key]), 'text');
    }
    if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
      return new Core\DataContainer('', 'text');
    }

    throw new Core\ApiException("GET variable ($key) not received", 5, $this->id, 417);
  }
}

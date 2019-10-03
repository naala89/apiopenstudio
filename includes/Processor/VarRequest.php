<?php

/**
 * Request variable
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarRequest extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Request)',
    'machineName' => 'var_request',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'key' => array(
        'description' => 'The key or name of the GET/POST variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nullable' => array(
        'description' => 'Allow the processing to continue if the GET or POST variable does not exist.',
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
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $key = $this->val('key', true);
    $vars = array_merge($this->request->getGetVars(), $this->request->getPostVars());

    if (isset($vars[$key])) {
      return new Core\DataContainer($vars[$key], 'text');
    }
    if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
      return new Core\DataContainer('', 'text');
    }
    throw new Core\ApiException("request var $key not available", 1, $this->id);
  }
}

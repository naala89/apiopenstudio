<?php

/**
 * Request variable
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarRequest extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Request)',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'string')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarRequest', 4);

    $name = $this->val($this->meta->name);
    $vars = array_merge($this->request->getGetVars(), $this->request->getPostVars());

    if (isset($vars[$name])) {
      return $vars[$name];
    }

    return null;
  }
}

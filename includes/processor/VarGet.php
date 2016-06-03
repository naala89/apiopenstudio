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
    'application' => 'All',
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
    Core\Debug::variable($this->meta, 'Processor VarGet', 4);
    $name = $this->val($this->meta->name);

    if (isset($_GET[$name])) {
      return $_GET[$name];
    }

    return null;
  }
}

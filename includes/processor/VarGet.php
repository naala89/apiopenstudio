<?php

/**
 * Get variable
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
        'accepts' => array('processor', 'string')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarGet');
    $name = $this->val($this->meta->name);

    if (isset($this->request->vars[$name])) {
      return $this->request->vars[$name];
    }

    return null;
  }
}

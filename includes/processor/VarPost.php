<?php

/**
 * Post variable
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarPost extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Post)',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
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
    Core\Debug::variable($this->meta, 'Processor VarPost', 4);
    $name = $this->val($this->meta->name);

    if (isset($_POST[$name])) {
      return $_POST[$name];
    }

    return null;
  }
}

<?php

/**
 * Get variable
 *
 * METADATA
 * {
 *    "type":"val",
 *    "meta":{
 *      "id":<integer>,
 *      "var":<processor|literal>,
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarGet extends VarMixed
{
  public $details = array(
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

    if (empty($this->request->vars[$name])) {
      throw new Core\ApiException("get variable ($name) does not exist", 6, $this->id, 417);
    }

    return $this->request->vars[$name];
  }
}

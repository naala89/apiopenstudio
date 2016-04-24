<?php

/**
 * Request variable
 *
 * METADATA
 * {
 *    "type":"requestVar",
 *    "meta":{
 *      "var":<processor|mixed>,
 *    }
 *  }
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarRequest extends VarMixed
{
  public $details = array(
    'name' => 'Var (Request)',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarRequest');
    $name = $this->val($this->meta->name);

    if (empty($_REQUEST[$name])) {
      throw new Core\ApiException("request variable ($name) does not exist", 6, $this->id, 417);
    }

    return $_REQUEST[$name];
  }
}

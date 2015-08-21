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

namespace Datagator\Processors;
use Datagator\Core;

class VarRequest extends VarMixed
{
  protected $required = array('name');
  protected $details = array(
    'name' => 'Var (Request)',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'variables',
    'client' => 'all',
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
    Core\Debug::variable($this->meta, 'ProcessorVarRequest');
    $name = parent::process();

    if (empty($_REQUEST[$name])) {
      throw new Core\ApiException("request variable ($name) does not exist", 5, $this->id, 417);
    }

    return $_REQUEST[$name];
  }
}

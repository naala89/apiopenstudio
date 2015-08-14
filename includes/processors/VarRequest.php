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
  protected $details = array(
    'name' => 'Var (Request)',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'variables',
    'client' => 'all',
    'input' => array(
      'var' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'ProcessorVarRequest');
    $var = parent::process();

    if (empty($_REQUEST[$var])) {
      throw new Core\ApiException("request variable ($var) does not exist", 5, $this->id, 417);
    }

    return $_REQUEST[$var];
  }
}

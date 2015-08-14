<?php

/**
 * Get variable
 *
 * METADATA
 * {
 *    "type":"getVar",
 *    "meta":{
 *      "id":<integer>,
 *      "var":<processor|literal>,
 *    }
 *  }
 */

namespace Datagator\Processors;
use Datagator\Core;

class VarGet extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
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
    Core\Debug::variable($this->meta, 'Processor VarGet');
    $varName = parent::process();

    if (empty($this->request->vars[$varName])) {
      throw new Core\ApiException("get variable ($varName) does not exist", 5, $this->id, 417);
    }

    return $this->request->vars[$varName];
  }
}

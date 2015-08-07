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

class VarGet extends ProcessorBase
{
  protected $required = array('var');
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
    'menu' => 'variables',
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
    Core\Debug::message('Processor VarGet');
    $varName = $this->getVar($this->meta->var);

    if (empty($this->request->vars[$varName])) {
      throw new Core\ApiException("get variable ($varName) does not exist", 5, $this->id, 417);
    }

    return $this->request->vars[$varName];
  }
}

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

class VariableRequest extends \Processor
{
  protected $details = array(
    'name' => 'Var (Request)',
    'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'mixed')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarRequest');
    $varName = parent::process();

    if (empty($_REQUEST[$varName])) {
      throw new \Datagator\includes\ApiException("request variable ($varName) does not exist", 5, $this->id, 417);
    } else {
      $result = $_REQUEST[$varName];
    }

    return $result;
  }
}
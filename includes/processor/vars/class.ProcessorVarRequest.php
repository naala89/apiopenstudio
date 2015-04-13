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

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarRequest extends ProcessorVar
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
    if ($this->status != 200) {
      return $varName;
    }

    if (empty($_REQUEST[$varName])) {
      throw new ApiException("request variable ($varName) does not exist", 5, $this->id, 417);
    } else {
      $result = $_REQUEST[$varName];
    }

    return $result;
  }
}

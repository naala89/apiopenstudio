<?php

/**
 * Variable type string
 *
 * METADATA
 * {
 *    "type":"string",
 *    "meta":{
 *      "var":<processor|string>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarStr extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (String)',
    'description' => 'A string variable. It validates the input and returns an error if it is not a string.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'str')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarStr', 4);
    $result = parent::process();

    if (!is_string($result)) {
      $this->status = 417;
      $result = new Error(5, $this->id, 'invalid string');
    }

    return $result;
  }
}

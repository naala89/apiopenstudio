<?php

/**
 * Variable type number
 *
 * METADATA
 * {
 *    "type":"varNum",
 *    "meta":{
 *      "var":<processor|number>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarNum extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Number)',
    'description' => 'A number variable. It validates the input and returns an error if it is not a real number.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'num')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarNum');
    $result = parent::process();

    if (!is_numeric($result)) {
      $this->status = 417;
      $result = new Error(5, $this->id, 'invalid number');
    }

    return $result;
  }
}

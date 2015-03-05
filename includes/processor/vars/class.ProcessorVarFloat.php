<?php

/**
 * Variable type float
 *
 * METADATA
 * {
 *    "type":"varFloat",
 *    "meta":{
 *      "var":<processor|float>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarFloat extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Float)',
    'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'float')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarFloat');
    $result = parent::process();

    if ($this->status == 200 && !is_float($result)) {
      $this->status = 417;
      $result = new Error(5, $this->id, 'invalid float');
    }

    return $result;
  }
}

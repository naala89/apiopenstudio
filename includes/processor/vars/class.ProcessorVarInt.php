<?php

/**
 * variable type integer
 *
 * METADATA
 * {
 *    "type":"integer",
 *    "meta":{
 *      "var":<processor|integer>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarInt extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Integer)',
    'description' => 'An integer variable. It validates the input and returns an error if it is not a integer.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The value of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'int')
      ),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarInt');
    $result = parent::process();

    if (!is_integer($result)) {
      throw new ApiException('invalid integer', 5, $this->id, 417);
    }

    return $result;
  }
}

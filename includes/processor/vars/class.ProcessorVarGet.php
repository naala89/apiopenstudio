<?php

/**
 * Get variable
 *
 * METADATA
 * {
 *    "type":"getVar",
 *    "meta":{
 *      "id":<integer>,
 *      "var":<processor|mixed>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarGet extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
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
    Debug::message('ProcessorVarGet');
    $varName = parent::process();
    if ($this->status != 200) {
      return $varName;
    }

    if (empty($this->request->vars[$varName])) {
      $this->status = 417;
      return new Error(3, $this->id, "get variable ($varName) does not exist");
    } else {
      $result = $this->request->vars[$varName];
    }

    return $result;
  }
}

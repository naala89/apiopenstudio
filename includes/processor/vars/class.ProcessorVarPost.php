<?php

/**
 * Post variable
 *
 * METADATA
 * {
 *    "type":"postVar",
 *    "meta":{
 *      "var":<processor|mixed>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarPost extends ProcessorVar
{
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'boolean')),
    ),
  );

  public function process()
  {
    Debug::message('ProcessorVarPost');
    $varName = parent::process();
    if ($this->status != 200) {
      return $varName;
    }

    if (empty($this->request->vars[$varName])) {
      throw new ApiException("post variable ($varName) does not exist", 5, $this->id, 417);
    } else {
      $result = $this->request->vars[$varName];
    }

    return $result;
  }
}

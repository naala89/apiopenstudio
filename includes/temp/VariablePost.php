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

namespace Datagator\Processors;

class VariablePost extends \Processor
{
  protected $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
    'menu' => 'variables',
    'input' => array(
      'var' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'mixed')),
    ),
  );



  public function process()
  {
    Debug::message('ProcessorVarPost');
    $varName = parent::process();

    if (empty($this->request->vars[$varName])) {
      throw new \Datagator\includes\ApiException("post variable ($varName) does not exist", 5, $this->id, 417);
    } else {
      $result = $this->request->vars[$varName];
    }

    return $result;
  }
}

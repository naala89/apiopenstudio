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

namespace Datagator\Processors;

class VariablerGet extends \Processor
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

    Debug::variable($this->request);
    Debug::variable($varName);

    if (empty($this->request->vars[$varName])) {
      throw new \Datagator\includes\ApiException("get variable ($varName) does not exist", 5, $this->id, 417);
    } else {
      $result = $this->request->vars[$varName];
    }

    return $result;
  }
}
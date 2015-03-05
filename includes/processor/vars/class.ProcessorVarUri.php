<?php

/**
 * URL variable
 *
 * METADATA
 * {
 *    "type":"uriVar",
 *    "meta":{
 *      "id":<integer>,
 *      "index":<integer>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorVarUri extends ProcessorVar
{
  protected $required = array('index');

  protected $details = array(
    'name' => 'Var (URI)',
    'description' => 'A value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
    'menu' => 'variables',
    'input' => array(
      'index' => array(
        'description' => 'The index of the variable, starting with 0 after the client ID, request noun and verb.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'num')
      ),
    ),
  );

  public function process()
  {
    Debug::variable($this->meta, 'ProcessorVarUri');
    $required = $this->validateRequired();
    if ($required !== TRUE) {
      return $required;
    }

    $index = $this->getVar($this->meta->index);
    if ($this->status != 200) {
      return $index;
    }

    if (!isset($this->request->args[$index])) {
      $this->status = 417;
      return new Error(1, $this->id, 'URI index "' . $index . '" does not exist');
    }

    return urldecode($this->request->args[$index]);
  }
}

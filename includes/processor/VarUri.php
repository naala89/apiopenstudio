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

namespace Datagator\Processor;
use Datagator\Core;

class VarUri extends ProcessorBase
{
  protected $details = array(
    'name' => 'Var (URI)',
    'description' => 'A value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'index' => array(
        'description' => 'The index of the variable, starting with 0 after the client ID, request noun and verb.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'int')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarUri', 4);
    $index = $this->val($this->meta->index);

    if (!isset($this->request->args[$index])) {
      throw new Core\ApiException('URI index "' . $index . '" does not exist', 6, $this->id, 417);
    }

    return urldecode($this->request->args[$index]);
  }
}

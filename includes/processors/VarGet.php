<?php

/**
 * Get variable
 *
 * METADATA
 * {
 *    "type":"getVar",
 *    "meta":{
 *      "id":<integer>,
 *      "var":<processor|literal>,
 *    }
 *  }
 */

namespace Datagator\Processors;
use Datagator\Core;

class VarGet extends VarMixed
{
  protected $required = array('name');
  public $details = array(
    'name' => 'Var (Get)',
    'description' => 'A "get" variable. It fetches a variable from the get request.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'accepts' => array('processor', 'literal')
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarGet');
    $name = $this->getVar($this->meta->name);

    if (empty($this->request->vars[$name])) {
      throw new Core\ApiException("get variable ($name) does not exist", 5, $this->id, 417);
    }

    return $this->request->vars[$name];
  }
}

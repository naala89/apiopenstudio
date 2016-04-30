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

namespace Datagator\Processor;
use Datagator\Core;

class VarBody extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Body)',
    'description' => 'Fetch the entire body of a post into a variable.',
    'menu' => 'Primitive',
    'application' => 'All',
    'input' => array(),
  );

  /**
   * @return mixed
   * @throws \Datagator\Core\ApiException
   * @throws \Datagator\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarBody', 4);
    return file_get_contents('php://input');
  }
}

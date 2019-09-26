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

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\Debug;

class VarBody extends VarMixed
{
  protected $details = array(
    'name' => 'Var (Body)',
    'machineName' => 'var_body',
    'description' => 'Fetch the entire body of a post into a variable.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(),
  );

  /**
   * @return mixed
   * @throws \Gaterdata\Core\ApiException
   * @throws \Gaterdata\Processor\ApiException
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
    $body = file_get_contents('php://input');
    return $body;
  }
}

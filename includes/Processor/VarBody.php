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
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Debug;

class VarBody extends VarMixed
{
  protected $details = [
    'name' => 'Var (Body)',
    'machineName' => 'var_body',
    'description' => 'Fetch the entire body of a post into a variable.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => [],
  ];

  /**
   * {inheritDocs}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
    $body = file_get_contents('php://input');
    return new Core\DataContainer($body, 'text');
  }
}

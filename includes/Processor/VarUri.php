<?php

/**
 * URI variable
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Core\Debug;

class VarUri extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (URI)',
    'machineName' => 'var_uri',
    'description' => 'A value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'index' => array(
        'description' => 'The index of the variable, starting with 0 after the client ID, request path.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('integer'),
        'limitValues' => array(),
        'default' => 0
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
    $index = $this->val('index', true);
    $args = $this->request->getArgs();

    if (!isset($args[$index])) {
      throw new Core\ApiException('URI index "' . $index . '" does not exist', 6, $this->id, 417);
    }

    return new Core\DataContainer(urldecode($args[intval($index)]), 'text');
  }
}

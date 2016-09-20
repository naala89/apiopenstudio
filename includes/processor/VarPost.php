<?php

/**
 * Post variable
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarPost extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Var (Post)',
    'machineName' => 'varPost',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the variable.',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('string'),
        'limitValues' => array(),
        'default' => ''
      ),
      'nullable' => array(
        'description' => 'Throw exception if the post variable does not exist.',
        'cardinality' => array(0, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array('boolean'),
        'limitValues' => array(),
        'default' => true
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor VarPost', 4);

    $name = $this->val('name', true);
    $vars = $this->request->getPostVars();

    if (isset($vars[$name])) {
      return new Core\DataContainer($vars[$name], 'text');
    }
    if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
      return new Core\DataContainer('', 'text');
    }

    throw new Core\ApiException("post variable ($name) not received", 5, $this->id, 417);
  }
}

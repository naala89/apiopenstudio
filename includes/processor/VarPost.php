<?php

/**
 * Post variable
 */

namespace Datagator\Processor;
use Datagator\Core;

class VarPost extends VarMixed
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

    $name = $this->val('name');
    $vars = $this->request->getPostVars();

    if (isset($vars[$name])) {
      return $vars[$name];
    }
    if ($this->val('nullable')) {
      return '';
    }
    throw new Core\ApiException("post var $name not available", 1, $this->id);
  }
}

<?php

/**
 * Fragment.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Fragment extends Core\ProcessorEntity
{
  protected $details = array(
    'name' => 'Fragment',
    'machineName' => 'fragment',
    'description' => 'Insert the result of a fragment declaration.',
    'menu' => 'Common',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the fragment',
        'cardinality' => array(1, 1),
        'literalAllowed' => true,
        'limitFunctions' => array(),
        'limitTypes' => array(),
        'limitValues' => array(),
        'default' => ''
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Fragment', 4);

    $name = $this->val('name');
    $fragments = $this->request->getFragments();
    if (empty($fragments) || empty($fragments->$name)) {
      throw new Core\ApiException("invalid fragment name: $name", $this->id);
    }

    return $fragments->$name;
  }
}

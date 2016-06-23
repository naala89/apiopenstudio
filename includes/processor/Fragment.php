<?php

/**
 * Fragment.
 */

namespace Datagator\Processor;
use Datagator\Core;

class Fragment extends ProcessorEntity
{
  protected $details = array(
    'name' => 'Fragment',
    'description' => 'Insert the result of a fragment declaration.',
    'menu' => 'Common',
    'application' => 'Common',
    'input' => array(
      'name' => array(
        'description' => 'The name of the fragment',
        'cardinality' => array(1, 1),
        'accepts' => array('function', 'literal'),
      ),
    ),
  );

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor Fragment', 4);

    $name = $this->val($this->meta->name);
    $fragments = $this->request->getFragments();
    if (empty($fragments) || empty($fragments->$name)) {
      throw new Core\ApiException("invalid fragment name: $name", $this->id);
    }

    return $fragments->$name;
  }
}

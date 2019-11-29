<?php

/**
 * Fragment.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class Fragment extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'Fragment',
    'machineName' => 'fragment',
    'description' => 'Insert the result of a fragment declaration.',
    'menu' => 'Logic',
    'input' => [
      'name' => [
        'description' => 'The name of the fragment',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $name = $this->val('name');
    $fragments = $this->request->getFragments();
    if (empty($fragments) || empty($fragments->$name)) {
      throw new Core\ApiException("invalid fragment name: $name", $this->id);
    }

    return $fragments->$name;
  }
}

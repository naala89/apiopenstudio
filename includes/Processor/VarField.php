<?php

/**
 * Simple field type.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarField extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
    protected $details = [
    'name' => 'Var (field)',
    'machineName' => 'var_field',
    'description' => 'Create a name value pair. This is primarily for use as a field in object.',
    'menu' => 'Primitive',
    'input' => [
      'key' => [
        'description' => 'The key of the nvp.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'value' => [
        'description' => 'The value of the nvp.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => [],
        'limitValues' => [],
        'default' => '',
      ],
    ],
    ];

  /**
   * {@inheritDoc}
   */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $key = $this->val('key', true);
        $value = $this->val('value', true);

        return new Core\DataContainer([$key => $value], 'array');
    }
}

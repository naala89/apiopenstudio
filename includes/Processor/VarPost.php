<?php

/**
 * Post variable
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;

class VarPost extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'Var (Post)',
    'machineName' => 'var_post',
    'description' => 'A "post" variable. It fetches a variable from the post request.',
    'menu' => 'Primitive',
    'application' => 'Common',
    'input' => [
      'key' => [
        'description' => 'The key or name of the POST variable.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'nullable' => [
        'description' => 'Allow the processing to continue if the POST variable does not exist.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true
      ],
    ],
  ];

  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $key = $this->val('key', true);
    $vars = $this->request->getPostVars();

    if (isset($vars[$key])) {
      return new Core\DataContainer($vars[$key], 'text');
    }
    if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
      return new Core\DataContainer('', 'text');
    }

    throw new Core\ApiException("post variable ($key) not received", 5, $this->id, 417);
  }
}

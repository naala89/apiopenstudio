<?php

/**
 * variables that are stored in the vars table in the session
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class VarTemporary extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
    protected $details = [
    'name' => 'Var (Temporary)',
    'machineName' => 'var_temporary',
    'description' => 'A temporarily stored variable. This allows you to store a regularly used variable with a single \
    value and fetch it at any time during your resource call. The value can be deleted, updated and fetched in future \
    resource.',
    'menu' => 'Primitive',
    'input' => [
      'key' => [
        'description' => 'The key or name of the variable.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'value' => [
        'description' => 'The value of the variable. This input is only used in save operations.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'operation' => [
        'description' => 'The operation to be performed on the variable.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['save', 'delete', 'fetch'],
        'default' => '',
      ],
      'strict' => [
        'description' => 'If set to true then return null if var does not exist. If set to false throw exception if \
        var does not exist. Default is strict. Only used in fetch or delete operations.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['boolean'],
        'limitValues' => [],
        'default' => true,
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
        $strict = !empty($this->meta->strict) ? $this->val('strict') : 1;
        $operation = $this->val('operation');

        switch ($operation) {
            case 'save':
                $_SESSION[$name] = $this->meta->value;
            return new Core\DataContainer('true', 'text');
            break;
            case 'delete':
                if (!isset($_SESSION[$name])) {
                    if ($strict) {
                        throw new Core\ApiException('could not delete variable, does not exist', 6, $this->id, 417);
                    }
                    return new Core\DataContainer('true', 'text');
                }
                unset($_SESSION[$name]);
            return new Core\DataContainer('true', 'text');
            break;
            case 'fetch':
                if ($strict && !isset($_SESSION[$name])) {
                    throw new Core\ApiException('could not fetch variable, does not exist', 6, $this->id, 417);
                }
            return new Core\DataContainer($_SESSION[$name], 'text');
            break;
            default:
            throw new Core\ApiException("invalid operation: $operation", 6, $this->id, 417);
            break;
        }
    }
}

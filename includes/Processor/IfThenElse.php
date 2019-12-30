<?php

/**
 * An If Then Else logic gate.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class IfThenElse extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'If Then Else',
        'machineName' => 'ifThenElse',
        'description' => 'An if then else logic gate.',
        'menu' => 'Logic',
        'input' => [
          'lhs' => [
            'description' => 'The left-land side value in the equation.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'rhs' => [
            'description' => 'The right-land side value in the equation.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'operator' => [
            'description' => 'The comparison operator in the equation.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => ['text'],
            'limitValues' => ['==', '!=', '>', '>=', '<', '<='],
            'default' => '',
          ],
          'then' => [
            'description' => 'What to do if the equation returns true.',
            'cardinality' => [1, 1],
            'literalAllowed' => false,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'else' => [
            'description' => 'What to do if the equation returns false.',
            'cardinality' => [1, 1],
            'literalAllowed' => false,
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
        $lhs = $this->val('lhs', true);
        $rhs = $this->val('rhs', true);
        $operator = $this->val('operator', true);

        switch ($operator) {
            case '==':
                $result = $lhs == $rhs;
            break;
            case '!=':
                $result = $lhs != $rhs;
            break;
            case '>':
                $result = $lhs > $rhs;
            break;
            case '>=':
                $result = $lhs >= $rhs;
            break;
            case '<':
                $result = $lhs < $rhs;
            break;
            case '<=':
                $result = $lhs <= $rhs;
            break;
            default:
            throw new Core\ApiException("invalid operator: $operator", 1, $this->id);
            break;
        }

        if ($result) {
            return $this->val('then');
        } else {
            return $this->val('else');
        }
    }
}

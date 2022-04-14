<?php

/**
 * Class IfThenElse.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\ApiException;

/**
 * Class IfThenElse
 *
 * Processor class to if/then/else logic.
 */
class IfThenElse extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'If Then Else',
        'machineName' => 'if_then_else',
        'description' => 'An if then else logic gate. LHS & RHS inputs can be empty to account for null/empty.',
        'menu' => 'Logic',
        'conditional' => true,
        'input' => [
            'lhs' => [
                'description' => 'The left-land side value in the equation.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => false,
            ],
            'rhs' => [
                'description' => 'The right-land side value in the equation.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => false,
            ],
            'operator' => [
                'description' => 'The comparison operator in the equation.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['==', '!=', '>', '>=', '<', '<='],
                'default' => '',
                'conditional' => false,
            ],
            'strict' => [
                'description' => 'Comparisons include data types, not just value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
                'conditional' => false,
            ],
            'then' => [
                'description' => 'What to do if the equation returns true.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => true,
            ],
            'else' => [
                'description' => 'What to do if the equation returns false.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => true,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process()
    {
        parent::process();

        $lhs = $this->val('lhs');
        $rhs = $this->val('rhs');
        $strict = $this->val('strict', true);
        $operator = $this->val('operator', true);

        if ($strict && $lhs->getType() != $rhs->getType()) {
            $doThen = $operator == '!=';
        } else {
            $lhs = $lhs->getData();
            $rhs = $rhs->getData();
            switch ($operator) {
                case '==':
                    $doThen = $lhs == $rhs;
                    break;
                case '!=':
                    $doThen = $lhs != $rhs;
                    break;
                case '>':
                    $doThen = $lhs > $rhs;
                    break;
                case '>=':
                    $doThen = $lhs >= $rhs;
                    break;
                case '<':
                    $doThen = $lhs < $rhs;
                    break;
                case '<=':
                    $doThen = $lhs <= $rhs;
                    break;
                default:
                    throw new ApiException("invalid operator: $operator", 1, $this->id);
            }
        }

        return $doThen ? $this->meta->then : $this->meta->else;
    }
}

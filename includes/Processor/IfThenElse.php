<?php

/**
 * Class IfThenElse.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;

/**
 * Class IfThenElse
 *
 * Processor class to if/then/else logic.
 */
class IfThenElse extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
            'limitProcessors' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'rhs' => [
            'description' => 'The right-land side value in the equation.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitProcessors' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'operator' => [
            'description' => 'The comparison operator in the equation.',
            'cardinality' => [1, 1],
            'literalAllowed' => true,
            'limitProcessors' => [],
            'limitTypes' => ['text'],
            'limitValues' => ['==', '!=', '>', '>=', '<', '<='],
            'default' => '',
          ],
          'then' => [
            'description' => 'What to do if the equation returns true.',
            'cardinality' => [1, 1],
            'literalAllowed' => false,
            'limitProcessors' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
          'else' => [
            'description' => 'What to do if the equation returns false.',
            'cardinality' => [1, 1],
            'literalAllowed' => false,
            'limitProcessors' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => '',
          ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

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

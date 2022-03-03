<?php

/**
 * Class Equation.
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

use ApiOpenStudio\Core;
use Exception;
use jlawrence\eos\Parser;

/**
 * Class Equation
 *
 * Processor class to implement equations.
 */
class Equation extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Equation',
        'machineName' => 'equation',
        // phpcs:ignore
        'description' => 'This processor allows you to define an equation with variables. These input variables are name/value pairs and substitute variables in th4e equation.',
        'menu' => 'Math',
        'input' => [
            'equation' => [
                'description' => 'The equation.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'variables' => [
                'description' => 'The variables. These are an associative array',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => ['var_object'],
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $eq = $this->val('equation', true);
        $vars = $this->val('variables', true);

        try {
            $result = Parser::solve($eq, $vars);
        } catch (Exception $e) {
            throw new Core\ApiException($e->getMessage(), 0, $this->id);
        }

        return new Core\DataContainer($result, 'number');
    }
}

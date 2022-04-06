<?php

/**
 * Class Math.
 *
 * @package    ApiOpenStudio\Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the
 *     ApiOpenStudio Public License. If a copy of the license was not
 *     distributed with this file, You can obtain one at
 *     https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use Exception;
use FormulaParser\FormulaParser;

/**
 * Class Math
 *
 * Processor class to implement basic mathematical formulas.
 */
class Math extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Math',
        'machineName' => 'math',
        // phpcs:ignore
        'description' => <<<TEXT
Formula is a processor for parsing and evaluating mathematical formulas.

In the case of a result of NaN, null is returned.

Supports:

Operators: +, -, *, /, ^
Numbers with decimal point '.'
Numbers in E notation
Constants: pi, e, Inf
Functions: sqrt, abs, sin, cos, tan, log, exp
Unlimited nested parentheses
NaN (Not a Number)

e.g.

10 + log(0)
(-5)^500+5
abs(-x^500)/pi
INF + x
3*x^2 - 4*y + 3/y
5/-x
+-z
sqrt(x^y/pi)
abs(a-b^3)
x-tan(-4)^3
(y)^x
XeY+5^30
2^(sqrt(x)^3)
(-1E3+1)^(1E+x)
4^-0.8e+1/x
--sin(c)
exp((-3)^2)

Example:

processor: math
id: example formula
formula: 3*x^2 - 4*y + 3/y
precision: 2
variables:
    processor: var_object
    id: formula variables
    attributes:
        - 
            processor: var_field
            id: variable 1
            key: x
            value: -4
        - 
            processor: var_field
            id: variable 2
            key: y
            value: 8

RESULT: 16.38
TEXT,
        'menu' => 'Math',
        'input' => [
            'formula' => [
                'description' => 'The formula.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'precision' => [
                'description' => 'Number of digits after the decimal point.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'variables' => [
                'description' => 'The variables. This is an object of var-field.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => ['var_object'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
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
        $formula = $this->val('formula', true);
        $precision = $this->val('precision', true);
        $vars = $this->val('variables', true);

        try {
            $parser = !empty($precision) ? new FormulaParser($formula, $precision) : new FormulaParser($formula);
            if (!empty($vars)) {
                $parser->setVariables($vars);
            }
            $result = $parser->getResult();
        } catch (Exception $e) {
            throw new Core\ApiException($e->getMessage(), 6, $this->id);
        }
        if ($result[0] == 'error') {
            throw new Core\ApiException($result[1], 6, $this->id);
        }

        return new Core\DataContainer($result[1]);
    }
}

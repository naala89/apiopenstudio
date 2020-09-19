<?php
/**
 * Class Equation.
 *
 * @package    Gaterdata
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
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
    protected $details = [
        'name' => 'Equation',
        'machineName' => 'equation',
        // phpcs:ignore
        'description' => 'This function allows you to define an equation with variables. These input variables are name/value pairs and substitute variables in th4e equation.',
        'menu' => 'Math',
        'input' => [
            'equation' => [
                'description' => 'The equation.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'variables' => [
                'description' => 'The variables. These are an associative array',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => ['varObject'],
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

        $eq = $this->val('equation', true);
        $vars = $this->val('variables', true);

        try {
            $result = Parser::solve($eq, $vars);
        } catch (\Exception $e) {
            throw new Core\ApiException($e->getMessage(), 0, $this->id);
        }

        return new Core\DataContainer($result, 'number');
    }
}

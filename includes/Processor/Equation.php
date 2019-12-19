<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;
use jlawrence\eos\Parser;

/**
 * Allows the calculation of equations, with variables.
 */
class Equation extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
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
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

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

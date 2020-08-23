<?php

/**
 * Parent class for mixed variable types
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class VarMixed extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Mixed)',
        'machineName' => 'var_mixed',
        'description' => 'A variable of any type.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the variable.',
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
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $result = $this->val('value');
        if (!$this->isDataContainer($result)) {
            $result = new Core\DataContainer($result, 'text');
        }

        return $result;
    }
}

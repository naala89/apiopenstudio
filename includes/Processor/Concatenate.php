<?php

/**
 * Perform string concatenation of two or more inputs
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class Concatenate extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Concatenate',
        'machineName' => 'concatenate',
        'description' => 'Concatenate a series of strings or numbers into a single string.',
        'menu' => 'Data operation',
        'input' => [
          'sources' => [
            'description' => 'The values to concatenate',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => ''
          ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $sources = $this->val('sources');
        $result = '';
        foreach ($sources as $source) {
            $result .= (string) $this->isDataContainer($source) ? $source->getData() : $source;
        }

        return new Core\DataContainer($result, 'text');
    }
}

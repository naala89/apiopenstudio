<?php

/**
 * Convert any data container data type to a JSON data container.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Output\Json;

class ConvertToJson extends Json
{
    /**
     * @var mixed The output data.
     */
    protected $data;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Convert to JSON',
        'machineName' => 'convert_to_json',
        'description' => 'Convert an input data into a different JSON data type (i.e. array, XML or object into a JSON string).',
        'menu' => 'Data operation',
        'input' => [
            'source' => [
                'description' => 'The source data.',
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
        $this->data = $this->val('source');
        return new Core\DataContainer($this->getData(), 'json');
    }
}

<?php

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Defines the Collection class.
 */

class Collection extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Collection',
        'machineName' => 'collection',
        'description' => 'Collection contains multiple values, like an array or list.',
        'menu' => 'Primitive',
        'input' => [
          'items' => [
            'description' => 'The items in the collection',
            'cardinality' => [0, '*'],
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
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $items = $this->val('items', true);

        if ($this->isDataContainer($items)) {
            if ($items->getType == 'array') {
                return $items;
            }
            // Convert the container of single type into a container of array.
            return new Core\DataContainer([$items], 'array');
        }

        // Convert single value into an array container.
        if (!is_array($items)) {
            return new Core\DataContainer([$items], 'array');
        }
    
        return new Core\DataContainer($items, 'array');
    }
}

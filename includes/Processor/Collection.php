<?php
/**
 * Class Collection.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class Collection
 *
 * Processor class to define a collection.
 */
class Collection extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
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
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
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

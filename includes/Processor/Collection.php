<?php

/**
 * Class Collection.
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
 * Class Collection
 *
 * Processor class to define a collection.
 */
class Collection extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
            'limitProcessors' => [],
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

<?php

/**
 * Class Collection.
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
    protected array $details = [
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $items = $this->val('items', true);

        if (is_array($items)) {
            foreach ($items as $key => $item) {
                if ($this->isDataContainer($item)) {
                    $items[$key] = $item->getData();
                }
            }
        } elseif (empty($items)) {
            $items = [];
        } else {
            $items = [$items];
        }

        return new Core\DataContainer($items, 'array');
    }
}

<?php

/**
 * Class Concatenate.
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

use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\ApiException;

/**
 * Class Collection
 *
 * Processor class to perform a concatenate operation.
 */
class Concatenate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Concatenate',
        'machineName' => 'concatenate',
        'description' => 'Concatenate a collection of strings or numbers.',
        'menu' => 'Data operation',
        'input' => [
          'items' => [
            'description' => 'The values to concatenate',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitProcessors' => ['collection', 'var_object'],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => []
          ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $items = $this->val('items', true);
        $result = '';

        foreach ($items as $item) {
            $result .= $this->isDataContainer($item) ? $item->getData() : $item;
        }

        try {
            $result = new DataContainer($result, 'text');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}

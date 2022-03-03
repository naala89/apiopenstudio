<?php

/**
 * Class Sort.
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
 * Class Sort
 *
 * Processor class to sort input data.
 */
class Sort extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Sort',
        'machineName' => 'sort',
        // phpcs:ignore
        'description' => 'Sort an input of multiple values. The values can be singular items or name/value pairs (sorted by key or value). Singular items cannot be mixed with name/value pairs.',
        'menu' => 'Data operation',
        'input' => [
            'values' => [
                'description' => 'The values to sort.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort ascending or descending.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'sort_by' => [
                'description' => 'Perform the sort on key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => ['key', 'value'],
                'default' => 'key',
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

        $values = $this->val('values', true);

        if (empty($values) || !is_array($values)) {
            return $values;
        }

        $direction = $this->val('direction', true);
        $sortBy = $this->val('sort_by', true);

        if ($sortBy == 'key') {
            if ($direction == 'desc') {
                if (!Core\Utilities::isAssoc($values)) {
                    ksort($values);
                }
            } else {
                if (!Core\Utilities::isAssoc($values)) {
                    $values = array_reverse($values);
                } else {
                    krsort($values);
                }
            }
        } else {
            if ($direction == 'asc') {
                if (!Core\Utilities::isAssoc($values)) {
                    sort($values);
                } else {
                    asort($values);
                }
            } else {
                if (!Core\Utilities::isAssoc($values)) {
                    rsort($values);
                } else {
                    arsort($values);
                }
            }
        }

        $this->logger->debug('api', 'values after sort: ' . print_r($values, true));

        return new Core\DataContainer($values);
    }
}

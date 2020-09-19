<?php
/**
 * Class Sort.
 *
 * @package    Gaterdata
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

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
    protected $details = [
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
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'direction' => [
                'description' => 'Sort ascending or descending.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['asc', 'desc'],
                'default' => 'asc',
            ],
            'sortBy' => [
                'description' => 'Perform the sort on key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
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
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $values = $this->val('values', true);

        if (empty($values) || !is_array($values)) {
            return $values;
        }

        $direction = $this->val('direction', true);
        $sortBy = $this->val('sortBy', true);

        $this->logger->debug('values before sort: ' . print_r($values, true));

        if ($sortBy == 'key') {
            if ($direction == 'asc') {
                if (!Core\Utilities::isAssoc($values)) {
                    // do nothing, this is a normal array
                } else {
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

        $this->logger->debug('values after sort: ' . print_r($values, true));

        return $values;
    }
}

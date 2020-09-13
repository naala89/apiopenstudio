<?php
/**
 * Class FieldValue.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class FieldValue
 *
 * Processor class to fetch the key or value from a field.
 */
class FieldValue extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Field value',
        'machineName' => 'field_value',
        'description' => 'Returns the key oy value from a field.',
        'menu' => 'Data operation',
        'input' => [
            'field' => [
                'description' => 'The input field.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitFunctions' => ['var_field'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'key_value' => [
                'description' => 'Return the key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => [],
                'limitValues' => ['key', 'value'],
                'default' => 'value',
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

        $field = $this->val('field', true);
        $keyValue = $this->val('key_value', true);

        $keys = array_keys($field);

        return new Core\DataContainer($keyValue == 'value' ? $field[$keys[0]] : $keys[0]);
    }
}

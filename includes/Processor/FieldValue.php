<?php

/**
 * Class FieldValue.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
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
 * Class FieldValue
 *
 * Processor class to fetch the key or value from a field.
 */
class FieldValue extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
                'limitProcessors' => ['var_field'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'key_value' => [
                'description' => 'Return the key or value.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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

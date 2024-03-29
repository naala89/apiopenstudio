<?php

/**
 * Class FieldValue.
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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;

/**
 * Class FieldValue
 *
 * Processor class to fetch the key or value from a field.
 */
class FieldValue extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Field value',
        'machineName' => 'field_value',
        'description' => 'Returns the key or value from a field.',
        'menu' => 'Data operation',
        'input' => [
            'field' => [
                'description' => 'The input field.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => ['var_field'],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $field = $this->val('field', true);
        $keyValue = $this->val('key_value', true);

        $keys = array_keys($field);

        return new DataContainer($keyValue == 'value' ? $field[$keys[0]] : $keys[0]);
    }
}

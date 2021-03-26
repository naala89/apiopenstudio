<?php

/**
 * Class VarField.
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
 * Class VarField
 *
 * Processor class to define a field variable.
 */
class VarField extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Var (field)',
        'machineName' => 'var_field',
        // phpcs:ignore
        'description' => 'Create a name value pair. This is primarily for use as a field in object. individual key/values can be input or a whole array. ',
        'menu' => 'Primitive',
        'input' => [
            'key' => [
                'description' => 'The key of the field name/value pair.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'value' => [
                'description' => 'The value of the field name/value pair.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'array' => [
                'description' => 'Array to be converted to a field. This can only have one index.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => [],
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

        $array = $this->val('array', true);
        $key = $this->val('key', true);
        $value = $this->val('value', true);

        if (!empty($array)) {
            if (sizeof($array) > 1) {
                throw new Core\ApiException('Cannot have more than one index in an input array.', 0, $this->id, 417);
            }
            $keys = array_keys($array);
            return new Core\DataContainer([$keys[0] => $array[$keys[0]]], 'array');
        }

        return new Core\DataContainer([$key => $value], 'array');
    }
}

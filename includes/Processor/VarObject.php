<?php

/**
 * Class VarObject.
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
 * Class VarObject
 *
 * Processor class to define an object variable.
 */
class VarObject extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var (object)',
        'machineName' => 'var_object',
        // phpcs:ignore
        'description' => 'Create a complex object. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
        'menu' => 'Primitive',
        'input' => [
            'attributes' => [
                'description' => 'The value of an attribute or a complex object.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => ['var_field'],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => '',
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
        parent::process();
        $attributes = $this->val('attributes', true);
        $result = [];

        foreach ($attributes as $attribute) {
            $field = $this->isDataContainer($attribute) ? $attribute->getData() : $attribute;
            if (is_object($field)) {
                $field = (array) $field;
            }
            $keys = is_object($field) ? get_object_vars($field) : array_keys($field);
            $result[$keys[0]] = $field[$keys[0]];
        }

        return new Core\DataContainer($result, 'array');
    }
}

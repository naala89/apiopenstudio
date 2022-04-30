<?php

/**
 * Class VarObject.
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

use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ApiException;

/**
 * Class VarObject
 *
 * Processor class to define an object variable.
 */
class VarObject extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Object',
        'machineName' => 'var_object',
        // phpcs:ignore
        'description' => 'Create a complex object. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
        'menu' => 'Primitive',
        'input' => [
            'attributes' => [
                'description' => 'An array of key/value pairs for each attribute in the array.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
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
        $attributes = $this->val('attributes', true);

        if (empty($attributes)) {
            try {
                $result = new DataContainer([], 'array');
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            return $result;
        }

        $result = [];
        foreach ($attributes as $index => $attribute) {
            $attribute = $this->isDataContainer(($attribute)) ? $attribute->getData() : $attribute;
            if (empty($attribute)) {
                continue;
            }
            $attribute = is_object($attribute) ? (array) $attribute : $attribute;
            if (!is_array($attribute)) {
                throw new ApiException(
                    "Cannot add attribute at index: $index. Attributes must be an array of key/value pair array/object",
                    6,
                    $this->id,
                    400
                );
            }
            if (sizeof($attribute) > 1) {
                throw new ApiException(
                    "Cannot add attribute at index: $index. The attribute must have a single key/value pair",
                    6,
                    $this->id,
                    400
                );
            }
            $keys = array_keys($attribute);
            $key = $keys[0];
            if (isset($result[$key])) {
                throw new ApiException(
                    "Cannot add attribute at index: $index. The attribute $key already exists",
                    6,
                    $this->id,
                    400
                );
            }
            $result[$key] = $attribute[$key];
        }

        try {
            $result = new DataContainer($result, 'array');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}

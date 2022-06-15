<?php

/**
 * Class VarLiteral.
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
 * Class VarLiteral
 *
 * Processor class te represent a literal.
 */
class VarLiteral extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Literal',
        'machineName' => 'var_literal',
        'description' => 'A literal string or value.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the literal.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => null,
            ],
            'type' => [
                'description' => 'The literal type.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [
                    'array',
                    'boolean',
                    'float',
                    'file',
                    'html',
                    'image',
                    'integer',
                    'json',
                    'text',
                    'undefined',
                    'xml',
                ],
                'default' => null,
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

        $value = $this->val('value', true);
        $value = is_object($value) ? json_decode(json_encode($value), true) : $value;
        $type = $this->val('type', true);

        try {
            if (!empty($type)) {
                $result = new DataContainer($value, $type);
            } else {
                $result = new DataContainer($value);
            }
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $result;
    }
}

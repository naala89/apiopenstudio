<?php

/**
 * Class VarUri.
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
 * Class VarUri
 *
 * Processor class to return a value from the request URI.
 */
class VarUri extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var (URI)',
        'machineName' => 'var_uri',
        // phpcs:ignore
        'description' => 'A url-decoded value from the request URI. It fetches the value of a particular param in the URI, based on the index value.',
        'menu' => 'Primitive',
        'input' => [
            'index' => [
                'description' => 'The index of the variable, starting with 0 after the client ID, request path.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'expected_type' => [
                // phpcs:ignore
                'description' => 'The expected input data type. If type is not defined, then ApiOpenStudio will attempt automatically set the data type.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [
                    'boolean',
                    'integer',
                    'float',
                    'text',
                    'empty',
                ],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the URI index does not exist (returns "").',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
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

        $index = intval($this->val('index', true));
        $nullable = $this->val('nullable', true);
        $expectedType = $this->val('expected_type', true);
        $args = $this->request->getArgs();

        $data = $args[$index] ?? '';

        if (!empty($expectedType)) {
            try {
                $result = new Core\DataContainer($data, $expectedType);
            } catch (Core\ApiException $e) {
                throw new Core\ApiException($e->getMessage(), 6, $this->id, 400);
            }
        } else {
            $result = new Core\DataContainer($data);
        }

        if (!$nullable && $result->getType() == 'empty') {
            throw new Core\ApiException("URI var does not exist or is empty: $index", 6, $this->id, 400);
        }

        return $result;
    }
}

<?php

/**
 * Class VarUri.
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
 * Class VarUri
 *
 * Processor class to return a value from the request URI.
 */
class VarUri extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Uri',
        'machineName' => 'var_uri',
        // phpcs:ignore
        'description' => 'A url-decoded value from the request URI. It fetches the value of a particular param in the URI, based on the index value. To pass null as a URI parameter, send the value "null", e.g. /4/null/foobar.',
        'menu' => 'Request',
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
                    'image',
                    'text',
                    'undefined',
                ],
                'default' => null,
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $index = intval($this->val('index', true));
        $nullable = $this->val('nullable', true);
        $expectedType = $this->val('expected_type', true);
        $args = $this->request->getArgs();

        if (!$nullable && !isset($args[$index])) {
            throw new ApiException("URI var does not exist or is undefined: $index", 6, $this->id, 400);
        }
        $data = $args[$index] ?? null;

        if (!empty($expectedType)) {
            try {
                $result = new DataContainer($data, $expectedType);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), 6, $this->id, 400);
            }
        } else {
            $result = new DataContainer($data);
        }

        return $result;
    }
}

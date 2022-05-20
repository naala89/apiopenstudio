<?php

/**
 * Class VarRequest.
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

use ApiOpenStudio\Core;

/**
 * Class VarRequest
 *
 * Processor class to return the post and get variables in a request.
 */
class VarRequest extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Request',
        'machineName' => 'var_request',
        'description' => 'A "get" or "post" variable. It fetches a variable from the get or post requests.',
        'menu' => 'Request',
        'input' => [
            'key' => [
                'description' => 'The key or name of the GET/POST variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'expected_type' => [
                // phpcs:ignore
                'description' => 'The expected input data type. If type is not defined, then ApiOpenStudio will attempt automatically set the data type.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [
                    'array',
                    'boolean',
                    'file',
                    'float',
                    'html',
                    'image',
                    'integer',
                    'json',
                    'text',
                    'undefined',
                    'xml',
                ],
                'default' => 'text',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the GET or POST variable does not exist.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
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

        $key = $this->val('key', true);
        $nullable = $this->val('nullable', true);
        $expectedType = $this->val('expected_type', true);

        $vars = array_merge($this->request->getGetVars(), $this->request->getPostVars());
        $data = empty($vars[$key]) ? null : $vars[$key];

        if (!$nullable && empty($data)) {
            throw new Core\ApiException("Request var does not exist or is empty: $key", 6, 400);
        }

        if (!empty($expectedType)) {
            return new Core\DataContainer($data, $expectedType);
        }
        return new Core\DataContainer($data);
    }
}

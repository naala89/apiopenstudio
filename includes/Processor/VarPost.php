<?php

/**
 * Class VarPost.
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
 * Class VarPost
 *
 * Processor class to return the post variables in a request.
 */
class VarPost extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Post',
        'machineName' => 'var_post',
        'description' => 'A "post" variable. It fetches a variable from the post request.',
        'menu' => 'Request',
        'input' => [
            'key' => [
                'description' => 'The key or name of the POST variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'expected_type' => [
                // phpcs:ignore
                'description' => 'The expected input data type. ApiOpenStudio will attempt to cast the input to this data type. If type is not defined, then ApiOpenStudio will attempt automatically set the data type.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [
                    'boolean',
                    'integer',
                    'float',
                    'text',
                    'array',
                    'json',
                    'xml',
                    'html',
                    'image',
                    'file',
                    'empty',
                ],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the POST variable does not exist.',
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
        $vars = $this->request->getPostVars();

        $data = $vars[$key] ?? '';

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
            throw new Core\ApiException("POST var does not exist or is empty: $key", 6, $this->id, 400);
        }

        return $result;
    }
}

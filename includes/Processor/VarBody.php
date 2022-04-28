<?php

/**
 * Class VarBody.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Debug;

/**
 * Class VarBody
 *
 * Processor class to return the contents of the body as a variable.
 */
class VarBody extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Body',
        'machineName' => 'var_body',
        'description' => 'Fetch the entire body of a post.',
        'menu' => 'Request',
        'input' => [
            'expected_type' => [
                // phpcs:ignore
                'description' => 'The expected data type in the body. If type is not defined, then ApiOpenStudio will attempt automatically set the data type.',
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
                'description' => 'Throw an error if the body is empty.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => 'true',
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

        $expectedType = $this->val('expected_type', true);
        $nullable = $this->val('nullable', true);
        $data = file_get_contents('php://input');

        if (empty($data) && !$nullable) {
            throw new ApiException("Body is empty", 6, $this->id);
        }

        if (!empty($expectedType)) {
            return new Core\DataContainer($data, $expectedType);
        }
        return new Core\DataContainer($data);
    }
}

<?php

/**
 * Class VarGet.
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
 * Class VarGet
 *
 * Processor class to return a requests get variables.
 */
class VarGet extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Get',
        'machineName' => 'var_get',
        // phpcs:ignore
        'description' => 'A "get" variable. It fetches a url-decoded variable from the get request. Empty values are treated as NULL.',
        'menu' => 'Request',
        'input' => [
            'key' => [
                'description' => 'The key or name of the GET variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'expected_type' => [
                // phpcs:ignore
                'description' => 'The expected incoming data type. This will cause ApiOpenStudio to explicitly cast the data to the type. If unable to cast (and nullable set to false), then an exception will be thrown.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [
                    'array',
                    'boolean',
                    'float',
                    'html',
                    'integer',
                    'json',
                    'text',
                    'undefined',
                    'xml',
                ],
                'default' => null,
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the GET variable does not exist.',
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

        $key = $this->val('key', true);
        $expectedType = $this->val('expected_type', true);
        $nullable = $this->val('nullable', true);
        $vars = $this->request->getGetVars();
        $data = null;

        if (isset($vars[$key])) {
            if (is_array($vars[$key])) {
                foreach ($vars[$key] as $index => $val) {
                    $vars[$key][$index] = !empty($val) || $val === '0' ? urldecode($val) : null;
                }
                $data = $vars[$key];
            } else {
                $data = !empty($vars[$key]) || $vars[$key] === '0' ? $vars[$key] : null;
            }
        }

        if (!empty($expectedType)) {
            if (!empty($data)) {
                try {
                    $result = new DataContainer($data, $expectedType);
                } catch (ApiException $e) {
                    throw new ApiException($e->getMessage(), 6, $this->id, 400);
                }
            } else {
                $result = new DataContainer($data);
                $result->setType($expectedType);
            }
        } else {
            $result = new DataContainer($data);
        }

        if (!$nullable && ($result->getType() == 'undefined' || is_null($result->getData()))) {
            throw new ApiException(
                "GET variable ($key) does not exist or is undefined",
                6,
                $this->id,
                400
            );
        }

        return $result;
    }
}

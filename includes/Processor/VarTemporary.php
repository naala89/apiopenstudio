<?php

/**
 * Class VarTemporary.
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
 * Class VarTemporary
 *
 * Processor class to define a temporary variable (stored in the request session),
 */
class VarTemporary extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Var (Temporary)',
        'machineName' => 'var_temporary',
        // phpcs:ignore
        'description' => 'A temporarily stored variable that will only be available to and have a life-time of the individual resource call. This allows you to store a regularly used variable with a single value and fetch it at any time during your resource call. The value can be deleted, updated and fetched in future resource. If the process fails (for whatever reason) and "strict" is set to true, then an exception will be thrown, otherwise false will be returned.',
        'menu' => 'Variables',
        'input' => [
            'key' => [
                'description' => 'The key or name of the variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'value' => [
                'description' => 'The value of the variable. This input is only used in save operations.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
            ],
            'operation' => [
                'description' => 'The operation to be performed on the variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['save', 'delete', 'fetch'],
                'default' => '',
            ],
            'strict' => [
                // phpcs:ignore
                'description' => 'If set to true then return null if var does not exist. If set to false throw exception if var does not exist. Default is strict. Only used in fetch or delete operations.',
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $key = $this->val('key', true);
        $value = $this->val('value', true);
        $strict = $this->val('strict', true);
        $operation = $this->val('operation', true);

        switch ($operation) {
            case 'save':
                $_SESSION[$key] = $value;
                $result = true;
                break;
            case 'delete':
                if (!isset($_SESSION[$key])) {
                    if ($strict) {
                        throw new ApiException('could not delete variable, does not exist', 6, $this->id, 417);
                    }
                    $result = false;
                } else {
                    unset($_SESSION[$key]);
                    $result = true;
                }
                break;
            case 'fetch':
                if (!isset($_SESSION[$key])) {
                    if ($strict) {
                        throw new ApiException('could not fetch variable, does not exist', 6, $this->id, 417);
                    }
                    $result = false;
                } else {
                    $result = $_SESSION[$key];
                }
                break;
            default:
                throw new ApiException("invalid operation: $operation", 6, $this->id, 417);
        }

        return new DataContainer($result);
    }
}

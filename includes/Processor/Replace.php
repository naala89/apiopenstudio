<?php
/**
 * Class Replace.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class PasswordReset
 *
 * Processor class to perform a str replace for a substring within a string.
 */
class Replace extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Replace',
        'machineName' => 'replace',
        'description' => 'Replace a substring in a string.',
        'menu' => 'Data operation',
        'input' => [
            'haystack' => [
                'description' => 'The source string.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'json', 'xml'],
                'limitValues' => [],
                'default' => ''
            ],
            'needle' => [
                'description' => 'The substring to replace.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => ''
            ],
            'value' => [
                'description' => 'The value to replace the needle.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => ''
            ],
            'ignore_case' => [
                'description' => 'Ignore case while searching for the needle.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
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
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $haystack = $this->val('haystack');
        $needle = $this->val('needle', true);
        $value = $this->val('value', true);
        $ignoreCase = $this->val('ignore_case', true);

        $type = $haystack->getType();
        $haystack = $haystack->getData();

        if ($ignoreCase) {
            $result = str_ireplace($needle, $value, $haystack);
        } else {
            $result = str_replace($needle, $value, $haystack);
        }

        return new Core\DataContainer($result, $type);
    }
}

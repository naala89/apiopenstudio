<?php

/**
 * Class Replace.
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
 * Class Replace
 *
 * Processor to perform a str replace for a substring within a string.
 */
class Replace extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Replace',
        'machineName' => 'replace',
        'description' => 'Replace a substring in a string.',
        'menu' => 'Data operation',
        'input' => [
            'haystack' => [
                'description' => 'The source string.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'json', 'xml'],
                'limitValues' => [],
                'default' => null,
            ],
            'needle' => [
                'description' => 'The substring to replace.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'json', 'xml'],
                'limitValues' => [],
                'default' => null,
            ],
            'value' => [
                'description' => 'The value to replace the needle.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'json', 'xml', 'integer', 'float'],
                'limitValues' => [],
                'default' => null,
            ],
            'ignore_case' => [
                'description' => 'Ignore case while searching for the needle.',
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

        return new DataContainer($result, $type);
    }
}

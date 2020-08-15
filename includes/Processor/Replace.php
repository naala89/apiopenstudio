<?php

/**
 * Replace a substring in a string.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class Replace extends Core\ProcessorEntity
{
    /**
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
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $haystack = $this->val('haystack');
        $needle = $this->val('needle', true);
        $value = $this->val('value', true);
        $ignoreCase = $this->val('ignore_case', true);

        $type = $haystack->getType();
        $haystack = $haystack->getData();

        if ($ignoreCase) {
            $result = str_ireplace($needle, $value, $haystack);
        }
        else {
            $result = str_replace($needle, $value, $haystack);
        }

        return new Core\DataContainer($result, $type);
    }
}

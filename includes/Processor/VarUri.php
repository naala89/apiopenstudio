<?php

/**
 * URI variable
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Core\Debug;

class VarUri extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
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
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);
        $index = intval($this->val('index', true));
        $args = $this->request->getArgs();

        if (!isset($args[$index])) {
            return new Core\DataContainer('', 'string');
        }

        return new Core\DataContainer(urldecode($args[$index]));
    }
}

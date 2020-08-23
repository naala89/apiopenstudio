<?php

/**
 * Get variable.
 *
 * @TODO: Should we cater for urlencoded keys in array values?
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

class VarGet extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Get)',
        'machineName' => 'var_get',
        'description' => 'A "get" variable. It fetches a urldecoded variable from the get request.',
        'menu' => 'Primitive',
        'input' => [
            'key' => [
                'description' => 'The key or name of the GET variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the GET variable does not exist.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => false,
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $key = $this->val('key', true);
        $vars = $this->request->getGetVars();
    
        if (isset($vars[$key])) {
            if (is_array($vars[$key])) {
                foreach ($vars[$key] as $index => $val) {
                    $vars[$key][$index] = urldecode($val);
                }
                return new Core\DataContainer($vars[$key], 'array');
            }
            return new Core\DataContainer(urldecode($vars[$key]));
        }
        if (filter_var($this->val('nullable', true), FILTER_VALIDATE_BOOLEAN)) {
            return new Core\DataContainer('', 'text');
        }

        throw new Core\ApiException("GET variable ($key) not received", 5, $this->id, 417);
    }
}

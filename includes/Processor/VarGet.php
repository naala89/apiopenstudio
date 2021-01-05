<?php
/**
 * Class VarGet.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;

/**
 * Class VarGet
 *
 * Processor class to return a requests get variables.
 */
class VarGet extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
                'limitTypes' => ['boolean', 'integer'],
                'limitValues' => [],
                'default' => false,
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

        throw new Core\ApiException("GET variable ($key) not received", 6, $this->id, 400);
    }
}

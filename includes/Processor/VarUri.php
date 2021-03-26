<?php

/**
 * Class VarUri.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;

/**
 * Class VarUri
 *
 * Processor class to return a value from the request URI.
 */
class VarUri extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'nullable' => [
                'description' => 'Allow the processing to continue if the URI index does not exist (returns "").',
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
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $index = intval($this->val('index', true));
        $nullable = $this->val('nullable', true);
        $args = $this->request->getArgs();

        if (!isset($args[$index])) {
            if ($nullable) {
                return new Core\DataContainer('', 'text');
            } else {
                throw new Core\ApiException("URI index $index does not exist", 6, $this->id, 400);
            }
        }

        return new Core\DataContainer(urldecode($args[$index]));
    }
}

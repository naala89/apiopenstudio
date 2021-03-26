<?php

/**
 * Class VarInt.
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
 * Class VarInt
 *
 * Processor class to define an integer variable.
 */
class VarInt extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
    'name' => 'Var (Integer)',
        'machineName' => 'var_int',
        'description' => 'An integer variable. It validates the input and returns an error if it is not a integer.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
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

        $result = $this->val('value');
        if (!$this->isDataContainer($result)) {
            $result = new Core\DataContainer($result, 'integer');
        }
        $integer = filter_var($result->getData(), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (is_null($integer)) {
            throw new Core\ApiException($result->getData() . ' is not integer', 6, $this->id, 400);
        }
        $result->setData($integer);
        $result->setType('integer');
        return $result;
    }
}

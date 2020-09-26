<?php
/**
 * Class VarBool.
 *
 * @package    Gaterdata
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class VarBool
 *
 * Processor class to define a boolean variable.
 */
class VarBool extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Var (Boolean)',
        'machineName' => 'var_bool',
        'description' => 'A boolean variable. It validates the input (0,1,yes,no,true,false) into a boolean value, and \
        returns an error if it is not a boolean.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean', 'integer', 'text'],
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

        $value = $this->val('value', true);
        switch ($value) {
            case 'yes':
            case 'true':
                $value = true;
                break;
            case 'no':
            case 'false':
                $value = false;
                break;
        }
        $boolean = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if (is_null($boolean)) {
            throw new Core\ApiException("$value is not boolean", 6, $this->id, 400);
        }

        return new Core\DataContainer($boolean, 'boolean');
    }
}

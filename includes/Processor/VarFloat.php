<?php
/**
 * Class VarFloat.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class VarFloat
 *
 * Processor class to define a float variable.
 */
class VarFloat extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Float)',
        'machineName' => 'var_float',
        'description' => 'A float variable. It validates the input and returns an error if it is not a float.',
        'menu' => 'Primitive',
        'input' => [
            'value' => [
                'description' => 'The value of the variable.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['float'],
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
            $result = new Core\DataContainer($result, 'float');
        }
        $float = filter_var($result->getData(), FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
        if (is_null($float)) {
            throw new Core\ApiException($result->getData() . ' is not float', 0, $this->id);
        }
        $result->setData($float);
        $result->setType('float');
        return $result;
    }
}

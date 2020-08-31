<?php
/**
 * Class VarRand.
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
 * Class VarRand
 *
 * Processor class to generate a random value.
 */
class VarRand extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Var (Rand)',
        'machineName' => 'var_rand',
        // phpcs:ignore
        'description' => 'A random variable. It produces a random variable of any specified length or mix of character types.',
        'menu' => 'Primitive',
        'input' => [
            'length' => [
                'description' => 'The length of the variable.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 8,
            ],
            'lower' => [
                'description' => 'Use lower-case alpha characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'upper' => [
                'description' => 'Use upper-case alpha characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'numeric' => [
                'description' => 'Use numeric characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'special' => [
                'description' => 'Use special characters.',
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
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $length = $this->val('length', true);
        $lower = $this->val('lower', true);
        $upper = $this->val('upper', true);
        $numeric = $this->val('numeric', true);
        $special = $this->val('special', true);

        return new Core\DataContainer(Core\Utilities::randomString($length, $lower, $upper, $numeric, $special),
            'text');
    }
}

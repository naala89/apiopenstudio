<?php

/**
 * Class VarRand.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;

/**
 * Class VarRand
 *
 * Processor class to generate a random value.
 */
class VarRand extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
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
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => 8,
            ],
            'lower' => [
                'description' => 'Use lower-case alpha characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean', 'integer'],
                'limitValues' => [],
                'default' => true,
            ],
            'upper' => [
                'description' => 'Use upper-case alpha characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean', 'integer'],
                'limitValues' => [],
                'default' => true,
            ],
            'numeric' => [
                'description' => 'Use numeric characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean', 'integer'],
                'limitValues' => [],
                'default' => true,
            ],
            'special' => [
                'description' => 'Use special characters.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
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

        $length = $this->val('length', true);
        $lower = $this->val('lower', true);
        $upper = $this->val('upper', true);
        $numeric = $this->val('numeric', true);
        $special = $this->val('special', true);

        return new Core\DataContainer(
            Core\Utilities::randomString(
                $length,
                boolval($lower),
                boolval($upper),
                boolval($numeric),
                boolval($special)
            ),
            'text'
        );
    }
}

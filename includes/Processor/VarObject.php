<?php
/**
 * Class VarObject.
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
 * Class VarObject
 *
 * Processor class to define an object variable.
 */
class VarObject extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Var (object)',
        'machineName' => 'var_object',
        // phpcs:ignore
        'description' => 'Create a complex object. This is useful for creating an output of object from selected input fields. You can use field processor for name value pairs, or other processors or literals to create single values. It can also be used to parse XML, JSON input from an external source into an object that you can work with.',
        'menu' => 'Primitive',
        'input' => [
            'attributes' => [
                'description' => 'The value of an attribute or a complex object.',
                'cardinality' => [0, '*'],
                'literalAllowed' => true,
                'limitFunctions' => ['varField'],
                'limitTypes' => [],
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
        $attributes = $this->val('attributes', false);
        $result = [];

        foreach ($attributes as $attribute) {
            $field = $attribute->getData();
            $keys = array_keys($field);
            $result[$keys[0]] = $field[$keys[0]];
        }

        return new Core\DataContainer($result, 'array');
    }
}

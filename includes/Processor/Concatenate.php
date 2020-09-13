<?php
/**
 * Class Concatenate.
 *
 * @package Gaterdata
 * @subpackage Processor
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;

/**
 * Class Collection
 *
 * Processor class to perform a concatenate operation.
 */
class Concatenate extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Concatenate',
        'machineName' => 'concatenate',
        'description' => 'Concatenate a series of strings or numbers into a single string.',
        'menu' => 'Data operation',
        'input' => [
          'sources' => [
            'description' => 'The values to concatenate',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitFunctions' => [],
            'limitTypes' => [],
            'limitValues' => [],
            'default' => ''
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

        $sources = $this->val('sources');
        $result = '';
        foreach ($sources as $source) {
            $result .= (string) $this->isDataContainer($source) ? $source->getData() : $source;
        }

        return new Core\DataContainer($result, 'text');
    }
}

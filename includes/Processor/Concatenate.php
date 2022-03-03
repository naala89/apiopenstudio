<?php

/**
 * Class Concatenate.
 *
 * @package    ApiOpenStudio\Processor
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
 * Class Collection
 *
 * Processor class to perform a concatenate operation.
 */
class Concatenate extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Concatenate',
        'machineName' => 'concatenate',
        'description' => 'Concatenate a series of strings or numbers into a single string.',
        'menu' => 'Data operation',
        'input' => [
          'sources' => [
            'description' => 'The values to concatenate',
            'cardinality' => [2, '*'],
            'literalAllowed' => true,
            'limitProcessors' => [],
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
    public function process(): Core\DataContainer
    {
        parent::process();

        $sources = $this->val('sources');
        $result = '';
        foreach ($sources as $source) {
            $result .= (string) $this->isDataContainer($source) ? $source->getData() : $source;
        }

        return new Core\DataContainer($result, 'text');
    }
}

<?php
/**
 * Class Concatenate.
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

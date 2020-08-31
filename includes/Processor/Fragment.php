<?php
/**
 * Class Fragment.
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
 * Class Fragment
 *
 * Processor class to define a fragment.
 * This is a like a routine that can be called multiple times in a resource.
 */
class Fragment extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Fragment',
        'machineName' => 'fragment',
        'description' => 'Insert the result of a fragment declaration.',
        'menu' => 'Logic',
        'input' => [
          'name' => [
            'description' => 'The name of the fragment',
            'cardinality' => [1, 1],
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

        $name = $this->val('name');
        $fragments = $this->request->getFragments();
        if (empty($fragments) || empty($fragments->$name)) {
            throw new Core\ApiException("invalid fragment name: $name", $this->id);
        }

        return $fragments->$name;
    }
}

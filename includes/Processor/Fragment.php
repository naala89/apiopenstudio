<?php
/**
 * Class Fragment.
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
 * Class Fragment
 *
 * Processor class to define a fragment.
 * This is a like a routine that can be called multiple times in a resource.
 */
class Fragment extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Fragment',
        'machineName' => 'fragment',
        'description' => 'Insert the result of a fragment declaration.',
        'menu' => 'Logic',
        'input' => [
          'name' => [
            'description' => 'The name of the fragment',
            'cardinality' => [1, 1],
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

        $name = $this->val('name');
        $fragments = $this->request->getFragments();
        if (empty($fragments) || empty($fragments->$name)) {
            throw new Core\ApiException("invalid fragment name: $name", $this->id);
        }

        return $fragments->$name;
    }
}

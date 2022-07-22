<?php

/**
 * Class Fragment.
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

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;

/**
 * Class Fragment
 *
 * Processor class to define a fragment.
 * This is a like a routine that can be called multiple times in a resource.
 */
class Fragment extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Fragment',
        'machineName' => 'fragment',
        'description' => 'Use the result of a pre-calculated fragment block.',
        'menu' => 'Logic',
        'input' => [
          'key' => [
            'description' => 'The key-name of the fragment',
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException Exception if invalid result.
     */
    public function process(): DataContainer
    {
        parent::process();

        $key = $this->val('key', true);

        try {
            $fragment = $this->request->getFragment($key);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return $fragment;
    }
}

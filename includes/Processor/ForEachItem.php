<?php

/**
 * Class ForEachItem.
 *
 * The individual input element's keys and values are available during
 * processing time as var_temporary variables, their keys are:
 *   <for_each_item.id>.key
 *   <for_each_item.id>.val
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

use ADOConnection;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\TreeParser;
use ApiOpenStudio\Core\ApiException;
use DeepCopy\DeepCopy;

/**
 * Class ForEachItem
 *
 * Processor class to For...Each logic.
 */
class ForEachItem extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'For Each Item',
        'machineName' => 'for_each_item',
        // phpcs:ignore
        'description' => 'A For Each logic gate. The iteration value and key are available using VarTemporary and the indexes "<for_each_item id>.key" and "<for_each_item id>.val". To access the data after processing, place the data in VarTemporary, within the for...each logic.',
        'menu' => 'Logic',
        'conditional' => true,
        'input' => [
            'input' => [
                'description' => 'The input array or object.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => [],
                'conditional' => false,
            ],
            'process_loop' => [
                'description' => 'The processing logic to apply to each item.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
                'conditional' => true,
            ],
            'process_after' => [
                // phpcs:ignore
                'description' => 'Processing to take place after processing the items. Because this is a conditional processor, it is likely to be processed last. If you have any logic that needs to process the data after the for...each loop, place it here.',
                'cardinality' => [0, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => [],
                'conditional' => true,
            ],
        ],
    ];

    /**
     * @var \DeepCopy\DeepCopy
     *   Enable deepy copy of objects.
     */
    protected DeepCopy $deepCopy;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        $meta,
        Request $request,
        ADOConnection $db = null,
        MonologWrapper $logger = null
    ) {
        parent::__construct($meta, $request, $db, $logger);
        $this->deepCopy = new DeepCopy();
    }

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function process()
    {
        parent::process();

        $itemKey = $this->id . '.key';
        $itemVal = $this->id . '.val';
        $inputArray = (array) $this->val('input', true);

        foreach ($inputArray as $key => $val) {
            $treeParser = new TreeParser($this->request, $this->db, $this->logger);
            $_SESSION[$itemKey] = $key;
            $_SESSION[$itemVal] = $val;
            $processLoop = $this->deepCopy->copy($this->meta->process_loop);
            $treeParser->pushToProcessingStack($processLoop);
            $treeParser->crawlMeta();
        }

        // Cleanup - the array elements stored in the session are no longer needed.
        unset($_SESSION[$itemKey]);
        unset($_SESSION[$itemVal]);

        return $this->meta->process_after;
    }
}

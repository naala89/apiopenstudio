<?php

/**
 * Class TreeParser.
 *
 * @package    ApiOpenStudio
 * @subpackage Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use ADOConnection;
use stdClass;

/**
 * Class TreeParser
 *
 * Crawl through the processor node tree, using depth-first iteration.
 *
 * The parse stores all un-processed nodes in $unprocessedStack.
 * Where a node has children, each child is placed at the head of the stack so that it can be processed before the
 * parent.
 *
 * Node results are placed into $resultStack.
 *
 * Once a parent node has been calculated, it's value is placed in the $resultStack,
 * and it's children results are removed from $resultStack.
 *
 * The crawler needs to also take into account conditional nodes. These branches should only be traversed,
 * based on the parent comparison results. This save unnecessary calculation of redundant branches.
 */
class TreeParser
{
    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    private ProcessorHelper $helper;

    /**
     * Logging class.
     *
     * @var MonologWrapper $logger
     */
    private MonologWrapper $logger;

    /**
     * @var array
     *   Stack of unprocessed nodes.
     */
    protected array $unprocessedStack = [];

    /**
     * @var array
     *   Stack of processed node results.
     */
    protected array $resultStack = [];

    /**
     * DB connection object.
     *
     * @var ADOConnection
     */
    private ADOConnection $db;

    /**
     * Request object class.
     *
     * @var Request
     */
    private Request $request;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param ADOConnection $db
     * @param MonologWrapper $logger
     */
    public function __construct(Request $request, ADOConnection $db, MonologWrapper $logger)
    {
        $this->helper = new ProcessorHelper();
        $this->request = $request;
        $this->db = $db;
        $this->logger = $logger;
    }

    /**
     * Process the metadata, using depth first iteration.
     *
     * @param mixed $meta The resource metadata.
     *
     * @throws ApiException
     */
    public function crawlMeta($meta)
    {
        $this->unprocessedStack[] = $meta;

        while (!empty($this->unprocessedStack)) {
            $currentNode = array_pop($this->unprocessedStack);

            if ($this->helper->isProcessor($currentNode)) {
                $this->processNode($currentNode);
            } elseif (is_array($currentNode)) {
                // currentNode is an array, process each item
                foreach ($currentNode as $index => $item) {
                    if ($this->helper->isProcessor($item)) {
                        if (isset($this->resultStack[$item->id])) {
                            $currentNode[$index] = $this->resultStack[$item->id];
                            unset($this->resultStack[$item->id]);
                        } else {
                            $this->processNode($item);
                        }
                    }
                }
            }
        }

        return array_shift($this->resultStack);
    }

    /**
     * Attempt to process a node from the stack. If the required attributes are not yet calculated,
     * re-add to the unprocessed stack followed by the unprocessed attributes.
     *
     * @throws ApiException
     */
    protected function processNode(stdClass $node)
    {
        $childNodes = [];
        $classStr = $this->helper->getProcessorString($node->processor);
        $class = new $classStr($node, $this->request, $this->db, $this->logger);
        $details = $class->details();
        $preprocess = isset($details['conditional']) && $details['conditional'] == true;

        foreach ((array) $node as $attributeId => $attribute) {
            if ($this->helper->isProcessor($attribute)) {
                if (isset($this->resultStack[$attribute->id])) {
                    $node->{$attributeId} = $this->resultStack[$attribute->id];
                    unset($this->resultStack[$attribute->id]);
                } elseif (!$preprocess || $details['input'][$attributeId]['conditional'] == false) {
                    $childNodes[] = $attribute;
                }
            } elseif (is_array($attribute)) {
                // currentNode is an array, process each item
                foreach ($attribute as $index => $item) {
                    if ($this->helper->isProcessor($item)) {
                        if (isset($this->resultStack[$item->id])) {
                            $node->{$attributeId}[$index] = $this->resultStack[$item->id];
                            unset($this->resultStack[$item->id]);
                        } else {
                            $childNodes[] = $item;
                        }
                    }
                }
            }
        }

        if (!empty($childNodes)) {
            $this->reprocessAfterChildren($node, $childNodes);
        } elseif ($preprocess) {
            // We have the result of the logic for a conditional processor.
            // The process() result is the meta for the branch to follow.
            $result = $class->process();
            $result->id = $node->id;
            $this->unprocessedStack[] = $result;
        } else {
            $this->resultStack[$node->id] = $class->process();
        }
    }

    /**
     * Re-add a node to the unprocessedStack, after its child dependencies.
     * @param $node
     * @param $childNodes
     */
    protected function reprocessAfterChildren($node, $childNodes)
    {
        $this->unprocessedStack[] = $node;
        foreach ($childNodes as $childNode) {
            $this->unprocessedStack[] = $childNode;
        }
    }
}

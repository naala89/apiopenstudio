<?php

/**
 * Class TreeParser.
 *
 * @package    ApiOpenStudio\Core
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Core;

use ADOConnection;

/**
 * Class TreeParser
 *
 * Crawl through the processor node tree, using depth-first iteration.
 *
 * The parser stores all un-processed nodes in $processingStack.
 * Where a node has children, each child is placed at the head of the stack so that it can be processed before the
 * parent.
 *
 * Node results are placed into $resultStack.
 *
 * Once a parent node has been calculated, it's value is placed in the $resultStack,
 * and it's children results are removed from $resultStack.
 *
 * The crawler needs to also take into account conditional nodes. These branches should only be traversed,
 * based on the parent comparison results. This save unnecessary calculation of redundant branches. These nodes are
 * placed in the jitStack, and will only be processed once all the nodes in the $processingStack have been cleared.
 */
class TreeParser
{
    /**
     * ApiOpenStudio Cache instance.
     *
     * @var Cache
     */
    protected Cache $cache;

    /**
     * Processor helper class.
     *
     * @var ProcessorHelper
     */
    protected ProcessorHelper $helper;

    /**
     * Logging class.
     *
     * @var MonologWrapper $logger
     */
    protected MonologWrapper $logger;

    /**
     * Stack of nodes to be processed.
     *
     * @var array
     */
    protected array $processingStack = [];

    /**
     * Stack of processed node results.
     *
     * @var array
     */
    protected array $resultStack = [];

    /**
     * DB connection object.
     *
     * @var ADOConnection
     */
    protected ADOConnection $db;

    /**
     * This request.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * Constructor.
     *
     * @param Request $request
     *   Request object.
     * @param ADOConnection $db
     *   DB connection.
     * @param MonologWrapper $logger
     *   Logger.
     */
    public function __construct(Request $request, ADOConnection $db, MonologWrapper $logger, Cache $cache)
    {
        $this->helper = new ProcessorHelper();
        $this->request = $request;
        $this->db = $db;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * Update the request object.
     *
     * @param Request $request
     *
     * @return void
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    /**
     * Append a node tree structure to the end of the processingStack.
     *
     * @param array $tree
     *   Node tree to add to the stack.
     *
     * @return void
     */
    public function pushToProcessingStack(array $tree): void
    {
        $this->processingStack[] = $tree;
    }

    /**
     * Fetch the element from the end of the processingStack.
     *
     * @return mixed|null
     *   Value from the stack to be processed.
     */
    public function popFromProcessingStack()
    {
        return array_pop($this->processingStack);
    }

    /**
     * Returns if the processing stack is empty.
     *
     * @return bool
     *   Processing stack is empty.
     */
    public function processingStackEmpty(): bool
    {
        return empty($this->processingStack);
    }

  /**
   * Add a value to thew result stack, indexed by its ID.
   *
   * @param $id
   *   ID/Key for the result item.
   * @param $val
   *   Value for the result item.
   *
   * @return void
   */
    public function addToResultStack($id, $val): void
    {
        $this->resultStack[$id] = $val;
    }

  /**
   * Fetch a value from the result stack.
   * This will remove it from the result stack.
   *
   * @param $id
   *
   * @return mixed|null Result value.
   */
    public function getFromResultStack($id)
    {
        if (isset($this->resultStack[$id])) {
            $result = $this->resultStack[$id];
            unset($this->resultStack[$id]);
            return $result;
        }
        return null;
    }

    /**
     * Process the metadata, using depth first iteration.
     *
     * @return mixed
     *
     * @throws ApiException
     */
    public function crawlMeta()
    {
        while (!$this->processingStackEmpty()) {
            $currentNode = $this->popFromProcessingStack();

            if ($this->helper->isProcessor($currentNode)) {
                $this->processNode($currentNode);
            } elseif (is_array($currentNode)) {
                // currentNode is an array, process each item
                foreach ($currentNode as $index => $item) {
                    if ($this->helper->isProcessor($item)) {
                        if (($result = $this->getFromResultStack($item['id'])) !== null) {
                            $currentNode[$index] = $result;
                        } else {
                            $this->processNode($item);
                        }
                    }
                }
            }
        }

        return array_pop($this->resultStack);
    }

    /**
     * Attempt to process a node from the stack. If the required attributes are not yet calculated,
     * re-add to the unprocessed stack followed by the unprocessed attributes.
     *
     * @param array $node Metadata for the node to process.
     *
     * @return void
     *
     * @throws ApiException
     */
    protected function processNode(array $node): void
    {
        // Check for cached processor result.
        if (!is_null($cachedResult = $this->getProcessorCache($node))) {
            $this->addToResultStack($node['id'], $cachedResult);
            return;
        }

        $childNodes = [];
        $classStr = $this->helper->getProcessorString($node['processor']);
        $class = new $classStr($node, $this->request, $this->db, $this->logger);
        $details = $class->details();
        $conditionalProcessor = isset($details['conditional']) && $details['conditional'];

        $attributeIds = array_keys($node);
        foreach ($attributeIds as $attributeId) {
            $conditionalAttribute = $details['input'][$attributeId]['conditional'] ?? false;
            if ($this->helper->isProcessor($node[$attributeId])) {
                if (($result = $this->getFromResultStack($node[$attributeId]['id'])) !== null) {
                    $node[$attributeId] = $result;
                } elseif (!$conditionalProcessor || !$conditionalAttribute) {
                    $childNodes[] = $node[$attributeId];
                }
            } elseif (is_array($node[$attributeId]) && !$conditionalProcessor) {
                // currentNode is an array, process each item
                foreach ($node[$attributeId] as $index => $item) {
                    if ($this->helper->isProcessor($item)) {
                        if (($result = $this->getFromResultStack($item['id'])) !== null) {
                            $node[$attributeId][$index] = $result;
                        } else {
                            $childNodes[] = $item;
                        }
                    }
                }
            }
        }

        if (!empty($childNodes)) {
            $this->reprocessAfterChildren($node, $childNodes);
        } elseif ($conditionalProcessor) {
            // We have the result of the logic for a conditional processor.
            // The process() result is the meta for the branch to follow.
            $result = $class->process();
            if ($this->helper->isProcessor($result)) {
                $result['id'] = $node['id'];
                $this->pushToProcessingStack($result);
            } else {
                $this->addToResultStack($node['id'], $result);
            }
        } else {
            $result = $class->process();
            $this->setProcessorCache($node, $result);
            $this->addToResultStack($node['id'], $result);
        }
    }

    /**
     * Fetch a processor result from cache. Null is returned if no results.
     *
     * @param array $node Metadata for the node to fetch from cache.
     *
     * @return DataContainer|null
     *
     * @throws ApiException
     */
    protected function getProcessorCache(array $node): ?DataContainer
    {
        if (isset($node['cache_ttl'])) {
            $processorCacheKey = $node['cache_id'] ?? '';
            if (empty($processorCacheKey)) {
                $resource = $this->request->getResource();
                if (is_null($inputsHash = $this->getInputsHash($node))) {
                    return null;
                }
                $processorCacheKey = $this->cache->getProcessorCacheKey(
                    $resource->getResid(),
                    $node['id'],
                    $inputsHash
                );
            }
            return $this->cache->get($processorCacheKey);
        }
        return null;
    }

    /**
     * Store a processor result in cache.
     *
     * @param array $node Metadata for the node to fetch from cache.
     * @param array|DataContainer $data Result data to store.
     *
     * @return bool
     *
     * @throws ApiException
     */
    protected function setProcessorCache(array $node, $data): bool
    {
        if (isset($node['cache_ttl'])) {
            $processorCacheKey = $node['cache_id'] ?? '';
            if (empty($processorCacheKey)) {
                $resource = $this->request->getResource();
                if (is_null($inputsHash = $this->getInputsHash($node))) {
                    return false;
                }
                $processorCacheKey = $this->cache->getProcessorCacheKey(
                    $resource->getResid(),
                    $node['id'],
                    $inputsHash
                );
            }
            return $this->cache->set($processorCacheKey, $data, $node['cache_ttl']);
        }
        return false;
    }

    /**
     * Return a hash of all calculated inputs.
     *
     * @param array $node
     *
     * @return string|null
     */
    protected function getInputsHash(array $node): ?string
    {
        $inputsHash = '';
        foreach ($node as $value) {
            if (is_array($value) && isset($value['processor']) && isset($value['id'])) {
                if (!isset($this->resultStack[$value['id']])) {
                    return null;
                }
                $result = $this->resultStack[$value['id']];
                $result = $result->getData();
                $result = is_null($result) ? 'null' : $result;
                $inputsHash .= is_array($result) ? json_encode($result) : $result;
            }
        }
        return md5($inputsHash);
    }

    /**
     * Pre-process a node's children.
     *
     * @param array $node
     * @param array $childNodes
     *
     * @return void
     */
    protected function reprocessAfterChildren(array $node, array $childNodes): void
    {
        $this->pushToProcessingStack($node);
        foreach ($childNodes as $childNode) {
            $this->pushToProcessingStack($childNode);
        }
    }
}

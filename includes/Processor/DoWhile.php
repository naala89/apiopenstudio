<?php

/**
 * Class DoWhile.
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

use ADOConnection;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\ProcessorHelper;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\TreeParser;
use ApiOpenStudio\Core\ApiException;
use DeepCopy\DeepCopy;

/**
 * Class DoWhile
 *
 * Processor class to Do...While logic.
 */
class DoWhile extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Do While',
        'machineName' => 'do_while',
        'description' => 'A processor for do...while logic.',
        'menu' => 'Logic',
        'conditional' => true,
        'input' => [
            'lhs' => [
                'description' => 'The left-land side value in the equation.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => true,
            ],
            'rhs' => [
                'description' => 'The right-land side value in the equation.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => [],
                'limitValues' => [],
                'default' => '',
                'conditional' => true,
            ],
            'operator' => [
                'description' => 'The comparison operator in the equation.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['==', '!=', '>', '>=', '<', '<='],
                'default' => '',
                'conditional' => true,
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
            'max_loops' => [
                // phpcs:ignore
                'description' => 'Optionally set the maximum times a the do...while loops can run. A negative value will run until the LHS/RHS comparison returns false.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => -1,
            ],
        ],
    ];

    /**
     * @var DeepCopy
     *   Enable deep copy of objects.
     */
    protected DeepCopy $deepCopy;

    /**
     * @var ProcessorHelper
     *   processor helper class.
     */
    protected ProcessorHelper $processorHelper;

    /**
     * @var TreeParser
     *   TreeParser.
     */
    protected TreeParser $treeParser;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        $meta,
        Request &$request,
        ADOConnection $db = null,
        MonologWrapper $logger = null
    ) {
        parent::__construct($meta, $request, $db, $logger);
        $this->deepCopy = new DeepCopy();
        $this->processorHelper = new ProcessorHelper();
        $this->treeParser = new TreeParser($this->request, $this->db, $this->logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return DataContainer
     *
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        parent::process();

        $maxLoops = $this->val('max_loops', true);
        $comparisonVals = $this->getComparisonVals();
        $comparisonTrue = $this->comparisonTrue(
            $comparisonVals['lhs'],
            $comparisonVals['rhs'],
            $comparisonVals['operator']
        );

        while ($comparisonTrue && $maxLoops-- != 0) {
            $processLoopMeta = $this->deepCopy->copy($this->meta->process_loop);
            $this->treeParser->pushToProcessingStack($processLoopMeta);
            $this->treeParser->crawlMeta();

            $comparisonVals = $this->getComparisonVals();
            $comparisonTrue = $this->comparisonTrue(
                $comparisonVals['lhs'],
                $comparisonVals['rhs'],
                $comparisonVals['operator']
            );
        }

        return new DataContainer(null);
    }

    /**
     * return calculated valued for LHS, RHS and operator for comparison.
     *
     * @return array
     *
     * @throws ApiException
     */
    protected function getComparisonVals(): array
    {
        $lhsMeta = $this->deepCopy->copy($this->meta->lhs);
        $rhsMeta = $this->deepCopy->copy($this->meta->rhs);
        $operatorMeta = $this->deepCopy->copy($this->meta->operator);

        if ($this->processorHelper->isProcessor($lhsMeta)) {
            $this->treeParser->pushToProcessingStack($lhsMeta);
            $lhs = $this->treeParser->crawlMeta();
        } else {
            $lhs = $lhsMeta;
        }
        if ($this->processorHelper->isProcessor($rhsMeta)) {
            $this->treeParser->pushToProcessingStack($rhsMeta);
            $rhs = $this->treeParser->crawlMeta();
        } else {
            $rhs = $rhsMeta;
        }
        if ($this->processorHelper->isProcessor($operatorMeta)) {
            $this->treeParser->pushToProcessingStack($operatorMeta);
            $operator = $this->treeParser->crawlMeta();
        } else {
            $operator = $operatorMeta;
        }

        return ['lhs' => $lhs, 'rhs' => $rhs, 'operator' => $operator];
    }

    /**
     * Evaluate continuing the do...while loop.
     *
     * @param mixed|null $lhs
     * @param mixed|null $rhs
     * @param string|null $operator
     *
     * @return bool
     *
     * @throws ApiException
     */
    protected function comparisonTrue($lhs, $rhs, ?string $operator): bool
    {
        $lhs = $this->isDataContainer($lhs) ? $lhs->getData() : $lhs;
        $rhs = $this->isDataContainer($rhs) ? $rhs->getData() : $rhs;
        $operator = $this->isDataContainer($operator) ? $operator->getData() : $operator;
        switch ($operator) {
            case '==':
                $doWhile = $lhs == $rhs;
                break;
            case '!=':
                $doWhile = $lhs != $rhs;
                break;
            case '>':
                $doWhile = $lhs > $rhs;
                break;
            case '>=':
                $doWhile = $lhs >= $rhs;
                break;
            case '<':
                $doWhile = $lhs < $rhs;
                break;
            case '<=':
                $doWhile = $lhs <= $rhs;
                break;
            default:
                throw new ApiException("invalid operator: $operator", 1, $this->id);
        }

        return $doWhile;
    }
}

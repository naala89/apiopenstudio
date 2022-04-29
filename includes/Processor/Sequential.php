<?php

/**
 * Class Sequential.
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
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\TreeParser;
use ApiOpenStudio\Core\ApiException;
use DeepCopy\DeepCopy;

/**
 * Class Sequential
 *
 * Processor class to implement sequential processing logic.
 */
class Sequential extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Sequential',
        'machineName' => 'sequential',
        // phpcs:ignore
        'description' => 'Run a sequence of processors in a specific order. The return value will be the result of the final processor.',
        'menu' => 'Logic',
        'conditional' => true,
        'input' => [
            'sequence' => [
                'description' => 'The input array of processors.',
                'conditional' => true,
                'cardinality' => [1, '*'],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['array'],
                'limitValues' => [],
                'default' => [],
            ],
        ],
    ];

    /**
     * @var DeepCopy
     *   Enable deepy copy of objects.
     */
    protected DeepCopy $deepCopy;

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
    }

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function process()
    {
        parent::process();
        $sequence = $this->meta->sequence;

        for ($index = 0; $index < sizeof($sequence) - 1; $index++) {
            $treeParser = new TreeParser($this->request, $this->db, $this->logger);
            $treeParser->pushToProcessingStack($sequence[$index]);
            $treeParser->crawlMeta();
        }

        return $sequence[$index];
    }
}

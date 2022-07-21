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
use ApiOpenStudio\Core\Cache;
use ApiOpenStudio\Core\Config;
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
     *   Enable deep copy of objects.
     */
    protected DeepCopy $deepCopy;

    /**
     * @var TreeParser
     *   TreeParser.
     */
    protected TreeParser $treeParser;

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->deepCopy = new DeepCopy();
        $settings = new Config();
        $cache = new Cache($settings->__get(['api', 'cache']), $this->logger);
        $this->treeParser = new TreeParser($this->request, $this->db, $this->logger, $cache);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function process()
    {
        parent::process();
        $sequence = $this->meta['sequence'];

        for ($index = 0; $index < sizeof($sequence) - 1; $index++) {
            $this->treeParser->pushToProcessingStack($sequence[$index]);
            $this->treeParser->crawlMeta();
        }

        return $sequence[$index];
    }
}

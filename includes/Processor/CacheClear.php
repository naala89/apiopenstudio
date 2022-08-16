<?php

/**
 * Class CacheClear.
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
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Cache;
use ApiOpenStudio\Core\Config;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;

/**
 * Class CacheClear
 *
 * Processor class to clear ApiOpenStudio cache.
 */
class CacheClear extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Cache clear',
        'machineName' => 'cache_clear',
        'description' => ' Clear all ApiOpenStudio cache.',
        'menu' => 'Admin',
        'input' => [],
    ];

    protected Cache $cache;

    /**
     * {@inheritDoc}
     *
     * @throws ApiException
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $settings  = new Config();
        $cacheSettings = $settings->__get(['api', 'cache']);
        if (!$cacheSettings['active']) {
            throw new ApiException('Cache inactive', 8, $this->id, 400);
        }
        $this->cache = new Cache($cacheSettings, $logger);
    }

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

        return new DataContainer($this->cache->clear(), 'text');
    }
}

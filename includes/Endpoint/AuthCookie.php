<?php

/**
 * Class AuthCookie.
 *
 * @package    ApiOpenStudio
 * @subpackage Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Endpoint;

use ApiOpenStudio\Core;

/**
 * Class AuthCookie
 *
 * Provide cookie authentication to a resource.
 */
class AuthCookie extends Core\ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'Auth (Cookie)',
        'machineName' => 'auth_cookie',
        'description' => 'Authentication for remote server, using a cookie.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'cookie' => [
                'description' => 'The cookie string.',
                'cardinality' => [1, 1],
                'literalAllowed' => false,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     */
    public function process()
    {
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $cookie = $this->val('cookie', true);

        return new Core\DataContainer([CURLOPT_COOKIE => $cookie], 'array');
    }
}

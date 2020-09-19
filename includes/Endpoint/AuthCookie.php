<?php
/**
 * Class AuthCookie.
 *
 * @package    Gaterdata
 * @subpackage Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 GaterData
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://gaterdata.com
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

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
                'limitFunctions' => [],
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

        return array(CURLOPT_COOKIE => $cookie);
    }
}

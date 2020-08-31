<?php
/**
 * Class AuthCookie.
 *
 * @package Gaterdata
 * @subpackage Endpoint
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
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
     * @var array Details of the processor.
     *
     * {@inheritDoc}
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

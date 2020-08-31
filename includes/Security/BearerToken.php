<?php
/**
 * Class BearerToken.
 *
 * @package Gaterdata
 * @subpackage Security
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Security;

use Gaterdata\Core;

/**
 * Class BearerToken
 *
 * Security class to process a bearer token.
 */
class BearerToken extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Bearer Token',
        'machineName' => 'bearer_token',
        // phpcs:ignore
        'description' => 'Fetch a bearer token from the request header. This takes the form of "Authorization: Bearer <token>"',
        'menu' => 'Security',
        'input' => [],
    ];

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process()
    {
        $this->logger->info('Security: ' . $this->details()['machineName']);

        $headers = '';

        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            // Nginx or fast CGI.
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about
            // capitalization for Authorization)
            $requestHeaders = array_combine(array_map(
                'ucwords',
                array_keys($requestHeaders)),
                array_values($requestHeaders)
            );
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        $headerParts = explode(' ', $headers);
        return array_pop($headerParts);
    }
}

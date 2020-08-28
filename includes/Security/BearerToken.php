<?php

/**
 * Fetch the bearer token from the Header
 *
 * This assumes the format
 *  Bearer <token>
 */

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Core\Debug;

class BearerToken extends Core\ProcessorEntity
{
    /**
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

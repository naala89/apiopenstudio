<?php
/**
 * Class AuthDigest.
 *
 * @package Gaterdata
 * @subpackage Endpoint
 * @author john89 (https://gitlab.com/john89)

 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Endpoint;

use Gaterdata\Core;

/**
 * Class AuthDigest
 *
 * Provide Digest authentication to a resource.
 */
class AuthDigest extends Core\ProcessorEntity
{
    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Auth (Digest User/Pass)',
        'machineName' => 'auth_digest',
        'description' => 'Digest authentication for remote server, using username/password.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'username' => [
                'description' => 'The username.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The password.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
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

        $username = $this->val('username', true);
        $password = $this->val('password', true);

        return array(
        CURLOPT_USERPWD => "$username:$password",
        CURLOPT_HTTPAUTH => CURLAUTH_DIGEST
        );
    }
}

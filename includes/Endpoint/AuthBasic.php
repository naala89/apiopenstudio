<?php

/**
 * Class AuthBasic.
 *
 * @package    ApiOpenStudio\Endpoint
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Endpoint;

use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\ProcessorEntity;

/**
 * Class AuthBasic
 *
 * Provide Auth basic authentication to a resource.
 */
class AuthBasic extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'Auth (Basic User/Pass)',
        'machineName' => 'auth_basic',
        'description' => 'Basic authentication for remote server, using username/password.',
        'menu' => 'Endpoint authentication',
        'input' => [
            'username' => [
                'description' => 'The username.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The password.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
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
     * @return DataContainer Result of the processor.
     *
     * @throws ApiException
     */
    public function process(): DataContainer
    {
        parent::process();

        $username = $this->val('username', true);
        $password = $this->val('password', true);

        return new DataContainer([CURLOPT_USERPWD => "$username:$password"], 'array');
    }
}

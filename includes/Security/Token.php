<?php
/**
 * Class Token.
 *
 * @package Gaterdata
 * @subpackage Security
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *      If a copy of the MPL was not distributed with this file, You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link https://gaterdata.com
 */

namespace Gaterdata\Security;

use Gaterdata\Core;
use Gaterdata\Db;
use Monolog\Logger;

/**
 * Class Token
 *
 * Provide valid token authentication.
 */
class Token extends Core\ProcessorEntity
{
    /**
     * @var mixed
     */
    protected $role = false;

    /**
     * @var Db\UserMapper
     */
    protected $userMapper;

    /**
     * @var array Details of the processor.
     *
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'Token',
        'machineName' => 'token',
        'description' => 'Validate that the user has a valid token.',
        'menu' => 'Security',
        'input' => [
            'token' => [
                'description' => 'The consumers token.',
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
     * Token constructor.
     *
     * @param mixed $meta The processor metadata.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db Database object.
     * @param \Monolog\Logger $logger Logger object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
    }

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

        $token = $this->val('token', true);

        // no token
        if (empty($token)) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        // invalid token or user not active
        $user = $this->userMapper->findBytoken($token);
        if (empty($user->getUid()) || $user->getActive() == 0) {
            throw new Core\ApiException('permission denied', 4, -1, 401);
        }

        return true;
    }
}

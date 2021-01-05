<?php
/**
 * Class UserUpdate.
 *
 * @package    ApiOpenStudio
 * @subpackage Processor
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Processor;

use ApiOpenStudio\Core;
use ApiOpenStudio\Db;
use Monolog\Logger;

/**
 * Class UserUpdate
 *
 * Processor class to update a user.
 */
class UserUpdate extends Core\ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private $userMapper;

    /**
     * User role mapper class.
     *
     * @var Db\UserRoleMapper
     */
    private $userRoleMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected $details = [
        'name' => 'User update',
        'machineName' => 'user_update',
        'description' => 'Update a user.',
        'menu' => 'Admin',
        'input' => [
            'token' => [
                'description' => 'The current users token.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'uid' => [
                'description' => 'The user ID of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'username' => [
                'description' => 'The username of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'active' => [
                'description' => 'The active flag for the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'password' => [
                'description' => 'The password of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'honorific' => [
                'description' => 'The honorific of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['Mr', 'Ms', 'Miss', 'Mrs', 'Dr', 'Prof', 'Hon'],
                'default' => '',
            ],
            'name_first' => [
                'description' => 'The first name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'name_last' => [
                'description' => 'The last name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'company' => [
                'description' => 'The company of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'website' => [
                'description' => 'The website of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_street' => [
                'description' => 'The street address of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_suburb' => [
                'description' => 'The suburb of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_city' => [
                'description' => 'The city of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_state' => [
                'description' => 'The state of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_country' => [
                'description' => 'The country of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_postcode' => [
                'description' => 'The postcode of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'phone_mobile' => [
                'description' => 'The mobile phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'phone_work' => [
                'description' => 'The work phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => 0,
            ],
        ],
    ];

    /**
     * UserUpdate constructor.
     *
     * @param mixed $meta Output meta.
     * @param mixed $request Request object.
     * @param \ADODB_mysqli $db DB object.
     * @param \Monolog\Logger $logger Logget object.
     */
    public function __construct($meta, &$request, \ADODB_mysqli $db, Logger $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db);
        $this->userRoleMapper = new Db\UserRoleMapper($db);
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
        $this->logger->info('Processor: ' . $this->details()['machineName']);

        $token = $this->val('token', true);
        $uid = $this->val('uid', true);
        $currentUser = $this->userMapper->findBytoken($token);

        if (!$this->userRoleMapper->hasRole($currentUser->getUid(), 'Administrator')
            && !$this->userRoleMapper->hasRole($currentUser->getUid(), 'Account manager')
            && !$this->userRoleMapper->hasRole($currentUser->getUid(), 'Application manager')) {
            // Non-privileged accounts can only edit their own accounts.
            if (!empty($uid) && $uid != $currentUser->getUid()) {
                throw new Core\ApiException("Permission denied", 6, $this->id, 400);
            }
            $uid = $currentUser->getUid();
        }

        $user = $this->userMapper->findByUid($uid);
        if (empty($user->getUid())) {
            throw new Core\ApiException("User not found: $uid", 6, $this->id, 400);
        }

        if (!empty($active = $this->val('active', true))) {
            $active = $active === 'true' ? true : ($active === 'false' ? false : $active);
            $user->setActive((boolean) $active ? 1 : 0);
        }
        if (!empty($username = $this->val('username', true)) && $user->getUsername() != $username) {
            $userCheck = $this->userMapper->findByUsername($username);
            if (!empty($userCheck->getUid())) {
                throw new Core\ApiException("Username $username already exists", 6, $this->id, 400);
            }
            $user->setUsername($username);
        }
        if (!empty($email = $this->val('email', true)) && $user->getEmail() != $email) {
            $userCheck = $this->userMapper->findByEmail($email);
            if (!empty($userCheck->getUid())) {
                throw new Core\ApiException("Email $email already exists", 6, $this->id, 400);
            }
            $user->setEmail($email);
        }
        if (!empty($password = $this->val('password', true))) {
            $user->setPassword($password);
        }
        if (!empty($honorific = $this->val('honorific', true))) {
            $user->setHonorific($honorific);
        }
        if (!empty($nameFirst = $this->val('name_first', true))) {
            $user->setNameFirst($nameFirst);
        }
        if (!empty($nameLast = $this->val('name_last', true))) {
            $user->setNameLast($nameLast);
        }
        if (!empty($company = $this->val('company', true))) {
            $user->setCompany($company);
        }
        if (!empty($website = $this->val('website', true))) {
            $user->setWebsite($website);
        }
        if (!empty($addressStreet = $this->val('address_street', true))) {
            $user->setAddressStreet($addressStreet);
        }
        if (!empty($addressSuburb = $this->val('address_suburb', true))) {
            $user->setAddressSuburb($addressSuburb);
        }
        if (!empty($addressCity = $this->val('address_city', true))) {
            $user->setAddressCity($addressCity);
        }
        if (!empty($addressState = $this->val('address_state', true))) {
            $user->setAddressState($addressState);
        }
        if (!empty($addressCountry = $this->val('address_country', true))) {
            $user->setAddressCountry($addressCountry);
        }
        if (!empty($addressPostcode = $this->val('address_postcode', true))) {
            $user->setAddressPostcode($addressPostcode);
        }
        if (!empty($phoneMobile = $this->val('phone_mobile', true))) {
            $user->setPhoneMobile($phoneMobile);
        }
        if (!empty($phoneWork = $this->val('phone_work', true))) {
            $user->setPhoneWork($phoneWork);
        }

        $this->userMapper->save($user);

        return new Core\DataContainer($user->dump(), 'array');
    }
}

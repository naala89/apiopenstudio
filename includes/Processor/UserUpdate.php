<?php

/**
 * Class UserUpdate.
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
use ApiOpenStudio\Core\DataContainer;
use ApiOpenStudio\Core\MonologWrapper;
use ApiOpenStudio\Core\ProcessorEntity;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Core\Utilities;
use ApiOpenStudio\Db\UserMapper;
use ApiOpenStudio\Db\UserRoleMapper;

/**
 * Class UserUpdate
 *
 * Processor class to update a user.
 */
class UserUpdate extends ProcessorEntity
{
    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User update',
        'machineName' => 'user_update',
        'description' => 'Update a user.',
        'menu' => 'Admin',
        'input' => [
            'uid' => [
                'description' => 'The user ID of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'username' => [
                'description' => 'The username of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'active' => [
                'description' => 'The active flag for the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['boolean'],
                'limitValues' => [],
                'default' => true,
            ],
            'password' => [
                'description' => 'The password of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'honorific' => [
                'description' => 'The honorific of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['Mr', 'Ms', 'Miss', 'Mrs', 'Dr', 'Prof', 'Hon'],
                'default' => null,
            ],
            'name_first' => [
                'description' => 'The first name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'name_last' => [
                'description' => 'The last name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'company' => [
                'description' => 'The company of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'website' => [
                'description' => 'The website of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_street' => [
                'description' => 'The street address of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_suburb' => [
                'description' => 'The suburb of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_city' => [
                'description' => 'The city of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_state' => [
                'description' => 'The state of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_country' => [
                'description' => 'The country of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => null,
            ],
            'address_postcode' => [
                'description' => 'The postcode of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => null,
            ],
            'phone_mobile' => [
                'description' => 'The mobile phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => 0,
            ],
            'phone_work' => [
                'description' => 'The work phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => 0,
            ],
        ],
    ];

    /**
     * User mapper class.
     *
     * @var UserMapper
     */
    private UserMapper $userMapper;

    /**
     * User role mapper class.
     *
     * @var UserRoleMapper
     */
    private UserRoleMapper $userRoleMapper;

    /**
     * {@inheritDoc}
     */
    public function __construct(array &$meta, Request &$request, ?ADOConnection $db, ?MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new UserMapper($db, $logger);
        $this->userRoleMapper = new UserRoleMapper($db, $logger);
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

        $uid = $this->val('uid', true);
        try {
            $currentUser = $this->userMapper->findByUid(Utilities::getClaimFromToken('uid'));
            $testAdministrator = $this->userRoleMapper->hasRole($currentUser->getUid(), 'Administrator');
            $testAccountManager = $this->userRoleMapper->hasRole($currentUser->getUid(), 'Account manager');
            $testApplicationManager = $this->userRoleMapper->hasRole($currentUser->getUid(), 'Application manager');
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!$testAdministrator && !$testAccountManager && !$testApplicationManager) {
            // Non-privileged accounts can only edit their own accounts.
            if (!empty($uid) && $uid != $currentUser->getUid()) {
                throw new ApiException("permission denied", 4, $this->id, 403);
            }
            $uid = $currentUser->getUid();
        }

        try {
            $user = $this->userMapper->findByUid($uid);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (empty($user->getUid())) {
            throw new ApiException("User not found: $uid", 6, $this->id, 400);
        }

        $active = (int) $this->val('active', true);
        if ($active === 0 || $active === 1) {
            $user->setActive($active);
        }

        if (!empty($username = $this->val('username', true)) && $user->getUsername() != $username) {
            try {
                $userCheck = $this->userMapper->findByUsername($username);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (!empty($userCheck->getUid())) {
                throw new ApiException("Username $username already exists", 6, $this->id, 400);
            }
            $user->setUsername($username);
        }

        if (!empty($email = $this->val('email', true)) && $user->getEmail() != $email) {
            try {
                $userCheck = $this->userMapper->findByEmail($email);
            } catch (ApiException $e) {
                throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
            }
            if (!empty($userCheck->getUid())) {
                throw new ApiException("Email $email already exists", 6, $this->id, 400);
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

        if (!$this->userMapper->save($user)) {
            throw new ApiException('failed to update the new user, please check the logs', 6, $this->id, 400);
        }

        return new DataContainer($user->dump(), 'array');
    }
}

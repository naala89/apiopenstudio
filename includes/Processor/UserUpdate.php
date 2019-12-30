<?php

/**
 * User update.
 */

namespace Gaterdata\Processor;

use Gaterdata\Core;
use Gaterdata\Db;

class UserUpdate extends Core\ProcessorEntity
{
    /**
     * @var UserMapper
     */
    private $userMapper;

    /**
     * {@inheritDoc}
     */
    protected $details = [
        'name' => 'User update',
        'machineName' => 'user_update',
        'description' => 'Update a user.',
        'menu' => 'Admin',
        'input' => [
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
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitFunctions' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [1, 1],
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
                'limitTypes' => ['text'],
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
     * {@inheritDoc}
     */
    public function __construct($meta, &$request, $db)
    {
        parent::__construct($meta, $request, $db);
        $this->userMapper = new Db\UserMapper($db);
    }

    /**
     * {@inheritDoc}
     */
    public function process()
    {
        Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

        $uid = $this->val('uid', true);
        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $password = $this->val('password', true);

        $user = $this->userMapper->findByUsername($username);
        if ($user->getUid() != $uid) {
            throw new Core\ApiException("Username $username already exists", 6, $this->id, 400);
        }
        $user = $this->userMapper->findByEmail($email);
        if (!empty($user->getUid()) && $user->getUid() != $uid) {
            throw new Core\ApiException("Email $email already exists", 6, $this->id, 400);
        }
        $user = $this->userMapper->findByUid($uid);
        if (!empty($user->getUid()) && $user->getUid() != $uid) {
            throw new Core\ApiException("Invalid UID: $uid", 6, $this->id, 400);
        }

        $active = $this->val('active', true);
        $bool = ($active === 'true') ? true : ($active === 'false' ? false : $active);
        $user->setActive((boolean) $bool ? 1 : 0);
        $user->setUsername($username);
        if (!empty($password)) {
            $user->setPassword($password);
        }
        $user->setEmail($email);
        $user->setHonorific($this->val('honorific', true));
        $user->setNameFirst($this->val('name_first', true));
        $user->setNameLast($this->val('name_last', true));
        $user->setCompany($this->val('company', true));
        $user->setWebsite($this->val('website', true));
        $user->setAddressStreet($this->val('address_street', true));
        $user->setAddressSuburb($this->val('address_suburb', true));
        $user->setAddressCity($this->val('address_city', true));
        $user->setAddressState($this->val('address_state', true));
        $user->setAddressCountry($this->val('address_country', true));
        $user->setAddressPostcode($this->val('address_postcode', true));
        $user->setPhoneMobile($this->val('phone_mobile', true));
        $user->setPhoneWork($this->val('phone_work', true));

        $this->userMapper->save($user);

        return new Core\DataContainer($user->dump(), 'array');
    }
}

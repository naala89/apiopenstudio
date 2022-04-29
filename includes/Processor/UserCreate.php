<?php

/**
 * Class UserCreate.
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
use ApiOpenStudio\Core;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\Request;
use ApiOpenStudio\Db;

/**
 * Class UserCreate
 *
 * Processor class to create a user.
 */
class UserCreate extends Core\ProcessorEntity
{
    /**
     * User mapper class.
     *
     * @var Db\UserMapper
     */
    private Db\UserMapper $userMapper;

    /**
     * {@inheritDoc}
     *
     * @var array Details of the processor.
     */
    protected array $details = [
        'name' => 'User create',
        'machineName' => 'user_create',
        'description' => 'Create a user.',
        'menu' => 'Admin',
        'input' => [
            'username' => [
                'description' => 'The username of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'email' => [
                'description' => 'The email of the user.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'password' => [
                'description' => 'The password of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
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
            'honorific' => [
                'description' => 'The honorific of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => ['Mr', 'Ms', 'Miss', 'Mrs', 'Dr', 'Prof', 'Hon'],
                'default' => '',
            ],
            'name_first' => [
                'description' => 'The first name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'name_last' => [
                'description' => 'The last name of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'company' => [
                'description' => 'The company of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'website' => [
                'description' => 'The website of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_street' => [
                'description' => 'The street address of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_suburb' => [
                'description' => 'The suburb of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_city' => [
                'description' => 'The city of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_state' => [
                'description' => 'The state of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_country' => [
                'description' => 'The country of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'address_postcode' => [
                'description' => 'The postcode of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text'],
                'limitValues' => [],
                'default' => '',
            ],
            'phone_mobile' => [
                'description' => 'The mobile phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
            'phone_work' => [
                'description' => 'The work phone of the user.',
                'cardinality' => [0, 1],
                'literalAllowed' => true,
                'limitProcessors' => [],
                'limitTypes' => ['text', 'integer'],
                'limitValues' => [],
                'default' => '',
            ],
        ],
    ];

    /**
     * UserCreate constructor.
     *
     * @param mixed $meta Output meta.
     * @param Request $request Request object.
     * @param ADOConnection $db DB object.
     * @param Core\MonologWrapper $logger Logger object.
     */
    public function __construct($meta, Request &$request, ADOConnection $db, Core\MonologWrapper $logger)
    {
        parent::__construct($meta, $request, $db, $logger);
        $this->userMapper = new Db\UserMapper($db, $logger);
    }

    /**
     * {@inheritDoc}
     *
     * @return Core\DataContainer Result of the processor.
     *
     * @throws Core\ApiException Exception if invalid result.
     */
    public function process(): Core\DataContainer
    {
        parent::process();

        $username = $this->val('username', true);
        $email = $this->val('email', true);
        $password = $this->val('password', true);

        try {
            $user = $this->userMapper->findByUsername($username);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($user->getUid())) {
            throw new Core\ApiException("Username $username already exists", 6, $this->id, 400);
        }

        try {
            $user = $this->userMapper->findByEmail($email);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }
        if (!empty($user->getUid())) {
            throw new Core\ApiException("Email $email already exists", 6, $this->id, 400);
        }

        $user->setUsername($username);
        $user->setEmail($email);
        if (!empty($password)) {
            $user->setPassword($password);
        }
        $active = $this->val('active', true);
        $bool = ($active === 'true') ? true : ($active === 'false' ? false : $active);
        $user->setActive($bool);
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
        $user->setPasswordReset();
        $user->setPasswordResetTtl();

        try {
            $this->userMapper->save($user);
            $user = $this->userMapper->findByUsername($username);
        } catch (ApiException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $this->id, $e->getHtmlCode());
        }

        return new Core\DataContainer($user->dump(), 'array');
    }
}

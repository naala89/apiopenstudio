<?php

/**
 * User create.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class UserCreate extends Core\ProcessorEntity
{
  /**
   * {@inheritDoc}
   */
  protected $details = [
    'name' => 'User create',
    'machineName' => 'user_create',
    'description' => 'Create a user.',
    'menu' => 'Admin',
    'input' => [
      'username' => [
        'description' => 'The username of the user.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'password' => [
        'description' => 'The password of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
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
        'default' => TRUE,
      ],
      'honorific' => [
        'description' => 'The honorific of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => ['Mr', 'Ms', 'Miss', 'Mrs', 'Dr', 'Prof', 'Hon'],
        'default' => '',
      ],
      'name_first' => [
        'description' => 'The first name of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'name_last' => [
        'description' => 'The last name of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'email' => [
        'description' => 'The email of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'company' => [
        'description' => 'The company of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'website' => [
        'description' => 'The website of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_street' => [
        'description' => 'The street address of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_suburb' => [
        'description' => 'The suburb of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_city' => [
        'description' => 'The city of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_state' => [
        'description' => 'The state of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_country' => [
        'description' => 'The country of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'address_postcode' => [
        'description' => 'The postcode of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'phone_mobile' => [
        'description' => 'The mobile phone of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
      'phone_work' => [
        'description' => 'The work phone of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => '',
      ],
    ],
  ];

  /**
   * {@inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $username = $this->val('username', TRUE);
    $password = $this->val('password', TRUE);
    $active = $this->val('active', TRUE);
    $honorific = $this->val('honorific', TRUE);
    $nameFirst = $this->val('name_first', TRUE);
    $nameLast = $this->val('name_last', TRUE);
    $email = $this->val('email', TRUE);
    $company = $this->val('company', TRUE);
    $website = $this->val('website', TRUE);
    $addressStreet = $this->val('address_street', TRUE);
    $addressSuburb = $this->val('address_suburb', TRUE);
    $addressCity = $this->val('address_city', TRUE);
    $addressState = $this->val('address_state', TRUE);
    $addressCountry = $this->val('address_country', TRUE);
    $addressPostcode = $this->val('address_postcode', TRUE);
    $phoneMobile = $this->val('phone_mobile', TRUE);
    $phoneWork = $this->val('phone_work', TRUE);

    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUsername($username);
    if (!empty($user->getUid())) {
      throw new Core\ApiException("Username $username already exists", 6, $this->id, 400);
    }
    $user = $userMapper->findByEmail($email);
    if (!empty($user->getUid())) {
      throw new Core\ApiException("Email $email already exists", 6, $this->id, 400);
    }

    $user->setUsername($username);
    if (!empty($password)) {
      $user->setPassword($password);
    }
    $user->setActive($active ? 1 : 0);
    $user->setHonorific($honorific);
    $user->setNameFirst($nameFirst);
    $user->setNameLast($nameLast);
    $user->setEmail($email);
    $user->setCompany($company);
    $user->setWebsite($website);
    $user->setAddressStreet($addressStreet);
    $user->setAddressSuburb($addressSuburb);
    $user->setAddressCity($addressCity);
    $user->setAddressState($addressState);
    $user->setAddressCountry($addressCountry);
    $user->setAddressPostcode($addressPostcode);
    $user->setPhoneMobile($phoneMobile);
    $user->setPhoneWork($phoneWork);

    $userMapper->save($user);
    $user = $userMapper->findByUsername($username);
    return $user->dump();
  }
}

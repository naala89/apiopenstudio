<?php

/**
 * User update.
 */

namespace Gaterdata\Processor;
use Gaterdata\Core;
use Gaterdata\Db;

class UserUpdate extends Core\ProcessorEntity
{
  protected $details = [
    'name' => 'User update',
    'machineName' => 'user_update',
    'description' => 'Update a single user.',
    'menu' => 'Admin',
    'application' => 'Admin',
    'input' => [
      'uid' => [
        'description' => 'The user ID of the user.',
        'cardinality' => [1, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['integer'],
        'limitValues' => [],
        'default' => ''
      ],
      'username' => [
        'description' => 'The username of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'honorific' => [
        'description' => 'The honorific of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'name_first' => [
        'description' => 'The first name of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'name_last' => [
        'description' => 'The last name of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'email' => [
        'description' => 'The email of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'company' => [
        'description' => 'The company of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'website' => [
        'description' => 'The website of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'street_address' => [
        'description' => 'The street address of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'suburb' => [
        'description' => 'The suburb of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'city' => [
        'description' => 'The city of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'state' => [
        'description' => 'The state of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'country' => [
        'description' => 'The country of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'postcode' => [
        'description' => 'The postcode of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'phone_mobile' => [
        'description' => 'The mobile phone of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
      'phone_work' => [
        'description' => 'The work phone of the user.',
        'cardinality' => [0, 1],
        'literalAllowed' => true,
        'limitFunctions' => [],
        'limitTypes' => ['string'],
        'limitValues' => [],
        'default' => ''
      ],
    ],
  ];

  /**
   * {inheritDoc}
   */
  public function process()
  {
    Core\Debug::variable($this->meta, 'Processor ' . $this->details()['machineName'], 2);

    $details = [
      'Uid' => $this->val('uid', TRUE),
      'Username' => $this->val('username', TRUE),
      'Honorific' => $this->val('honorific', TRUE),
      'Namefirst' => $this->val('name_first', TRUE),
      'Namelast' => $this->val('name_last', TRUE),
      'Email' => $this->val('email', TRUE),
      'Company' => $this->val('company', TRUE),
      'Website' => $this->val('website', TRUE),
      'AddressStreet' => $this->val('street_address', TRUE),
      'AddressSuburb' => $this->val('suburb', TRUE),
      'AddressCity' => $this->val('city', TRUE),
      'AddressState' => $this->val('state', TRUE),
      'AddressCountry' => $this->val('country', TRUE),
      'AddressPostcode' => $this->val('postcode', TRUE),
      'PhoneMobile' => $this->val('phone_mobile', TRUE),
      'PhoneWork' => $this->val('phone_work', TRUE),
    ];

    if (empty($details['Uid'])) {
      throw new Core\ApiException('Missing UID', 6, $this->id, 400);
    }

    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUid($details['Uid']);
    if (empty($user->getUid())) {
      throw new Core\ApiException('Invalid UID' . $details['Uid'], 6, $this->id, 400);
    }

    foreach ($details as $key => $value) {
      if (!empty($value)) {
        $function = "set$key";
        $user->$function($value);
      }
    }

    $userMapper->save($user);
      $user = $userMapper->findByUid($details['Uid']);
      return $user->dump();

  }
}

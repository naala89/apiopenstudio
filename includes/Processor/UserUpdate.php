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
      'user_details' => [
        'description' => 'The user details to edit. A json string is expected.',
        'cardinality' => [1, 1],
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

    $user_details = json_decode($this->val('user_details', TRUE));
    if ($user_details == NULL) {
      throw new Core\ApiException('Invalid user details JSON string', 6, $this->id, 400);
    }

    $uid = $this->val('uid', TRUE);
    $userMapper = new Db\UserMapper($this->db);
    $user = $userMapper->findByUid($uid);
    if (empty($user->getUid())) {
      throw new Core\ApiException("Invalid UID: $uid", 6, $this->id, 400);
    }

    if (!empty($user_details->username)) {
      $user->setUsername($user_details->username);
    }
    if (!empty($user_details->password)) {
      $user->setPassword($user_details->password);
    }
    if (!empty($user_details->email)) {
      $user->setEmail($user_details->email);
    }
    if (!empty($user_details->honorific)) {
      $user->setHonorific($user_details->honorific);
    }
    if (!empty($user_details->name_first)) {
      $user->setNameFirst($user_details->name_first);
    }
    if (!empty($user_details->name_last)) {
      $user->setNameLast($user_details->name_last);
    }
    if (!empty($user_details->company)) {
      $user->setCompany($user_details->company);
    }
    if (!empty($user_details->website)) {
      $user->setWebsite($user_details->website);
    }
    if (!empty($user_details->street_address)) {
      $user->setAddressStreet($user_details->address_street);
    }
    if (!empty($user_details->suburb)) {
      $user->setAddressSuburb($user_details->address_suburb);
    }
    if (!empty($user_details->city)) {
      $user->setAddressCity($user_details->address_city);
    }
    if (!empty($user_details->country)) {
      $user->setAddressCountry($user_details->address_country);
    }
    if (!empty($user_details->postcode)) {
      $user->setAddressPostcode($user_details->address_postcode);
    }
    if (!empty($user_details->phone_mobile)) {
      $user->setPhoneMobile($user_details->phone_mobile);
    }
    if (!empty($user_details->phone_work)) {
      $user->setPhoneWork($user_details->phone_work);
    }
    Core\Debug::variable($user);
    $userMapper->save($user);

    $user = $userMapper->findByUid($uid);
    return $user->dump();
  }
}

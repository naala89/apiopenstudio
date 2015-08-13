<?php

/**
 * Fetch and save user data.
 */

namespace Datagator\Db;
use Datagator\Core;

class UserMapper
{
  protected $db;

  /**
   * @param $dbLayer
   */
  public function __construct($dbLayer)
  {
    $this->db = $dbLayer;
  }

  /**
   * @param \Datagator\Db\User $user
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function save(User $user)
  {
    if ($user->getUid() == NULL) {
      $sql = 'INSERT INTO `user` (`active`, `honorific`, `name_first`, `name_last`, `company`, `website`, `address_street`, `address_suburb`, `address_city`, `address_state`, `address_postcode`, `phone_mobile`, `phone_work`, `email`, `password`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, )';
      $bindParams = array(
        $user->getActive(),
        $user->getHonorific(),
        $user->getNameFirst(),
        $user->getNameLast(),
        $user->getCompany(),
        $user->getWebsite(),
        $user->getAddressStreet(),
        $user->getAddressSuburb(),
        $user->getAddressCity(),
        $user->getAddressState(),
        $user->getAddressPostcode(),
        $user->getPhoneMobile(),
        $user->getPhoneWork(),
        $user->getEmail(),
        $user->getPassword()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE `user` SET `active`=?, `honorific`=?, `name_first`=?, `name_last`=?, `company`=?, `website`=?, `address_street`=?, `address_suburb`=?, `address_city`=?, `address_state`=?, `address_postcode`=?, `phone_mobile`=?, `phone_work`=?, `email`=?, `password`=?)  WHERE `uid`=?)';
      $bindParams = array(
        $user->getActive(),
        $user->getHonorific(),
        $user->getNameFirst(),
        $user->getNameLast(),
        $user->getCompany(),
        $user->getWebsite(),
        $user->getAddressStreet(),
        $user->getAddressSuburb(),
        $user->getAddressCity(),
        $user->getAddressState(),
        $user->getAddressPostcode(),
        $user->getPhoneMobile(),
        $user->getPhoneWork(),
        $user->getEmail(),
        $user->getPassword(),
        $user->getUid()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg());
    }
    return TRUE;
  }

  /**
   * @param $uid
   * @return \Datagator\Db\User
   */
  public function findByUid($uid)
  {
    $sql = 'SELECT * FROM `user` WHERE `uid` = ?';
    $bindParams = array($uid);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $email
   * @return \Datagator\Db\User
   */
  public function findByEmail($email)
  {
    $sql = 'SELECT * FROM `users` WHERE `email` = ?';
    $bindParams = array($email);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param array $row
   * @return \Datagator\Db\User
   */
  protected function mapArray(array $row)
  {
    $user = new User();

    $user->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $user->setCid(!empty($row['cid']) ? $row['cid'] : NULL);
    $user->setEmail(!empty($row['email']) ? $row['email'] : NULL);
    $user->setPassword(!empty($row['password']) ? $row['password'] : NULL);
    $user->setActive(!empty($row['active']) ? $row['active'] : NULL);

    return $user;
  }
}

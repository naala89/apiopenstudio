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
    if (empty($user->getUid())) {
      $sql = 'INSERT INTO user (active, username, salt, hash, token, token_ttl, email, honorific, name_first, name_last, company, website, address_street, address_suburb, address_city, address_state, address_country, address_postcode, phone_mobile, phone_work) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
      $bindParams = array(
        $user->getActive(),
        $user->getUsername(),
        $user->getSalt(),
        $user->getHash(),
        $user->getToken(),
        $user->getTokenTtl(),
        $user->getEmail(),
        $user->getHonorific(),
        $user->getNameFirst(),
        $user->getNameLast(),
        $user->getCompany(),
        $user->getWebsite(),
        $user->getAddressStreet(),
        $user->getAddressSuburb(),
        $user->getAddressCity(),
        $user->getAddressState(),
        $user->getAddressCountry(),
        $user->getAddressPostcode(),
        $user->getPhoneMobile(),
        $user->getPhoneWork()
      );
      $result = $this->db->Execute($sql, $bindParams);
    } else {
      $sql = 'UPDATE user SET active=?, username=?, salt=?, hash=?, token=?, token_ttl=?, email=?, honorific=?, name_first=?, name_last=?, company=?, website=?, address_street=?, address_suburb=?, address_city=?, address_state=?, address_country=?, address_postcode=?, phone_mobile=?, phone_work=?  WHERE uid=?';
      $bindParams = array(
        $user->getActive(),
        $user->getUsername(),
        $user->getSalt(),
        $user->getHash(),
        $user->getToken(),
        $user->getTokenTtl(),
        $user->getEmail(),
        $user->getHonorific(),
        $user->getNameFirst(),
        $user->getNameLast(),
        $user->getCompany(),
        $user->getWebsite(),
        $user->getAddressStreet(),
        $user->getAddressSuburb(),
        $user->getAddressCity(),
        $user->getAddressState(),
        $user->getAddressCountry(),
        $user->getAddressPostcode(),
        $user->getPhoneMobile(),
        $user->getPhoneWork(),
        $user->getUid()
      );
      $result = $this->db->Execute($sql, $bindParams);
    }
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return TRUE;
  }

  /**
   * @param \Datagator\Db\User $user
   * @return bool
   * @throws \Datagator\Core\ApiException
   */
  public function delete(User $user)
  {
    $sql = 'DELETE FROM user WHERE uid = ?';
    $bindParams = array($user->getUid());
    $result = $this->db->Execute($sql, $bindParams);
    if (!$result) {
      throw new Core\ApiException($this->db->ErrorMsg(), 2);
    }
    return true;
  }

  /**
   * @param $uid
   * @return \Datagator\Db\User
   */
  public function findByUid($uid)
  {
    $sql = 'SELECT * FROM user WHERE uid = ?';
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
    $sql = 'SELECT * FROM user WHERE email = ?';
    $bindParams = array($email);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $username
   * @return \Datagator\Db\User
   */
  public function findByUsername($username)
  {
    $sql = 'SELECT * FROM user WHERE username = ?';
    $bindParams = array($username);
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $token
   * @return \Datagator\Db\User
   */
  public function findBytoken($token)
  {
    $sql = 'SELECT * FROM user WHERE token = ? AND token_ttl > ?';
    $bindParams = array($token, Core\Utilities::mysqlNow());
    $row = $this->db->GetRow($sql, $bindParams);
    return $this->mapArray($row);
  }

  /**
   * @param $uid
   * @param $appId
   * @param $rid
   * @return bool
   */
  public function hasRole($uid, $appId, $rid)
  {
    $sql = 'SELECT u.* FROM user AS u INNER JOIN user_role AS ur ON u.uid=ur.uid WHERE u.uid=? AND ur.appid=? AND ur.rid=?';
    $bindParams = array($uid, $appId, $rid);
    $row = $this->db->GetRow($sql, $bindParams);
    return !empty($row['uid']);
  }

  /**
   * @param array $row
   * @return \Datagator\Db\User
   */
  protected function mapArray($row)
  {
    $user = new User();
    $user->setUid(!empty($row['uid']) ? $row['uid'] : NULL);
    $user->setActive(!empty($row['active']) ? $row['active'] : NULL);
    $user->setUsername(!empty($row['username']) ? $row['username'] : NULL);
    $user->setSalt(!empty($row['salt']) ? $row['salt'] : NULL);
    $user->setHash(!empty($row['hash']) ? $row['hash'] : NULL);
    $user->setToken(!empty($row['token']) ? $row['token'] : NULL);
    $user->setTokenTtl(!empty($row['token_ttl']) ? $row['token_ttl'] : NULL);
    $user->setEmail(!empty($row['email']) ? $row['email'] : NULL);
    $user->setHonorific(!empty($row['honorific']) ? $row['honorific'] : NULL);
    $user->setNameFirst(!empty($row['name_first']) ? $row['name_first'] : NULL);
    $user->setNameLast(!empty($row['name_last']) ? $row['name_last'] : NULL);
    $user->setCompany(!empty($row['company']) ? $row['company'] : NULL);
    $user->setWebsite(!empty($row['website']) ? $row['website'] : NULL);
    $user->setAddressStreet(!empty($row['address_street']) ? $row['address_street'] : NULL);
    $user->setAddressSuburb(!empty($row['address_suburb']) ? $row['address_suburb'] : NULL);
    $user->setAddressCity(!empty($row['address_city']) ? $row['address_city'] : NULL);
    $user->setAddressState(!empty($row['address_state']) ? $row['address_state'] : NULL);
    $user->setAddressCountry(!empty($row['address_country']) ? $row['address_country'] : NULL);
    $user->setAddressPostcode(!empty($row['address_postcode']) ? $row['address_postcode'] : NULL);
    $user->setPhoneMobile(!empty($row['phone_mobile']) ? $row['phone_mobile'] : NULL);
    $user->setPhoneWork(!empty($row['phone_work']) ? $row['phone_work'] : NULL);
    return $user;
  }
}

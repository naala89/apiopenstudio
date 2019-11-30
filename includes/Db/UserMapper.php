<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use Gaterdata\Core\Debug;
use Gaterdata\Core\Utilities;

/**
 * Class UserMapper.
 *
 * @package Gaterdata\Db
 */
class UserMapper extends Mapper {

  /**
   * Save the user.
   *
   * @param User $user
   *   User object.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
    public function save(User $user)
    {
        if (empty($user->getUid())) {
            $sql = 'INSERT INTO user (active, username, hash, token, token_ttl, email, honorific, name_first, ';
            $sql .= 'name_last, company, website, address_street, address_suburb, address_city, address_state, ';
            $sql .= 'address_country, address_postcode, phone_mobile, phone_work) VALUES ';
            $sql .= '(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $bindParams = [
            $user->getActive(),
            $user->getUsername(),
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
            ];
        } else {
            $sql = 'UPDATE user SET active=?, username=?, hash=?, token=?, token_ttl=?, email=?, honorific=?, ';
            $sql .= 'name_first=?, name_last=?, company=?, website=?, address_street=?, address_suburb=?, ';
            $sql .= 'address_city=?, address_state=?, address_country=?, address_postcode=?, phone_mobile=?, ';
            $sql .= 'phone_work=?  WHERE uid=?';
            $bindParams = [
            $user->getActive(),
            $user->getUsername(),
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
            $user->getUid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

  /**
   * Delete a user.
   *
   * @param User $user
   *   The user object.
   *
   * @return bool
   *   Success.
   *
   * @throws ApiException
   */
    public function delete(User $user)
    {
        $sql = 'DELETE FROM user WHERE uid = ?';
        $bindParams = [$user->getUid()];
        return $this->saveDelete($sql, $bindParams);
    }

  /**
   * Find all user.
   *
   * @param array $params
   *   @see \Gaterdata\Db\Mapper.
   *
   * @return array
   *   $array of Users.
   *
   * @throws ApiException
   */
    public function findAll($params = [])
    {
        $sql = 'SELECT * FROM user';
        $bindParams = [];
        return $this->fetchRows($sql, $bindParams, $params);
    }

  /**
   * Find a user by user ID.
   *
   * @param int $uid
   *   User ID.
   *
   * @return User
   *   User object.
   *
   * @throws ApiException
   */
    public function findByUid($uid)
    {
        $sql = 'SELECT * FROM user WHERE uid = ?';
        $bindParams = [$uid];
        return $this->fetchRow($sql, $bindParams);
    }

  /**
   * Find a user by email address.
   *
   * @param string $email
   *   Users email.
   *
   * @return User
   *   User object.
   *
   * @throws ApiException
   */
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM user WHERE email = ?';
        $bindParams = [$email];
        return $this->fetchRow($sql, $bindParams);
    }

  /**
   * Find user bu username.
   *
   * @param string $username
   *   Users usdername.
   *
   * @return User
   *   User object.
   *
   * @throws ApiException
   */
    public function findByUsername($username)
    {
        $sql = 'SELECT * FROM user WHERE username = ?';
        $bindParams = [$username];
        return $this->fetchRow($sql, $bindParams);
    }

  /**
   * Find a user by their auth token.
   *
   * @param string $token
   *   User auth token.
   *
   * @return User
   *   User object.
   *
   * @throws ApiException
   */
    public function findBytoken($token)
    {
        $sql = 'SELECT * FROM user WHERE token = ? AND token_ttl > ?';
        $bindParams = [$token, Utilities::mysqlNow()];
        return $this->fetchRow($sql, $bindParams);
    }

  /**
   * Map a DB row into a User object.
   *
   * @param array $row
   *   DB row object.
   *
   * @return User
   *   Mapped User object.
   */
    protected function mapArray(array $row)
    {
        $user = new User();
        $user->setUid(!empty($row['uid']) ? $row['uid'] : null);
        $user->setActive(!empty($row['active']) ? $row['active'] : null);
        $user->setUsername(!empty($row['username']) ? $row['username'] : null);
        $user->setHash(!empty($row['hash']) ? $row['hash'] : null);
        $user->setToken(!empty($row['token']) ? $row['token'] : null);
        $user->setTokenTtl(!empty($row['token_ttl']) ? $row['token_ttl'] : null);
        $user->setEmail(!empty($row['email']) ? $row['email'] : null);
        $user->setHonorific(!empty($row['honorific']) ? $row['honorific'] : null);
        $user->setNameFirst(!empty($row['name_first']) ? $row['name_first'] : null);
        $user->setNameLast(!empty($row['name_last']) ? $row['name_last'] : null);
        $user->setCompany(!empty($row['company']) ? $row['company'] : null);
        $user->setWebsite(!empty($row['website']) ? $row['website'] : null);
        $user->setAddressStreet(!empty($row['address_street']) ? $row['address_street'] : null);
        $user->setAddressSuburb(!empty($row['address_suburb']) ? $row['address_suburb'] : null);
        $user->setAddressCity(!empty($row['address_city']) ? $row['address_city'] : null);
        $user->setAddressState(!empty($row['address_state']) ? $row['address_state'] : null);
        $user->setAddressCountry(!empty($row['address_country']) ? $row['address_country'] : null);
        $user->setAddressPostcode(!empty($row['address_postcode']) ? $row['address_postcode'] : null);
        $user->setPhoneMobile(!empty($row['phone_mobile']) ? $row['phone_mobile'] : null);
        $user->setPhoneWork(!empty($row['phone_work']) ? $row['phone_work'] : null);
        return $user;
    }

}

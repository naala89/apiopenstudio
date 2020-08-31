<?php
/**
 * Class UserMapper.
 *
 * @package Gaterdata
 * @subpackage Db
 * @author john89
 * @copyright 2020-2030 GaterData
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @link https://gaterdata.com
 */

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;

/**
 * Class UserMapper.
 *
 * Mapper class for DB calls used for the user table.
 */
class UserMapper extends Mapper
{
    /**
     * Save the user.
     *
     * @param User $user User object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(User $user)
    {
        if (empty($user->getUid())) {
            $sql = 'INSERT INTO user (active, username, hash, token, token_ttl, email, honorific, name_first, ';
            $sql .= 'name_last, company, website, address_street, address_suburb, address_city, address_state, ';
            $sql .= 'address_country, address_postcode, phone_mobile, phone_work, password_reset, password_reset_ttl)';
            $sql .= ' VALUES';
            $sql .= ' (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
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
                $user->getPasswordReset(),
                $user->getPasswordResetTtl(),
            ];
        } else {
            $sql = 'UPDATE user SET active=?, username=?, hash=?, token=?, token_ttl=?, email=?, honorific=?, ';
            $sql .= 'name_first=?, name_last=?, company=?, website=?, address_street=?, address_suburb=?, ';
            $sql .= 'address_city=?, address_state=?, address_country=?, address_postcode=?, phone_mobile=?, ';
            $sql .= 'phone_work=?, password_reset=?, password_reset_ttl=? WHERE uid=?';
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
                $user->getPasswordReset(),
                $user->getPasswordResetTtl(),
                $user->getUid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete a user.
     *
     * @param User $user The user object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(User $user)
    {
        $sql = 'DELETE FROM user WHERE uid = ?';
        $bindParams = [$user->getUid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find all users. If the calling uid does not have elevated access, only return that user.
     *
     * @param integer $uid User ID of the current user.
     * @param array $params Filter parameters.
     * @see \Gaterdata\Db\Mapper.
     *
     * @return array $array of Users.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAllByPermissions(int $uid, array $params = [])
    {
        $elevatedRoles = ["Administrator", "Account manager", "application manager"];
        $sql = 'SELECT *';
        $sql .= ' FROM user';
        $sql .= ' WHERE uid IN (';
        $sql .= ' SELECT uid';
        $sql .= ' FROM user';
        $sql .= ' WHERE EXISTS (';
        $sql .= ' SELECT *';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name IN ("' . implode('", "', $elevatedRoles) . '")';
        $sql .= ' )';
        $sql .= ' UNION DISTINCT';
        $sql .= ' SELECT uid';
        $sql .= ' FROM user';
        $sql .= ' WHERE uid = ?';
        $sql .= ' AND active = 1)';
        $bindParams = [$uid, $uid];
        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find allUsers
     *
     * @return array User objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll()
    {
        $sql = 'SELECT * FROM user';
        return $this->fetchRows($sql);
    }

    /**
     * Find a user by user ID.
     *
     * @param integer $uid User ID.
     *
     * @return User User object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid)
    {
        $sql = 'SELECT * FROM user WHERE uid = ?';
        $bindParams = [$uid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a user by email address.
     *
     * @param string $email Users email.
     *
     * @return User User object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByEmail(string $email)
    {
        $sql = 'SELECT * FROM user WHERE email = ?';
        $bindParams = [$email];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find user bu username.
     *
     * @param string $username Users usdername.
     *
     * @return User User object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUsername(string $username)
    {
        $sql = 'SELECT * FROM user WHERE username = ?';
        $bindParams = [$username];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a user by their auth token.
     *
     * @param string $token User auth token.
     *
     * @return User User object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findBytoken(string $token)
    {
        $sql = 'SELECT * FROM user WHERE token = ?';
        $bindParams = [$token];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a user by their password reset token.
     *
     * @param string $token User auth token.
     *
     * @return User User object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByPasswordToken(string $token)
    {
        $sql = 'SELECT * FROM user WHERE password_reset = ?';
        $bindParams = [$token];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Map a DB row into a User object.
     *
     * @param array $row DB row object.
     *
     * @return User Mapped User object.
     */
    protected function mapArray(array $row)
    {
        $user = new User();
        $user->setUid(!empty($row['uid']) ? $row['uid'] : 0);
        $user->setActive(!empty($row['active']) ? $row['active'] : 0);
        $user->setUsername(!empty($row['username']) ? $row['username'] : '');
        $user->setHash(!empty($row['hash']) ? $row['hash'] : '');
        $user->setToken(!empty($row['token']) ? $row['token'] : '');
        $user->setTokenTtl(!empty($row['token_ttl']) ? $row['token_ttl'] : '');
        $user->setEmail(!empty($row['email']) ? $row['email'] : '');
        $user->setHonorific(!empty($row['honorific']) ? $row['honorific'] : '');
        $user->setNameFirst(!empty($row['name_first']) ? $row['name_first'] : '');
        $user->setNameLast(!empty($row['name_last']) ? $row['name_last'] : '');
        $user->setCompany(!empty($row['company']) ? $row['company'] : '');
        $user->setWebsite(!empty($row['website']) ? $row['website'] : '');
        $user->setAddressStreet(!empty($row['address_street']) ? $row['address_street'] : '');
        $user->setAddressSuburb(!empty($row['address_suburb']) ? $row['address_suburb'] : '');
        $user->setAddressCity(!empty($row['address_city']) ? $row['address_city'] : '');
        $user->setAddressState(!empty($row['address_state']) ? $row['address_state'] : '');
        $user->setAddressCountry(!empty($row['address_country']) ? $row['address_country'] : '');
        $user->setAddressPostcode(!empty($row['address_postcode']) ? $row['address_postcode'] : '');
        $user->setPhoneMobile(!empty($row['phone_mobile']) ? $row['phone_mobile'] : '');
        $user->setPhoneWork(!empty($row['phone_work']) ? $row['phone_work'] : '');
        $user->setPasswordReset(!empty($row['password_reset']) ? $row['password_reset'] : '');
        $user->setPasswordResetTtl(!empty($row['password_reset_ttl']) ? $row['password_reset_ttl'] : '');
        return $user;
    }
}

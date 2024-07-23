<?php

/**
 * Class UserMapper.
 *
 * @package    ApiOpenStudio\Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the ApiOpenStudio Public License.
 *             If a copy of the license was not distributed with this file,
 *             You can obtain one at https://www.apiopenstudio.com/license/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\ApiException;

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
    public function save(User $user): bool
    {
        if (empty($user->getUid())) {
            $sql = 'INSERT INTO user (active, username, hash, email, honorific, name_first,';
            $sql .= ' name_last, company, website, address_street, address_suburb, address_city, address_state,';
            // phpcs:ignore
            $sql .= ' address_country, address_postcode, phone_mobile, phone_work, password_reset, password_reset_ttl, refresh_token)';
            $sql .= ' VALUES';
            $sql .= ' (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $bindParams = [
                $user->getActive(),
                $user->getUsername(),
                $user->getHash(),
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
                $user->getRefreshToken(),
            ];
        } else {
            $sql = 'UPDATE user SET active=?, username=?, hash=?, email=?, honorific=?, ';
            $sql .= 'name_first=?, name_last=?, company=?, website=?, address_street=?, address_suburb=?, ';
            $sql .= 'address_city=?, address_state=?, address_country=?, address_postcode=?, phone_mobile=?, ';
            $sql .= 'phone_work=?, password_reset=?, password_reset_ttl=?, refresh_token=? WHERE uid=?';
            $bindParams = [
                $user->getActive(),
                $user->getUsername(),
                $user->getHash(),
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
                $user->getRefreshToken(),
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
    public function delete(User $user): bool
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
     *
     * @return array $array of Users.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAllByPermissions(int $uid, array $params = []): array
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
     * Find all users
     *
     * @return array User objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(): array
    {
        $sql = 'SELECT * FROM user';
        return $this->fetchRows($sql);
    }

    /**
     * Find a user by user ID.
     *
     * @param integer $uid User ID.
     *
     * @return User User row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid): User
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
     * @return User User row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByEmail(string $email): User
    {
        $sql = 'SELECT * FROM user WHERE email = ?';
        $bindParams = [$email];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find user bu username.
     *
     * @param string $username Users username.
     *
     * @return User User row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByUsername(string $username): User
    {
        $sql = 'SELECT * FROM user WHERE username = ?';
        $bindParams = [$username];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find a user by their password reset token.
     *
     * @param string $token User auth token.
     *
     * @return User User row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByPasswordToken(string $token): User
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
    protected function mapArray(array $row): User
    {
        $user = new User();
        $user->setUid($row['uid'] ?? 0);
        $user->setActive($row['active'] ?? 0);
        $user->setUsername($row['username'] ?? '');
        $user->setHash($row['hash'] ?? null);
        $user->setRefreshToken($row['refresh_token'] ?? null);
        $user->setEmail($row['email'] ?? '');
        $user->setHonorific($row['honorific'] ?? null);
        $user->setNameFirst($row['name_first'] ?? null);
        $user->setNameLast($row['name_last'] ?? null);
        $user->setCompany($row['company'] ?? null);
        $user->setWebsite($row['website'] ?? null);
        $user->setAddressStreet($row['address_street'] ?? null);
        $user->setAddressSuburb($row['address_suburb'] ?? null);
        $user->setAddressCity($row['address_city'] ?? null);
        $user->setAddressState($row['address_state'] ?? null);
        $user->setAddressCountry($row['address_country'] ?? null);
        $user->setAddressPostcode($row['address_postcode'] ?? null);
        $user->setPhoneMobile($row['phone_mobile'] ?? null);
        $user->setPhoneWork($row['phone_work'] ?? null);
        $user->setPasswordReset($row['password_reset'] ?? null);
        $user->setPasswordResetTtl($row['password_reset_ttl'] ?? null);
        return $user;
    }
}

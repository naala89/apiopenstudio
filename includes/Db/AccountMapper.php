<?php

/**
 * Class AccountMapper.
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
 * Class AccountMapper.
 *
 * Mapper class for DB calls used for the account table.
 */
class AccountMapper extends Mapper
{
    /**
     * Save an Account.
     *
     * @param Account $account Account object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function save(Account $account): bool
    {
        if ($account->getAccid() == null) {
            $sql = 'INSERT INTO account (name) VALUES (?)';
            $bindParams = [$account->getName()];
        } else {
            $sql = 'UPDATE account SET name = ? WHERE accid = ?';
            $bindParams = [
                $account->getName(),
                $account->getAccid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete an account.
     *
     * @param Account $account Account object.
     *
     * @return boolean Success.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function delete(Account $account): bool
    {
        $sql = 'DELETE FROM account WHERE accid = ?';
        $bindParams = [$account->getAccid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find an accounts.
     *
     * @param array $params Filter parameters.
     *
     * @return array array Account objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = []): array
    {
        $sql = 'SELECT * FROM account';
        return $this->fetchRows($sql, [], $params);
    }

    /**
     * Find an account by ID.
     *
     * @param integer $accid Account ID.
     *
     * @return Account Account object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAccid(int $accid): Account
    {
        $sql = 'SELECT * FROM account WHERE accid = ?';
        $bindParams = [$accid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find accounts by IDs.
     *
     * @param array $accids Account Ids.
     *
     * @return array Array of Account objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByAccids(array $accids): array
    {
        $inAccid = [];
        for ($count = 0; $count < sizeof($accids); $count++) {
            $inAccid[] = '?';
        }
        $sql = 'SELECT * FROM account';
        if (!empty($inAccid)) {
            $sql .= ' WHERE accid IN (' . implode(', ', $inAccid) . ')';
        }
        return $this->fetchRows($sql, $accids);
    }

    /**
     * Find an account by name.
     *
     * @param string $name Account name.
     *
     * @return Account Account object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByName(string $name): Account
    {
        $sql = 'SELECT * FROM account WHERE name = ?';
        $bindParams = [$name];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find an accounts by names.
     *
     * @param array $names Account names.
     *
     * @return array array of Account object.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findByNames(array $names = []): array
    {
        $arr = [];
        for ($count = 0; $count < sizeof($names); $count++) {
            $arr[] = '?';
        }
        $sql = 'SELECT * FROM account WHERE name IN (' . implode(', ', $arr) . ')';
        return $this->fetchRows($sql, $names);
    }

    /**
     * Find an accounts by a user has roles for.
     *
     * @param integer $uid User ID.
     * @param array $params Filter parameters.
     *
     * @return array Array of Account objects.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    public function findAllForUser(int $uid, array $params = []): array
    {
        $sql = 'SELECT *';
        $sql .= ' FROM account';
        $sql .= ' WHERE accid';
        $sql .= ' IN (';
        $sql .= ' SELECT accid';
        $sql .= ' FROM account';
        $sql .= ' WHERE EXISTS';
        $sql .= ' (';
        $sql .= ' SELECT *';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name = "Administrator"';
        $sql .= ' )';
        $sql .= ' UNION DISTINCT';
        $sql .= ' SELECT a.accid';
        $sql .= ' FROM account AS a';
        $sql .= ' INNER JOIN user_role AS ur';
        $sql .= ' ON a.accid = ur.accid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' )';
        $bindParams = [$uid, $uid];
        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Map a DB row into an Account object.
     *
     * @param array $row DB row object.
     *
     * @return Account Account object.
     */
    protected function mapArray(array $row): Account
    {
        $account = new Account();

        $account->setAccid($row['accid'] ?? 0);
        $account->setName($row['name'] ?? '');

        return $account;
    }
}

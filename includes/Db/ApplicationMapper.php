<?php
/**
 * Class ApplicationMapper.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 ApiOpenStudio
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

/**
 * Class ApplicationMapper.
 *
 * Mapper class for DB calls used for the application table.
 */
class ApplicationMapper extends Mapper
{
    /**
     * Save an Application object.
     *
     * @param \ApiOpenStudio\Db\Application $application The Applicationm object.
     *
     * @return boolean Success.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function save(Application $application)
    {
        if ($application->getAppid() == null) {
            $sql = 'INSERT INTO application (accid, name) VALUES (?, ?)';
            $bindParams = [
            $application->getAccid(),
            $application->getName(),
            ];
        } else {
            $sql = 'UPDATE application SET accid = ?, name = ? WHERE appid = ?';
            $bindParams = [
            $application->getAccid(),
            $application->getName(),
            $application->getAppid(),
            ];
        }
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Delete an application.
     *
     * @param \ApiOpenStudio\Db\Application $application Application object.
     *
     * @return boolean Success.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function delete(Application $application)
    {
        $sql = 'DELETE FROM application WHERE appid = ?';
        $bindParams = [$application->getAppid()];
        return $this->saveDelete($sql, $bindParams);
    }

    /**
     * Find applications.
     *
     * @param array $params Filter params.
     *
     * @return array Array of Application objects.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findAll(array $params = [])
    {
        $sql = 'SELECT * FROM application';
        $bindParams = [];
        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find application by application ID.
     *
     * @param integer $appid Application ID.
     *
     * @return \ApiOpenStudio\Db\Application Application object.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAppid(int $appid)
    {
        $sql = 'SELECT * FROM application WHERE appid = ?';
        $bindParams = [$appid];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Fetch applications for a user.
     *
     * @param integer $uid User ID.
     * @param array $params Filter params.
     *
     * @return array Array of Application objects.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findByUid(int $uid, array $params = [])
    {
        $sql = 'SELECT *';
        $sql .= ' FROM application';
        $sql .= ' WHERE appid';
        $sql .= ' IN (';
        $sql .= ' SELECT appid';
        $sql .= ' FROM application';
        $sql .= ' WHERE EXISTS';
        $sql .= ' (SELECT *';
        $sql .= ' FROM user_role AS ur';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON ur.rid = r.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name = "Administrator")';
        $sql .= ' UNION DISTINCT';
        $sql .= ' SELECT app.appid appid';
        $sql .= ' FROM application AS app';
        $sql .= ' INNER JOIN user_role as ur';
        $sql .= ' ON ur.accid = app.accid';
        $sql .= ' INNER JOIN role AS r';
        $sql .= ' ON r.rid = ur.rid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND r.name = "Account manager"';
        $sql .= ' UNION DISTINCT';
        $sql .= ' SELECT app.appid appid';
        $sql .= ' FROM application AS app';
        $sql .= ' INNER JOIN user_role as ur';
        $sql .= ' ON ur.appid = app.appid';
        $sql .= ' WHERE ur.uid = ?';
        $sql .= ' AND ur.appid IS NOT NULL)';
        $bindParams = [$uid, $uid, $uid];

        return $this->fetchRows($sql, $bindParams, $params);
    }

    /**
     * Find application by account ID and application name.
     *
     * @param integer $accid Account ID.
     * @param string $name Application name.
     *
     * @return \ApiOpenStudio\Db\Application Application object.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAccidAppname(int $accid, string $name)
    {
        $sql = 'SELECT * FROM application WHERE accid = ? AND name = ?';
        $bindParams = [
        $accid,
        $name,
        ];
        return $this->fetchRow($sql, $bindParams);
    }

    /**
     * Find applications by account ID.
     *
     * @param integer $accid Account ID.
     *
     * @return array array of mapped Application objects.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAccid(int $accid)
    {
        $sql = 'SELECT * FROM application WHERE accid = ?';
        $bindParams = [$accid];
        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Find applications by multiple account IDs and/or application names.
     *
     * @param array $accids Array of account IDs.
     * @param array $appNames Array of account IDs.
     * @param array $params Parameters (optional).
     *
     * @return array array of mapped Application objects.
     *
     * @throws \ApiOpenStudio\Core\ApiException Return an ApiException on DB error.
     */
    public function findByAccidsAppnames(array $accids = [], array $appNames = [], array $params = [])
    {
        $byAccid = [];
        $bindParams = [];
        $where = [];
        $orderBy = '';
        $sql = 'SELECT * FROM application';

        foreach ($accids as $accid) {
            $byAccid[] = '?';
            $bindParams[] = $accid;
        }
        if (!empty($byAccid)) {
            $where[] = 'accid IN (' . implode(', ', $byAccid) . ')';
        }

        $byAppname = [];
        foreach ($appNames as $appName) {
            $byAppname[] = '?';
            $bindParams[] = $appName;
        }
        if (!empty($byAppname)) {
            $where[] = 'name IN (' . implode(', ', $byAppname) . ')';
        }

        if (!empty($params['filter']) && !empty($params['filter']['column']) && !empty($params['filter']['keyword'])) {
            $where[] = mysqli_real_escape_string($this->db->_connectionID, $params['filter']['column'])  . ' = ?';
            $bindParams[] = $params['filter']['keyword'];
        }

        if (!empty($params['keyword'])) {
            $where[] = 'name like ?';
            $bindParams[] = $params['keyword'];
        }
        if (!empty($params['order_by'])) {
            $orderBy .= ' ORDER BY ' . mysqli_real_escape_string($this->db->_connectionID, $params['order_by']);
            if (!empty($params['direction'])) {
                $orderBy .= ' ' . strtoupper(mysqli_real_escape_string($this->db->_connectionID, $params['direction']));
            }
        }
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= $orderBy;

        return $this->fetchRows($sql, $bindParams);
    }

    /**
     * Map a DB row into an Application object.
     *
     * @param array $row DB row object.
     *
     * @return \ApiOpenStudio\Db\Application Application object
     */
    protected function mapArray(array $row)
    {
        $application = new Application();

        $application->setAppid(!empty($row['appid']) ? $row['appid'] : 0);
        $application->setAccid(!empty($row['accid']) ? $row['accid'] : 0);
        $application->setName(!empty($row['name']) ? $row['name'] : '');

        return $application;
    }
}

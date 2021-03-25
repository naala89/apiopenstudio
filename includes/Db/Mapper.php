<?php

/**
 * Class Mapper.
 *
 * @package    ApiOpenStudio
 * @subpackage Db
 * @author     john89 (https://gitlab.com/john89)
 * @copyright  2020-2030 Naala Pty Ltd
 * @license    This Source Code Form is subject to the terms of the Mozilla Public License, v. 2.0.
 *             If a copy of the MPL was not distributed with this file,
 *             You can obtain one at https://mozilla.org/MPL/2.0/.
 * @link       https://www.apiopenstudio.com
 */

namespace ApiOpenStudio\Db;

use ApiOpenStudio\Core\ApiException;
use Cascade\Cascade;
use ADODB_mysqli;
use ApiOpenStudio\Core\Config;

/**
 * Abstract class Mapper.
 *
 * All derived Mapper classes but extend this class.
 */
abstract class Mapper
{
    /**
     * DB connector.
     *
     * @var ADODB_mysqli DB Instance.
     */
    protected $db;

    /**
     * Logger object.
     *
     * @var \Monolog\Logger
     */
    protected $logger;

    /**
     * Mapper constructor.
     *
     * @param ADODB_mysqli $dbLayer DB connection object.
     */
    public function __construct(ADODB_mysqli $dbLayer)
    {
        $this->db = $dbLayer;
        $config = new Config();
        Cascade::fileConfig($config->__get(['debug']));
        $this->logger = Cascade::getLogger('db');
    }

    /**
     * Map a DB row into an object.
     *
     * @param array $row DB row.
     *
     * @return mixed
     */
    abstract protected function mapArray(array $row);

    /**
     * Perform a save or delete.
     *
     * @param string $sql        Query string.
     * @param array  $bindParams Array of bind params.
     *
     * @return boolean Success status.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    protected function saveDelete(string $sql, array $bindParams)
    {
        $this->logger->debug("INSERT or DROP SQL...");
        $this->logger->debug("SQL: $sql");
        $this->logger->debug('Bind Params: ' . print_r($bindParams, true));
        $this->db->Execute($sql, $bindParams);
        if ($this->db->affected_rows() !== 0) {
            return true;
        }
        if (empty($this->db->ErrorMsg())) {
            $message = 'Affected rows: 0, no error message returned. There was possibly nothing to update';
            $this->logger->warning($message);
            return true;
        }
        $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
        $this->logger->error($message);
        throw new ApiException($message, 2);
    }

    /**
     * Perform an SQL statement that expects a single row.
     *
     * @param string $sql        Query string.
     * @param array  $bindParams Array of bind params.
     *
     * @return array Mapped row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    protected function fetchRow(string $sql, array $bindParams)
    {
        $this->logger->debug("SELECT single row SQL...");
        $this->logger->debug("SQL: $sql");
        $this->logger->debug('Bind Params: ' . print_r($bindParams, true));
        $row = $this->db->GetRow($sql, $bindParams);
        if ($row === false) {
            $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            $this->logger->error($message);
            throw new ApiException($message, 2);
        }
        return $this->mapArray($row);
    }

    /**
     * Perform an SQL statement that expects multiple rows.
     *
     * @param string $sql        Query string.
     * @param array  $bindParams Array of bind params.
     * @param array  $params     Parameters (optional).
     *                           Example: [ 'filter' =>
     *                           [ 'keyword' => string,
     *                           'column' => string, ]
     *                           'order_by' => string,
     *                           'direction' => string
     *                           "ASC"|"DESC", 'offset'
     *                           => int, 'limit' =>
     *                           int, ] NOTE: * This
     *                           will throw an
     *                           exception if the sql
     *                           already contains a
     *                           WHERE clause and
     *                           should be calculated
     *                           separately in these
     *                           cases. *
     *                           ['filter']['keyword']
     *                           '%' characters in
     *                           keyword not added to
     *                           keyword automatically.
     *
     * @return array
     *   Array of mapped rows.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    protected function fetchRows(string $sql, array $bindParams = [], array $params = [])
    {
        $this->logger->debug("SELECT multiple rows SQL...");
        // Add filter by keyword.
        if (!empty($params['filter'])) {
            $arr = [];
            foreach ($params['filter'] as $filter) {
                if (isset($filter['column']) && isset($filter['keyword'])) {
                    $arr[] = mysqli_real_escape_string($this->db->_connectionID, $filter['column']) . ' LIKE ?';
                    $bindParams[] = $filter['keyword'];
                }
                if (isset($filter['column']) && isset($filter['value'])) {
                    $arr[] = $filter['column'] . ' = ?';
                    $bindParams[] = $filter['value'];
                }
            }
            if (!empty($arr)) {
                if (stripos($sql, ' where ') !== false) {
                    $sql .= ' AND (' . implode(' AND ', $arr) . ')';
                } else {
                    $sql .= ' WHERE (' . implode(' OR ', $arr) . ')';
                }
            }
        }

        // Add order by.
        if (!empty($params['order_by'])) {
            if (stripos($sql, ' order by ') !== false) {
                throw new ApiException('Trying to add order by params on SQL with ORDER BY clause: ' . $sql);
            }
            $orderBy = mysqli_real_escape_string($this->db->_connectionID, $params['order_by']);
            $direction = strtoupper(mysqli_real_escape_string($this->db->_connectionID, $params['direction']));
            $sql .= " ORDER BY $orderBy $direction";
        }

        // Add limit.
        $this->logger->debug("SQL: $sql");
        $this->logger->debug('Bind Params: ' . print_r($bindParams, true));
        if (!empty($params['offset']) || !empty($params['limit'])) {
            if (stripos($sql, ' limit ') !== false) {
                throw new ApiException('Trying to limit params on SQL with LIMIT clause: ' . $sql);
            }
            $recordSet = $this->db->selectLimit(
                $sql,
                (int) $params['limit'],
                (int) $params['offset'],
                $bindParams
            );
        } else {
            $recordSet = $this->db->Execute($sql, $bindParams);
        }

        if (!$recordSet) {
            $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            $this->logger->error($message);
            throw new ApiException($message, 2);
        }

        $entries = [];
        while (!$recordSet->EOF) {
            $entries[] = $this->mapArray($recordSet->fields);
            $recordSet->moveNext();
        }

        return $entries;
    }
}

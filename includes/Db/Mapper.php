<?php

/**
 * Class Mapper.
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

use ADOConnection;
use ApiOpenStudio\Core\ApiException;
use ApiOpenStudio\Core\MonologWrapper;

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
     * @var ADOConnection DB Instance.
     */
    protected ADOConnection $db;

    /**
     * Logger object.
     *
     * @var MonologWrapper
     */
    protected MonologWrapper $logger;

    /**
     * Mapper constructor.
     *
     * @param ADOConnection $dbLayer DB connection object.
     *
     * @param MonologWrapper $logger Logger object.
     *
     */
    public function __construct(ADOConnection $dbLayer, MonologWrapper $logger)
    {
        $this->db = $dbLayer;
        $this->logger = $logger;
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
    protected function saveDelete(string $sql, array $bindParams): bool
    {
        $this->logger->debug('db', 'INSERT or DROP SQL...');
        $this->logger->debug('db', "SQL: $sql");
        $this->logger->debug('db', 'Bind Params: ' . print_r($bindParams, true));
        $this->db->Execute($sql, $bindParams);
        if ($this->db->affected_rows() != 0) {
            return true;
        }
        if (empty($this->db->ErrorMsg())) {
            $message = 'Affected rows: 0, no error message returned. There was possibly nothing to update';
            $this->logger->warning('db', $message);
            return true;
        }
        $logMessage = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
        $this->logger->error('db', $logMessage);
        throw new ApiException('A DB error occurred, please check the logs', 2, -1, 500);
    }

    /**
     * Perform an SQL statement that expects a single row.
     *
     * @param string $sql        Query string.
     * @param array  $bindParams Array of bind params.
     *
     * @return mixed Mapped row.
     *
     * @throws ApiException Return an ApiException on DB error.
     */
    protected function fetchRow(string $sql, array $bindParams)
    {
        $this->logger->debug('db', 'SELECT single row SQL...');
        $this->logger->debug('db', "SQL: $sql");
        $this->logger->debug('db', 'Bind Params: ' . print_r($bindParams, true));
        $row = $this->db->GetRow($sql, $bindParams);
        $this->logger->info('db', print_r($row, true));
        if ($row === false) {
            $logMessage = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            $this->logger->error('db', $logMessage);
            throw new ApiException('A DB error occurred, please check the logs', 2, -1, 500);
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
    protected function fetchRows(string $sql, array $bindParams = [], array $params = []): array
    {
        $this->logger->debug('db', 'SELECT multiple rows SQL...');
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
                $logMessage = "Trying to add order by params on SQL with ORDER BY clause: $sql";
                $this->logger->error('db', $logMessage);
                throw new ApiException('A DB error occurred, please check the logs', 2, -1, 500);
            }
            $orderBy = mysqli_real_escape_string($this->db->_connectionID, $params['order_by']);
            $direction = strtoupper(mysqli_real_escape_string($this->db->_connectionID, $params['direction']));
            $sql .= " ORDER BY $orderBy $direction";
        }

        // Add limit.
        $this->logger->debug('db', "SQL: $sql");
        $this->logger->debug('db', 'Bind Params: ' . print_r($bindParams, true));
        if (!empty($params['offset']) || !empty($params['limit'])) {
            if (stripos($sql, ' limit ') !== false) {
                $logMessage = "Trying to limit params on SQL with LIMIT clause: $sql";
                $this->logger->error('db', $logMessage);
                throw new ApiException('A DB error occurred, please check the logs', 2, -1, 500);
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
            $logMessage = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            $this->logger->error('db', $logMessage);
            throw new ApiException('A DB error occurred, please check the logs', 2, -1, 500);
        }

        $entries = [];
        while (!$recordSet->EOF) {
            $entries[] = $this->mapArray($recordSet->fields);
            $recordSet->moveNext();
        }

        return $entries;
    }
}

<?php

namespace Gaterdata\Db;

use Gaterdata\Core\ApiException;
use Cascade\Cascade;
use ADODB_mysqli;

abstract class Mapper
{
    /**
     * @var ADODB_mysqli DB Instance.
     */
    protected $db;

    /**
     * Mapper constructor.
     *
     * @param ADODB_mysqli $dbLayer
     *   DB connection object.
     */
    public function __construct(ADODB_mysqli $dbLayer)
    {
        $this->db = $dbLayer;
    }

    /**
     * Map a DB row into an object.
     *
     * @param array $row
     *   DB row.
     *
     * @return mixed
     */
    abstract protected function mapArray(array $row);

    /**
     * Perform a save or delete.
     *
     * @param string $sql
     *   Query string.
     * @param array $bindParams
     *   Array of bind params.
     *
     * @return bool
     *   Success status.
     *
     * @throws ApiException
     */
    protected function saveDelete($sql, array $bindParams)
    {
        $this->db->Execute($sql, $bindParams);
        if ($this->db->affected_rows() !== 0) {
            return true;
        }
        if (empty($this->db->ErrorMsg())) {
            $message = 'Affected rows: 0, no error message returned. There was possibly nothing to update';
            throw new ApiException($message, 2);
        }
        $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
        // Cascade::getLogger('gaterdata')->error($message);
        throw new ApiException($message, 2);
    }

    /**
     * Perform an SQL statement that expects a single row.
     *
     * @param string $sql
     *   Query string.
     * @param array $bindParams
     *   Array of bind params.
     *
     * @return mixed
     *   Mapped row.
     *
     * @throws ApiException
     */
    protected function fetchRow($sql, $bindParams)
    {
        $row = $this->db->GetRow($sql, $bindParams);
        if ($row === false) {
            $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            Cascade::getLogger('gaterdata')->error($message);
            throw new ApiException($message, 2);
        }
        return $this->mapArray($row);
    }

    /**
     * Perform an SQL statement that expects multiple rows.
     *
     * @param string $sql
     *   Query string.
     * @param array $bindParams
     *   Array of bind params.
     * @param array $params
     *   parameters (optional)
     *     [
     *       'filter' => [
     *         'keyword' => string,
     *         'column' => string,
     *       ]
     *       'order_by' => string,
     *       'direction' => string "ASC"|"DESC",
     *       'offset' => int,
     *       'limit' => int,
     *     ]
     * NOTE:
     *   * This will throw an exception if the sql already contains a WHERE clause and should be calculated separately
     *     in these cases.
     *   * ['filter']['keyword'] '%' characters in keyword not added to keyword automatically.
     *
     * @return array
     *   Array of mapped rows.
     *
     * @throws ApiException
     */
    protected function fetchRows($sql, $bindParams = [], array $params = [])
    {
        // Add filter by keyword.
        if (!empty($params['filter'])) {
            $arr = [];
            foreach ($params['filter'] as $filter) {
                if (isset($filter['column']) && isset($filter['keyword'])) {
                    $arr[] = mysqli_real_escape_string($this->db->_connectionID, $filter['column']) . ' LIKE ?';
                    $bindParams[] = $filter['keyword'];
                }
            }
            if (!empty($arr)) {
                if (stripos($sql, ' where ') !== false) {
                    $sql .= ' AND (' . implode(' OR ', $arr) . ')';
                } else {
                    $sql .= ' WHERE (' . implode(' OR ', $arr) . ')';
                }
            }
        }

        // Add order by.
        if (!empty($params['order_by'])) {
            if (stripos($sql, 'order by') !== false) {
                throw new ApiException('Trying to add order by params on SQL with ORDER BY clause: ' . $sql);
            }
            $orderBy = mysqli_real_escape_string($this->db->_connectionID, $params['order_by']);
            $direction = strtoupper(mysqli_real_escape_string($this->db->_connectionID, $params['direction']));
            $sql .= " ORDER BY $orderBy $direction";
        }

        // Add limit.
        if (!empty($params['offset']) || !empty($params['limit'])) {
            if (stripos($sql, 'order by') !== false) {
                throw new ApiException('Trying to limit params on SQL with LIMIT clause: ' . $sql);
            }
            $recordSet = $this->db->selectLimit($sql, (integer) $params['limit'],
                (integer) $params['offset'], $bindParams);
        } else {
            $recordSet = $this->db->Execute($sql, $bindParams);
        }

        if (!$recordSet) {
            $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
            Cascade::getLogger('gaterdata')->error($message);
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

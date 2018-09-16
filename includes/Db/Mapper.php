<?php

namespace Datagator\Db;
use Datagator\Core\ApiException;
use Cascade\Cascade;
use ADOConnection;

abstract class Mapper {

  protected $db;

  /**
   * Mapper constructor.
   *
   * @param \ADOConnection $dbLayer
   *   DB connection object.
   */
  public function __construct(ADOConnection $dbLayer) {
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
  protected function saveDelete($sql, array $bindParams) {
    $this->db->Execute($sql, $bindParams);
    if ($this->db->affected_rows() !== 0) {
      return TRUE;
    }
    $message = $this->db->ErrorMsg() . ' (' .  __METHOD__ . ')';
    Cascade::getLogger('gaterdata')->error($message);
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
  protected function fetchRow($sql, $bindParams) {
    $row = $this->db->GetRow($sql, $bindParams);
    if ($row === FALSE) {
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
   *
   * @return array
   *   Array of mapped rows.
   *
   * @throws ApiException
   */
  protected function fetchRows($sql, $bindParams) {
    $recordSet = $this->db->Execute($sql, $bindParams);
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

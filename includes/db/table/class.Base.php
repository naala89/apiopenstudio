<?php

namespace Datagator\DB\Table;

abstract class Base
{
  protected $db;
  protected $tableName = '';
  protected $cols = array();
  protected $pk = '';

  protected $data = array();

  /**
   * @param $db
   */
  public function __constructor($db)
  {
    $this->db = $db;
    foreach ($this->cols as $col) {
      $this->data[$col] = NULL;
    }
  }

  /**
   * @param array $data
   * @return array|bool
   */
  protected function insertRow($data = array())
  {
    $this->data = array_merge($this->data, $data);
    $cols = $vals = $bindParams = array();
    foreach ($this->data as $key => $val) {
      if ($key != $this->pk) {
        $cols[] = "`$key`";
        $vals[] = '?';
        $bindParams[] = $val;
      }
    }

    $sql = 'INSERT INTO ' . $this->tableName . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', $vals) . ')';
    $this->db->StartTrans();
    $result = $this->db->Execute($sql, $bindParams);
    if ($result) {
      $this->db->CommitTrans();
    } else {
      $this->db->RollbackTrans();
      return FALSE;
    }
    $this->data[$this->pk] = $result->Insert_ID();
    return $this->cols;
  }

  /**
   * @param array $data
   * @return mixed
   */
  protected function selectRow($data = array())
  {
    $params = $bindParams = [];
    foreach ($data as $key => $value) {
      $params[] = "$key = ?";
      $bindParams[] = $value;
    }
    $sql = 'SELECT * FROM ' . $this->tableName .' WHERE ' . implode(' AND ', $params);
    $result = $this->db->Execute($sql, $bindParams);
    $this->data = $result->fields;
    return $result;
  }

  /**
   * @param array $data
   * @return array|bool
   */
  protected function updateRow($data = array())
  {
    $this->data = array_merge($this->data, $data);
    $cols = $vals = $bindParams = array();
    $pk = '';
    foreach ($this->data as $key => $val) {
      if ($key != $this->pk) {
        $cols[] = "`$key` = ?";
        $bindParams[] = $val;
      } else {
        $pk = $val;
      }
    }

    $sql = 'UPDATE ' . $this->tableName . ' SET ' . implode(',', $cols) . ' WHERE ' . $this->pk . ' = ' . $pk;
    $this->db->StartTrans();
    $result = $this->db->Execute($sql, $bindParams);
    if ($result) {
      $this->db->CommitTrans();
    } else {
      $this->db->RollbackTrans();
      return FALSE;
    }
    $this->data[$this->pk] = $result->Insert_ID();
    return $this->cols;
  }

  /**
   * @param $data
   * @return mixed
   */
  protected function deleteRow($data)
  {
    $params = $bindParams = [];
    foreach ($data as $key => $value) {
      $params[] = "$key = ?";
      $bindParams[] = $value;
    }
    $sql = 'DELETE FROM ' . $this->tableName .' WHERE ' . implode(' AND ', $params);
    $result = $this->db->Execute($sql, $bindParams);
    $this->data = $result->fields;
    return $result;
  }

  /**
   * @param $data
   * @return array|bool
   */
  public function create($data) {
    return $this->insertRow($data);
  }

  /**
   * @param $data
   * @return mixed
   */
  public function read($data) {
    return $this->selectRow($data);
  }

  /**
   * @param $data
   * @return array|bool
   */
  public function update($data) {
    return $this->updateRow($data);
  }

  /**
   * @param $data
   * @return mixed
   */
  public function delete($data) {
    return $this->deleteRow($data);
  }
}

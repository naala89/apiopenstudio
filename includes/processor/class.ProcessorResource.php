<?php

/**
 * Base class for all resource based processors.
 *
 * METADATA
 * {
 *    "type":"resource",
 *    "meta":{
 *      "id":<mixed>,
 *    }
 *  }
 */

include_once(Config::$dirIncludes . 'processor/vars/class.ProcessorVar.php');

class ProcessorResource extends Processor
{
  public function process()
  {
    Debug::message('ProcessorResource');
    return;
  }

  private function _selectRow($clientId, $method, $resource)
  {
    $sql = 'SELECT * FROM resources WHERE `client` = ? AND `method` = ? AND `resource` = ?';
    $bindParams = array($clientId, $method, $resource);
    $this->request->db->debug = TRUE;
    return $this->request->db->Execute($sql, $bindParams);
  }

  private function _insertRow($clientId, $method, $resource, $meta, $ttl)
  {
    $sql = "INSERT INTO resources (`client`, `method`, `resource`, `meta`, `ttl`) VALUES (?, ?, ?, ?, ?)";
    $bindParams = array($clientId, $method, $resource, $meta, $ttl);
    return $this->request->db->Execute($sql, $bindParams);
  }

  private function _updateRow($clientId, $method, $resource, $meta, $ttl)
  {
    $sql = "UPDATE resources SET `meta` = ?, `ttl` = ? WHERE `client` = ? AND `method` = ? AND `resource` = ?";
    $bindParams = array($meta, $ttl, $clientId, $method, $resource);
    return $this->request->db->Execute($sql, $bindParams);
  }

  private function _deleteRow($clientId, $method, $resource)
  {
    $sql = "DELETE FROM resources WHERE `client` = ? AND `method` = ? AND `resource` = ?";
    $bindParams = array($clientId, $method, $resource);
    return $this->request->db->Execute($sql, $bindParams);
  }

  protected function insertResource($clientId, $method, $resource, $meta, $ttl)
  {
    $recordSet = $this->_selectRow($clientId, $method, $resource);
    $result = FALSE;
    if ($recordSet->RecordCount() < 1) {
      $result = $this->_insertRow($clientId, $method, $resource, $meta, $ttl);
    } else {
      $result = $this->_updateRow($clientId, $method, $resource, $meta, $ttl);
    }
    if (!$result) {
      throw new ApiException('there was an error inserting resource', 2, $this->id, 417);
    }
    return TRUE;
  }

  protected function fetchResource($clientId, $method, $resource)
  {
    $recordSet = $this->_selectRow($clientId, $method, $resource);
    return $recordSet->fields;
  }

  protected function deleteResource($clientId, $method, $resource)
  {
    $result = $this->_deleteRow($clientId, $method, $resource);
    return $result->numOfRows > 0;
  }
}
